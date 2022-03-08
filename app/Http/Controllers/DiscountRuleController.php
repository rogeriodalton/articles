<?php

namespace App\Http\Controllers;

use App\DiscountRule;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class DiscountRuleController extends Controller
{
    protected $DiscountRule;
    protected $Request;
    protected $userId = 0;
    protected $isAdmin = false;
    private $loggedIn = [];

    private $fields = [
        'articles.name as rule_for_article',
        'article_id as rule_for_article_id',
        'discount_rules.id as rule_id',
        'value_min',
        'value_max',
        'units_min',
        'units_max',
        'discount_percent',
        'active'
    ];

    private $rules = [
        'article_id' => 'required|integer|exists:App\Article,id',
        'units_min' => 'required|numeric',
        'units_max' => 'required|numeric',
        'value_min' => 'required|numeric',
        'value_max' => 'required|numeric',
        'discount_percent' => 'required|numeric',
        'active' => 'required|boolean',
    ];

    public function __construct(DiscountRule $discountRule, Request $request)
    {
        $this->DiscountRule = $discountRule;
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
            $this->DiscountRule->join('articles', 'articles.id', 'discount_rules.article_id')
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

        $this->DiscountRule->article_id = $this->Request->article_id;
        $this->DiscountRule->units_min = $this->Request->units_min;
        $this->DiscountRule->units_max = $this->Request->units_max;
        $this->DiscountRule->value_min = $this->Request->value_min;
        $this->DiscountRule->value_max = $this->Request->value_max;
        $this->DiscountRule->discount_percent = $this->Request->discount_percent;
        $this->DiscountRule->active = $this->Request->active;

        $this->DiscountRule->save();
        return $this->msgInclude($this->DiscountRule);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DiscountRule  $discountRule
     * @return \Illuminate\Http\Response
     */
    public function show($id = null)
    {
        if (is_numeric($id))
            return response()->json(
                $this->DiscountRule->join('articles', 'articles.id', 'discount_rules.article_id')
                                   ->select($this->fields)
                                   ->where('discount_rules.id', $id)
                                   ->first()
        );
        else if ($id == 'help')
            return $this->help();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DiscountRule  $discountRule
     * @return \Illuminate\Http\Response
     */
    public function update(int $id = 0)
    {
        $requiredField = count($this->Request->all()) > 0;
        if ($requiredField == 0)
            return $this->msgNoParameterInformed();

        $aDiscountRule = $this->DiscountRule->where('id', $id)->first();

        if (!$aDiscountRule)
            return $this->msgRecordNotFound();

        if (($this->Request->has('article_id')) && ($this->Request->article_id <> 0)) {
            $validator = Validator::make($this->Request->all(),
                ['article_id' => $this->rules['article_id']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aDiscountRule->article_id = $this->Request->article_id;
        }

        if (($this->Request->has('value_min')) && ($this->Request->value_min <> null)) {
            $validator = Validator::make($this->Request->all(),
                ['value_min' => $this->rules['value_min']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aDiscountRule->value_min = $this->Request->value_min;
        }

        if (($this->Request->has('value_max')) && ($this->Request->value_max <> null)) {
            $validator = Validator::make($this->Request->all(),
                ['value_max' => $this->rules['value_max']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aDiscountRule->value_max = $this->Request->value_max;
        }

        if (($this->Request->has('units_min')) && ($this->Request->units_min <> null)) {
            $validator = Validator::make($this->Request->all(),
                ['units_min' => $this->rules['units_min']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aDiscountRule->units_min = $this->Request->units_min;
        }

        if (($this->Request->has('units_max')) && ($this->Request->units_max <> null)) {
            $validator = Validator::make($this->Request->all(),
                ['units_max' => $this->rules['units_max']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aDiscountRule->units_max = $this->Request->units_max;
        }

        if (($this->Request->has('discount_percent')) && ($this->Request->discount_percent <> null)) {
            $validator = Validator::make($this->Request->all(),
                ['discount_percent' => $this->rules['discount_percent']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aDiscountRule->discount_percent = $this->Request->discount_percent;
        }

        if (($this->Request->has('active')) && ($this->Request->active <> null)) {
            $validator = Validator::make($this->Request->all(),
                ['active' => $this->rules['active']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aDiscountRule->active = $this->Request->active;
        }

        $aDiscountRule->save();
        return $this->msgUpdated($aDiscountRule);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DiscountRule  $discountRule
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id = 0)
    {
        $aDiscountRule = $this->DiscountRule->where('discount_rules.id', $id)
                                            ->where('discount_rules.active', 1)
                                            ->first();

        if (!$aDiscountRule)
            return $this->msgRecordNotFound();

        $aDiscountRule->active = 0;
        $aDiscountRule->save();

        return $this->msgRecordDisabled($aDiscountRule);
    }

    function help()
    {
        return response()->json([
            'help -> ' => [
                'OBTER REGISTROS' => 'GET RECORDS',
                '[ GET ]  /discountRules/help'   => 'Informações sobre o point solicitado.',
                '[ GET ]  /discountRules'        => 'Lista todas as configurações de desconto globais',
                '[ GET ]  /discountRules/{ID}'   => 'Localizar configuração de desconto por id da regra',

                'NOVO REGISTRO ' => 'NEW RECORD',

                '[ POST ] /discountRules' => [
                    '{article_id}' => 'id do Artigo para o qual essa regra se aplica',
                    '{units_min}' => 'Quantidade mínima, informe -1 quando não aplicado',
                    '{units_max}' => 'Quantidade máxima, informe -1 quando não aplicado',
                    '{value_min}' => 'Valor mínimo, informe -1 quando não aplicado',
                    '{value_max}' => 'Valor máximo, informe -1 quando não aplicado',
                    '{discount_percent}' => 'Percentual de desconto a ser aplicado',
                    '{active}' => '(1)Ativo, (0)Inativo',
                ],

                'ALTERAR REGISTRO ' => 'CHANGE RECORD',

                '[ PUT ] /discountRules/{id}' => [
                    'article_id' => 'id do Artigo para o qual essa regra se aplica',
                    'units_min' => 'Quantidade mínima, informe -1 quando não aplicado',
                    'units_max' => 'Quantidade máxima, informe -1 quando não aplicado',
                    'value_min' => 'Valor mínimo, informe -1 quando não aplicado',
                    'value_max' => 'Valor máximo, informe -1 quando não aplicado',
                    'discount_percent' => 'Percentual de desconto a ser aplicado',
                    'active' => '(1)Ativo, (0)Inativo',
                ],
                'DESABILITAR REGRA ' => 'DISABLE RULE',

                '[ DELETE ] /discountRules/{id}' => 'Desabilita regra de desconto',
            ]
        ]);
    }

}
