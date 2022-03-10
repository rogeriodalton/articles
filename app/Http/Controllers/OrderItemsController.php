<?php

namespace App\Http\Controllers;

use App\Article;
use App\OrderItems;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderItemsController extends Controller
{
    protected $Article;
    protected $OrderItems;
    protected $Request;
    protected $userId = 0;
    protected $isAdmin = false;
    private $loggedIn = [];

    private $fields = [
        'id',
        'order_id',
        'article_id',
        'articles.name as article_name',
        'units',
        'unit_value',
        'amount_liquid',
        'amount_discount',
        'amount_add',
        'amount_gross',
    ];

    private $rules = [
        'order_id' => 'required|integer|exists:App\Order,id',
        'article_id' => 'required|integer|exists:App\Article,id',
        'units' => 'required|numeric',
    ];

    public function __construct(OrderItems $orderItems, Article $article, Request $request)
    {
        $this->OrderItems = $orderItems;
        $this->Article = $article;
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
            $this->OrderItems->join('orders','orders.id','order_items.order_id')
                             ->join('articles','articles.id','order_items.article_id')
                             ->select($this->fields)
                             ->get()
        );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validator = Validator::make($this->Request->all(), $this->rules);
        if ($validator->fails())
            return $this->msgMissingValidator($validator);

        $aArticle = $this->Article->where('id', $this->Request->article_id)->first();

        $this->OrderItems->order_id = $this->Request->order_id;
        $this->OrderItems->article_id = $this->Request->article_id;
        $this->OrderItems->units = $this->Request->units;
        $this->OrderItems->amount_discount = $this->Request->amount_discount;
        $this->OrderItems->amount_add = $this->Request->amount_add;
        $this->OrderItems->unit_value = $aArticle->amount;
        $this->OrderItems->amount_discount = 0;

        if (($this->Request->has('amount_discount')) && ($this->Request->amount_discount <> 0))
            $this->OrderItems->amount_discount = $this->Request->amount_discount;

        $this->OrderItems->amount_add = 0;
        if ($this->Request->has('amount_add') && ($this->Request->amount_add <> 0))
            $this->OrderItems->amount_add = $this->Request->amount_add;

        $this->OrderItems->amount_gross = ($aArticle->amount * $this->Request->units) + $this->OrderItems->amount_add - $this->OrderItems->amount_discount;

        $this->OrderItems->save();
        return $this->msgInclude($this->Order);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\OrderItems  $orderItems
     * @return \Illuminate\Http\Response
     */
    public function show($id = null)
    {
        if ($id == 'help')
            return $this->help();


        return response()->json(
                $this->orderItems->join('articles','articles.id','order_items.article_id')
                                 ->select($this->fields)
                                 ->where('order_items.id',$id)
                                 ->first()
        );
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

        $aOrderItems = $this->OrderItems->where('id', $id)->first();

        if (!$aOrderItems)
            return $this->msgRecordNotFound();

        //-----------------------------------------------------------------------------------------
        if ($this->Request->has('article_id')) {
            $validator = Validator::make($this->Request->all(),
                ['article_id' => $this->rules['article_id']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aOrderItems->article_id = $this->Request->article_id;
        }

        //-----------------------------------------------------------------------------------------
        if ($this->Request->has('units')) {
            $validator = Validator::make($this->Request->all(),
                ['units' => $this->rules['units']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aOrderItems->units = $this->Request->units;
        }

        //-----------------------------------------------------------------------------------------
        if ($this->Request->has('amount_discount')) {
            $validator = Validator::make($this->Request->all(),
                ['amount_discount' => $this->rules['amount_discount']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aOrderItems->amount_discount = $this->Request->amount_discount;
        }

        //-----------------------------------------------------------------------------------------
        if ($this->Request->has('amount_add')) {
            $validator = Validator::make($this->Request->all(),
                ['amount_add' => $this->rules['amount_add']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aOrderItems->amount_add = $this->Request->amount_add;
        }

        $aOrderItems->save();

        //amount_gross update
        //-----------------------------------------------------------------------------------------
        $aArticle = $this->Article->where('id', $aOrderItems->article_id)->first();
        $aOrderItems->amount_gross = ($aArticle->amount * $aOrderItems->units) + $aOrderItems->amount_add - $aOrderItems->amount_discount;
        //-----------------------------------------------------------------------------------------

        $aOrderItems->save();
        return $this->msgUpdated($aOrderItems);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OrderItems  $orderItems
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id = 0)
    {
        $aOrderItems = $this->OrderItems->where('id', $id)->first();

        if (!$aOrderItems)
            return $this->msgRecordNotFound();

        $aOrderItems->delete();
        return $this->msgRecordDeleted($aOrderItems);

    }

    private function help()
    {
        return response()->json([
            'help -> ' => [
                'OBTER REGISTROS' => 'GET RECORDS',
                '[ GET ]  /api/orderItems/help'   => 'Informações sobre o point solicitado.',
                '[ GET ]  /api/orderItems'        => 'Lista todos itens vendidos',
                '[ GET ]  /api/orderItems/{ID}'   => 'item Vendido pelo id',

                'NOVO REGISTRO ' => 'NEW RECORD',

                '[ POST ] /api/orderItems' => [
                    '{order_id}' => 'id da Venda para associar aos ítens de venda.',
                    '{article_id}' => 'id do artigo que está sendo adquirido',
                    '{units}' => 'Quantidade deste.',
                ],

                'ALTERAR REGISTRO ' => 'CHANGE RECORD',

                '[ PUT ] /api/orderItems/{id}' => [
                    'order_id' => 'id da Venda para associar aos ítens de venda.',
                    'article_id' => 'id do artigo que está sendo adquirido',
                    'units' => 'Quantidade deste.',
                ],

                'EXCLUIR REGISTRO ' => 'DELETE RECORD',
                '[ DELETE ] /api/orderItems/{id}' => 'Exclui o registro do cliente',
            ]
        ]);
    }
}
