<?php

namespace App\Http\Controllers;

use App\OrderItems;
use App\Order;
use App\DiscountRule;
use App\DiscountOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FinalizeOrderController extends Controller
{
    protected $Order;
    protected $OrderItems;
    protected $DiscountRule;
    protected $DiscountOrder;
    protected $Request;
    protected $userId = 0;
    protected $isAdmin = false;
    private $loggedIn = [];

    private $fields = [
        'id',
        'client_id',
        'code',
        'date',
        'amount_liquid',
        'amount_discount',
        'amount_add',
        'amount_gross',
    ];

    private $rules = [
        'order_id' => 'required|integer|exists:App\Order,id',
    ];

    public function __construct(Order $order, OrderItems $orderItems, DiscountRule $discountRule, DiscountOrders $discountOrder, Request $request)
    {
        $this->Order = $order;
        $this->OrderItems = $orderItems;
        $this->DiscountRule = $discountRule;
        $this->DiscountOrders = $discountOrder;
        $this->Request = $request;
        $this->loggedIn = auth()->user();
        $this->isAdmin = false;
        $this->userId = 0;

        if ($this->loggedIn) {
            $this->isAdmin = $this->getPermission($this->loggedIn->email, 1) ;
            $this->userId = $this->getUserId($this->loggedIn->email);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(
            $this->Order->select($this->fields)
                        ->orderBy('id', 'desc')
                        ->get()
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id = null)
    {
        if ($id == 'help')
            return $this->help();
    }

    /**
     * Execute a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function execute()
    {
        $validator = Validator::make($this->Request->all(), $this->rules);
        if ($validator->fails())
            return $this->msgMissingValidator($validator);


        $aOrder = $this->Order->where('id', $this->Request->order_id)->first();
        $aItems = $this->OrderItems->where('order_id', $this->Request->order_id)->get();
        $prodTotal = $aItems->count();
        $record = 0;

        //reset Order
        $aOrder->amount_discount = $aOrder->first_amount_discount;
        $aOrder->amount_add      = $aOrder->first_amount_add;
        $aOrder->amount_liquid   = 0;
        $aOrder->amount_gross    = 0;
        $aOrder->save();

        //verify order items
        do {
            $aOrderItem = $this->OrderItems->where('id', $aItems[$record]->id)->first();

            if ($aOrderItem) {
                $this->checkPromotionItem($aOrderItem);
                $aOrder->amount_liquid   += $aOrderItem->amount_liquid;
                $aOrder->amount_add      += $aOrderItem->amount_add;
                $aOrder->amount_discount += $aOrderItem->amount_discount;
                $aOrder->amount_gross    += $aOrderItem->amount_gross;
                $aOrder->save();
            }

            $record++;

        } while ($record < $prodTotal);

        //verify Order final promotion
        $this->checkFinalPromotion($aOrder);

        return response()->json([
            'message:' => "Operation completed successfully!",
            'records:' => "{$record} checked."
        ]);
    }


    /**
     * Apply discount rule based on the product item that was selected from the sale if you have any rule compatible with it
     *
     *
     */
    private function checkPromotionItem(&$aOrderItem)
    {
        //------------------------------------------------------------------------------------------------------------
        //no max unit limit
        $aDiscountRule = $this->DiscountRule->where('article_id', $aOrderItem->article_id)
                                            ->where('units_min', '<=' , $aOrderItem->units)
                                            ->where('units_max', -1)
                                            ->first();
        if ($aDiscountRule) {
            $aOrderItem->amount_discount = ($aOrderItem->amount_liquid * $aDiscountRule->discount_percent) / 100;
            $aOrderItem->save();
        }
        //------------------------------------------------------------------------------------------------------------
        //unit between min and max
        $aDiscountRule = $this->DiscountRule->where('article_id', $aOrderItem->article_id)
                                            ->where('units_min', '<=', $aOrderItem->units)
                                            ->where('units_max', '>=', $aOrderItem->units)
                                            ->first();
        if ($aDiscountRule) {
            $aOrderItem->amount_discount = ($aOrderItem->amount_liquid * $aDiscountRule->discount_percent) / 100;
            $aOrderItem->amount_liquid = ($aOrderItem->amount_liquid - $aOrderItem->amount_discount);
            $aOrderItem->save();

            ($aOrderItem->amount_liquid + $aOrderItem->amount_add) - $aOrderItem->amount_discount
            $aOrderItem->save();
        }

        //------------------------------------------------------------------------------------------------------------
        //no max amount limit
        $aDiscountRule = $this->DiscountRule->where('article_id', $aOrderItem->article_id)
                                            ->where('value_min', '<=', $aOrderItem->amount_liquid)
                                            ->where('value_max', -1)
                                            ->first();
        if ($aDiscountRule) {
            $aOrderItem->amount_discount = ($aOrderItem->amount_liquid * $aDiscountRule->discount_percent) / 100;
            $aOrderItem->save();
        }
        //------------------------------------------------------------------------------------------------------------
        //amount between min and max
        $aDiscountRule = $this->DiscountRule->where('article_id', $aOrderItem->article_id)
                                            ->where('value_min', '<=', $aOrderItem->amount_liquid)
                                            ->where('value_max', '>=', $aOrderItem->amount_liquid)
                                            ->first();
        if ($aDiscountRule) {
            $aOrderItem->amount_discount = ($aOrderItem->amount_liquid * $aDiscountRule->discount_percent) / 100;
            $aOrderItem->save();
        }
        //------------------------------------------------------------------------------------------------------------
    }

     /**
     * Apply final discount rule based on final gross sale value
     *
     */
    private function checkFinalPromotion(&$aOrder)
    {
        //------------------------------------------------------------------------------------------------------------
        //no max amount limit
        $aDiscountOrders = $this->DiscountOrders->where('value_min', '<=', $aOrder->amount_gross)
                                                ->where('value_max', -1)
                                                ->first();
        if ($aDiscountOrders) {
            $aOrder->amount_discount = ($aOrder->amount_gross * $aDiscountOrders->discount_percent) / 100;
            $aOrder->save();
        }
        //------------------------------------------------------------------------------------------------------------
        //amount between min and max
        $aDiscountOrders = $this->DiscountOrders->where('value_min', '<=', $aOrder->amount_gross)
                                                ->where('value_max', '>=', $aOrder->amount_gross)
                                                ->first();
        if ($aDiscountOrders) {
            $aOrder->amount_discount = ($aOrder->amount_gross * $aDiscountOrders->discount_percent) / 100;
            $aOrder->save();
        }
        //------------------------------------------------------------------------------------------------------------
    }

    function help()
    {
        return response()->json([
            'help -> ' => [
                'OBTER REGISTROS' => 'GET RECORDS',
                '[ GET ]  /api/finalizeOrder/help' => 'Informações sobre o point solicitado.',
                '[ GET ]  /api/finalizeOrder'      => 'Lista todas as configurações de desconto globais',

                'Executar ' => 'Execute',

                '[ POST ] /api/finalizeOrder' => [
                    '{order_id}' => 'id do Artigo para o qual essa regra se aplica',
                ],

            ]
        ]);
    }


}
