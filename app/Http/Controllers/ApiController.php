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
                'ARTICLE' => 'Ecommerce experimental.',
                'obs.:' => 'Cada entpoint tem seu help  ex.: /api/client/help'
            ],
            [
                'clients' => '/api/client/help',
                'articles' => '/api/article/help',
                'discountOrder' => '/api/discountOrder/help',
                'discountRules' => '/api/discountRules/help',
                'orders' => '/api/orders/help',
                'orderItems' => '/api/orderItems/help',
                'finalizeOrder' => '/api/finalizeOrder/help',
            ],
            [
                'Jwt Autentication' => 'Para todos os métodos utilize o verbo [ POST ]',
                'login' => '/api/login',
                'me' => '/api/me',
                'permission' => '/api/permission',
                'allPermissions' => '/api/allPermissions',
                'logout' => '/api/logout',
            ],
            [
                'Processo de conclusão da venda:'=> [
                    '1. Criação de pedido' => '/api/orders/orders',
                    '2. Adicionar itens ao pedido' => '/api/orderItems',
                    '3. Finalizar pedido' => '/api/finalizeOrder',
                ],

            ]
        ]);
    }
}
