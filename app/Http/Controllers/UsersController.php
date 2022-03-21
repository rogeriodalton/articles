<?php

namespace App\Http\Controllers;

use App\User;
use App\UserGroups;
use App\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Hash} ;

class UsersController extends Controller
{
    protected $User;
    protected $UserGroups;
    protected $Order;
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
        'name' => 'required|unique:users|max:255',
        'email' => 'required|unique:users|max:255',
        'password' => 'required|string',
    ];

    public function __construct(User $user, UserGroups $userGroups, Order $order, Request $request)
    {
        $this->User = $user;
        $this->UserGroups = $userGroups;
        $this->Order = $order;
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
            $this->User->all()
        );
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

        $this->User->name = $this->Request->name;
        $this->User->fname = $this->Request->name;
        $this->User->email = $this->Request->email;
        $this->User->password = Hash::make($this->Request->password);
        $this->User->active = 1;
        $this->User->save();

        $this->UserGroups->user_id = $this->User->id;
        $this->UserGroups->group_id = 1;
        $this->UserGroups->active = 1;
        $this->UserGroups->save();

        return $this->msgInclude($this->User);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id = null)
    {
        if (is_numeric($id))
            return response()->json(
                $this->User->select($this->fields)
                             ->where('id', $id)
                             ->first()
            );
        else if ($id == 'help')
            return $this->help();
        else
        {
            $fName = phonetics($id);
            return response()->json(
                $this->User->select($this->fields)
                           ->where('fname', 'like' ,"%{$fName}%" )
                           ->get()
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(int $id = 0)
    {
        $requiredField = count($this->Request->all()) > 0;
        if ($requiredField == 0)
            return $this->msgNoParameterInformed();

        $aUser = $this->Client->where('id', $id)->first();

        if (!$aUser)
            return $this->msgRecordNotFound();

        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('name')) && ($this->Request->name <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['name' => $this->rules['name']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aUser->name = $this->Request->name;
            $aUser->fname = $this->Request->name;
        }

        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('email')) && ($this->Request->email <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['email' => $this->rules['email']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aUser->email = $this->Request->email;
        }

        //-----------------------------------------------------------------------------------------
        if (($this->Request->has('password')) && ($this->Request->password <> '')) {
            $validator = Validator::make($this->Request->all(),
                ['password' => $this->rules['password']]);

            if ($validator->fails())
                return $this->msgInvalidValue($validator);

            $aUser->password = Hash::make($this->Request->password);
        }

        $aUser->save();
        return $this->msgUpdated($aUser);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id = 0)
    {
        $aUser = $this->User->where('id', $id)->first();
        if (!$aUser)
            return $this->msgRecordNotFound();

        $aOrder = $this->Order->where('user_id', $id)->first();
        if ($aOrder)
            return $this->msgRecordExceptionNotDelete($aUser);

        do {
            $aUser_group = $this->UserGroups->where('user_id', $id)->first();
            if ($aUser_group)
                $aUser_group->delete();
        } while ($aUser_group);

        $error = null;
        try
            {$aUser->delete();}
        catch (Exception $e)
            {$error = mb_convert_encoding($e->getMessage(), "UTF-8", "auto");}
        finally {
            if (!$error)
                return $this->msgRecordDeleted($aUser);
            else
                return $this->msgRecordExceptionNotDelete($aUser);
        }
    }
}
