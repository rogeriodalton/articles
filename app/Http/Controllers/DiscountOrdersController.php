<?php

namespace App\Http\Controllers;

use App\DiscountOrders;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountOrdersController extends Controller
{
    protected $DiscountOrders;
    protected $Request;
    protected $userId = 0;
    protected $isAdmin = false;
    private $loggedIn = [];

    private $fields = [
        'id',
        'name',
        'min_value',
        'max_value',
        'discount_percent',
        'active'
    ];

    private $rules = [
        'name' => 'required|string',
        'min_value' => 'required|numeric',
        'max_value' => 'required|numeric',
        'discount_percent' => 'required|numeric',
        'active' => 'required|boolean',
    ];

    public function __construct(DiscountOrders $discountOrders, Request $request)
    {
        $this->DiscountOrders = $discountOrders;
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
        return response()->json($this->DiscountOrders->all());
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

        $this->DiscountOrders->name = $this->Request->name;
        $this->DiscountOrders->min_value = $this->Request->min_value;
        $this->DiscountOrders->max_value = $this->Request->max_value;
        $this->DiscountOrders->discount_percent = $this->Request->discount_percent;
        $this->DiscountOrders->active = $this->Request->active;

        $this->DiscountOrders->save();
        return $this->msgInclude($this->DiscountOrders);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id = null)
    {
        if (is_numeric($id))
            return response()->json(
                $this->DiscountOrders->select($this->fields)
                                     ->where('id', $id)
                                     ->first()
            );
        else if ($id == 'help')
            return $this->help();
        else
            return response()->json(
                $this->DiscountOrders->select($this->fields)
                                     ->where('name', 'like' ,"%{$id}%" )
                                     ->get()
                );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(int $id = 0)
    {
        $requiredField = count($this->Request->all()) > 0;
        if ($requiredField == 0)
            return $this->msgNoParameterInformed();

        $aDiscountOrders = $this->DiscountOrders->where('id', $id)->first();

        if (!$aDiscountOrders)
            return $this->msgRecordNotFound();

        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('name')) && ($this->Request->name <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['name' => $this->rules['name']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aDiscountOrders->name = $this->Request->name;
        }

        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('min_value')) && ($this->Request->min_value <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['min_value' => $this->rules['min_value']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aDiscountOrders->min_value = $this->Request->min_value;
        }

        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('max_value')) && ($this->Request->max_value <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['max_value' => $this->rules['max_value']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aDiscountOrders->max_value = $this->Request->max_value;
        }

        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('discount_percent')) && ($this->Request->discount_percent <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['discount_percent' => $this->rules['discount_percent']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aDiscountOrders->discount_percent = $this->Request->discount_percent;
        }

        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('active')) && ($this->Request->active <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['active' => $this->rules['active']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aDiscountOrders->active = $this->Request->active;
        }

        $aDiscountOrders->save();
        return $this->msgUpdated($aDiscountOrders);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DiscountOrders  $discountOrders
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id = 0)
    {
        $aDiscountOrders = $this->DiscountOrders->where('id', $id)
                                                ->where('active', 1)
                                                ->first();
        if (!$aDiscountOrders)
            return $this->msgRecordNotFound();

        $aDiscountOrders->active = 0;
        $aDiscountOrders->save();

        return $this->msgRecordDisabled($aDiscountOrders);
    }

    function help()
    {
        return response()->json([
            'help -> ' => [
                'OBTER REGISTROS' => 'GET RECORDS',
                '[ GET ]  /api/discountOrder/help'   => 'Informações sobre o point solicitado.',
                '[ GET ]  /api/discountOrder'        => 'Lista todas as configurações de desconto globais',
                '[ GET ]  /api/discountOrder/{ID}'   => 'Localizar configuração de desconto por id ou nome',

                'NOVO REGISTRO ' => 'NEW RECORD',

                '[ POST ] /api/discountOrder' => [
                    '{name}' => 'Nome da Regra',
                    '{min_value}' => 'valor mínimo',
                    '{max_value}' => 'Valor máximo, obs.: Quando não existir informar -1',
                    '{discount_percent}' => 'Percentual de desconto a ser aplicado',
                    '{active}' => '(1)Ativo, (0)Inativo',
                ],

                'ALTERAR REGISTRO ' => 'CHANGE RECORD',

                '[ PUT ] /api/discountOrder/{id}' => [
                    'name' => 'Nome da Regra',
                    'min_value' => 'valor mínimo',
                    'max_value' => 'Valor máximo, obs.: Quando não existir informar -1',
                    'discount_percent' => 'Percentual de desconto a ser aplicado',
                    'active' => '(1)Ativo, (0)Inativo',                ],

                'DESABILITAR REGRA ' => 'DISABLE RULE',
                '[ DELETE ] /api/discountOrder/{id}' => 'Desabilita regra de desconto',
            ]

        ]);
    }

}
