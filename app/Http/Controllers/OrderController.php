<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderItems;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected $Order;
    protected $OrderItems;
    protected $Request;
    protected $userId = 0;
    protected $isAdmin = false;
    private $loggedIn = [];

    private $fields = [
        'orders.id as OrderId',
        'code as OrderCode',
        'date as OrderDate',
        'amount_liquid as TotalAmountWihtoutDiscount',
        'amount_gross as TotalAmountWithDiscount',
        'amount_add',
        'amount_discount',
        'client_id as clientId',
        'clients.name as ClientName',
        'user_id as salesmanId',
        'users.name as salesman',
    ];

    private $updateRules = [
        'client_id' => 'required|integer|exists:App\Client,id',
        'first_amount_discount'=> 'required|numeric',
        'first_amount_add' => 'required|numeric',
    ];

    private $rules = [
        'client_id' => 'required|integer|exists:App\Client,id',
    ];

    public function __construct(Order $order, OrderItems $orderItems, Request $request)
    {
        $this->Order = $order;
        $this->OrderItems = $orderItems;
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
        if ($this->isAdmin)
            return response()->json([
                $this->Order->join('clients','clients.id','orders.client_id')
                            ->join('users','users.id','orders.user_id')
                            ->select($this->fields)
                            ->orderBy('orders.id','desc')
                            ->get()
            ]);
        else
            return response()->json([
                $this->Order->join('clients','clients.id','orders.client_id')
                            ->join('users','users.id','orders.user_id')
                            ->select($this->fields)
                            ->where('orders.user_id', $this->userId)
                            ->orderBy('orders.id','desc')
                            ->get()
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validator = Validator::make($this->Request->all(), $this->rules);
        if ($validator->fails())
            return $this->msgMissingValidator($validator);

        $this->Order->client_id = $this->Request->client_id;
        $this->Order->user_id = $this->userId;
        $this->Order->date = 'today date';
        $this->Order->save();

        $this->Order->code = 'generated code';
        $this->Order->save();
        return $this->msgInclude($this->Order);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(int $id = 0)
    {
        $aOrder = $this->Order->get();

        $order_items = $aOrder->orderItems()->get();
        return $order_items;

        if ($this->isAdmin)
            return response()->json([
                $this->Order->join('clients','clients.id','orders.client_id')
                            ->join('users','users.id','orders.user_id')
                            ->select($this->fields)
                            ->where('orders.id', $id)
                            ->first()
            ]);
        else
            return response()->json([
                $this->Order->join('clients','clients.id','orders.client_id')
                            ->join('users','users.id','orders.user_id')
                            ->select($this->fields)
                            ->where('orders.user_id', $this->userId)
                            ->where('orders.id', $id)
                            ->first()
            ]);



    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(int $id = 0)
    {
        $requiredField = count($this->Request->all()) > 0;
        if ($requiredField == 0)
            return $this->msgNoParameterInformed();

        $aOrder = $this->Order->where('id', $id)->first();

        if (!$aOrder)
            return $this->msgRecordNotFound();

        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('first_amount_add'))) {
            $validator = Validator::make($this->Request->all(),
                ['first_amount_add' => $this->updateRules['first_amount_add']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aOrder->first_amount_add = $this->Request->first_amount_add;
        }

        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('first_amount_discount'))) {
            $validator = Validator::make($this->Request->all(),
                ['first_amount_discount' => $this->updateRules['first_amount_discount']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aOrder->first_amount_discount = $this->Request->first_amount_discount;
        }

        $aOrder->save();
        return $this->msgUpdated($aOrder);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id = 0)
    {
        $error = null;
        $aOrder = $this->Order->where('id', $id)->first();

        if (!$aOrder)
            return $this->msgRecordNotFound();

        try
            {$aOrder->delete();}
        catch (Exception $e)
            {$error = mb_convert_encoding($e->getMessage(), "UTF-8", "auto");}
        finally {
            if (!$error)
                return $this->msgRecordDeleted($aOrder);
            else
                return $this->msgRecordExceptionNotDelete($aOrder);
        }
    }

    private function help()
    {
        return response()->json([
            'help -> ' => [
                'OBTER REGISTROS' => 'GET RECORDS',
                '[ GET ]  /api/order/help'   => 'Informa????es sobre o point solicitado.',
                '[ GET ]  /api/order'        => 'Lista todos os clientes',
                '[ GET ]  /api/order/{ID}'   => 'Localizar cliente pelo id ou nome',

                'NOVO REGISTRO' => 'NEW RECORD',

                '[ POST ] /api/order' => [
                    '{first_amount_discount}' => 'Primeiro valor de desconto informado',
                    '{first_amount_add}' => 'Primeiro valor de acr??scimo informado',
                    '{client_id}' => 'id do cliente',
                ],

                'ALTERAR REGISTRO' => 'CHANGE RECORD',

                '[ PUT ] /api/order/{id}' => [
                    '{name}' => 'Nome do cliente',
                    '{email}' => 'Email do cliente',
                ],

                'EXCLUIR REGISTRO' => 'DELETE RECORD',
                '[ DELETE ] /api/order/{id}' => 'Exclui o registro do cliente',
            ]
        ]);
    }
}
