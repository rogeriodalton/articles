<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            [
                'ARTICLE' => 'Ecommerce experimental.'
            ],
            [
                'clients' => '/api/client',
                'articles' => '/api/article',
                'discountOrder' => '/api/discountOrder',
                'discountRules' => '/api/discountRules',
            ],
        ]);
    }
}
