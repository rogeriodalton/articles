<?php

namespace App\Http\Controllers;

use App\Article;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    protected $Article;
    protected $Request;
    protected $userId = 0;
    protected $isAdmin = false;
    private $loggedIn = [];

    private $fields = [
        'id',
        'code',
        'name',
        'amount',
    ];

    private $rules = [
        'name' => 'required|string',
        'code' => 'required|string',
        'price' => 'required|numeric',
        'quantity' => 'required|numeric',

    ];

    public function __construct(Article $article, Request $request)
    {
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
        return response()->json($this->Article->all());
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

        $this->Article->code = $this->Request->code;
        $this->Article->name = $this->Request->name;
        $this->Article->fname = $this->Request->name;
        $this->Article->price = $this->Request->price;
        $this->Article->quantity = $this->Request->quantity;

        $this->Article->save();
        return $this->msgInclude($this->Article);
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
                $this->Article->select($this->fields)
                             ->where('id', $id)
                             ->first()
            );
        else if ($id == 'help')
            return $this->help();
        else
        {
            $fName = phonetics($id);
            return response()->json(
                $this->Article->select($this->fields)
                              ->where('fname', 'like' ,"%{$fName}%" )
                              ->get()
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(int $id = 0)
    {
        $requiredField = count($this->Request->all()) > 0;
        if ($requiredField == 0)
            return $this->msgNoParameterInformed();

        $aArticle = $this->Article->where('id', $id)->first();

        if (!$aArticle)
            return $this->msgRecordNotFound();

        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('name')) && ($this->Request->name <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['name' => $this->rules['name']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aArticle->name = $this->Request->name;
            $aArticle->fname = $this->Request->name;
        }

        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('code')) && ($this->Request->code <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['code' => $this->rules['code']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aArticle->code = $this->Request->code;
        }

        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('price')) && ($this->Request->price <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['price' => $this->rules['price']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aArticle->price = $this->Request->price;
        }
        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('quantity')) && ($this->Request->quantity <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['quantity' => $this->rules['quantity']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aArticle->quantity = $this->Request->quantity;
        }


        $aArticle->save();
        return $this->msgUpdated($aArticle);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id = 0)
    {
        $error = null;
        $aArticle = $this->Article->where('id', $id)->first();

        if (!$aArticle)
            return $this->msgRecordNotFound();

        try
            {$aArticle->delete();}
        catch (Exception $e)
            {$error = mb_convert_encoding($e->getMessage(), "UTF-8", "auto");}
        finally {
            if (!$error)
                return $this->msgRecordDeleted($aArticle);
            else
                return $this->msgRecordExceptionNotDelete($aArticle);
        }
    }

    function help()
    {
        return response()->json([
            'help -> ' => [
                'OBTER REGISTROS' => 'GET RECORDS',
                '[ GET ]  /api/article/help'   => 'Informações sobre o point solicitado.',
                '[ GET ]  /api/article'        => 'Lista todos os artigos',
                '[ GET ]  /api/article/{ID}'   => 'Localizar artigo pelo id ou nome',

                'NOVO REGISTRO ' => 'NEW RECORD',

                '[ POST ] /api/article' => [
                    '{code}' => 'Código do artigo',
                    '{name}' => 'Nome do artigo',
                    '{price}' => 'Valor do artigo',
                    '{quantity}' => 'quantidade',

                ],

                'ALTERAR REGISTRO ' => 'CHANGE RECORD',

                '[ PUT ] /api/article/{id}' => [
                    'code' => 'Código do artigo',
                    'name' => 'Nome do artigo',
                    'price' => 'Valor do artigo',
                    'quantity' => 'quantidade',
                ],

                'EXCLUIR REGISTRO ' => 'DELETE RECORD',
                '[ DELETE ] /api/article/{id}' => 'Exclui o registro do artigo',
            ]

        ]);
    }
}
