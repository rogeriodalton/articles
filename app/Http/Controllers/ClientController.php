<?php

namespace App\Http\Controllers;

use App\Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    protected $Client;
    protected $Request;
    protected $userId = 0;
    protected $isAdmin = false;
    private $loggedIn = [];

    private $fields = [
        'id',
        'name',
        'email',
    ];

    private $rules = [
        'name' => 'required|string',
        'email' => 'required|email',
    ];

    public function __construct(Client $client, Request $request)
    {
        $this->Client = $client;
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
        return response()->json($this->Client->all());
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

        $this->Client->name = $this->Request->name;
        $this->Client->fname = $this->Request->name;
        $this->Client->email = $this->Request->email;
        $this->Client->save();
        return $this->msgInclude($this->Client);
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
                $this->Client->select($this->fields)
                             ->where('id', $id)
                             ->first()
            );
        else if ($id == 'help')
            return $this->help();
        else
        {
            $fName = phonetics($id);
            return response()->json(
                $this->Client->select($this->fields)
                             ->where('fname', 'like' ,"%{$fName}%" )
                             ->get()
            );
        }
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

        $aClient = $this->Client->where('id', $id)->first();

        if (!$aClient)
            return $this->msgRecordNotFound();

        if (($this->Request->has('name')) && ($this->Request->name <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['name' => $this->rules['name']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aClient->name = $this->Request->name;
            $aClient->fname = $this->Request->name;
        }

        if (($this->Request->has('email')) && ($this->Request->name <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['email' => $this->rules['email']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aClient->email = $this->Request->email;
        }

        $aClient->save();
        return $this->msgUpdated($aClient);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id = 0)
    {
        $error = null;
        $aClient = $this->Client->where('id', $id)->first();

        if (!$aClient)
            return $this->msgRecordNotFound();

        try
            {$aClient->delete();}
        catch (Exception $e)
            {$error = mb_convert_encoding($e->getMessage(), "UTF-8", "auto");}
        finally {
            if (!$error)
                return $this->msgRecordDeleted($aClient);
            else
                return $this->msgRecordExceptionNotDelete($aClient);
        }
    }

    private function help()
    {
        return response()->json([
            'help -> ' => [
                'OBTER REGISTROS' => 'GET RECORDS',
                '[ GET ]  /client/help'   => 'Informações sobre o point solicitado.',
                '[ GET ]  /client'        => 'Lista todos os clientes',
                '[ GET ]  /client/{ID}'   => 'Localizar cliente pelo id ou nome',

                'NOVO REGISTRO ' => 'NEW RECORD',

                '[ POST ] /client' => [
                    '{name}' => 'Nome do cliente',
                    '{email}' => 'Email do cliente',
                ],

                'ALTERAR REGISTRO ' => 'CHANGE RECORD',

                '[ PUT ] /client/{id}' => [
                    '{name}' => 'Nome do cliente',
                    '{email}' => 'Email do cliente',
                ], //

                'EXCLUIR REGISTRO ' => 'DELETE RECORD',
                '[ DELETE ] /client/{id}' => 'Exclui o registro do cliente',
            ]
        ]);
    }

}
