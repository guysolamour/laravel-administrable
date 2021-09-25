<?php

namespace App\Http\Controllers\Front\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Command;
use Illuminate\Http\Request;

class CommandController extends Controller
{
    public function index(Request $request)
    {
        /**
         * @var \App\Models\User
         */
        $client = $request->user();


        return view('front.shop.commands.index', compact('client'));
    }

    public function show(Command $command)
    {
        $products = $command->formated_products;
        // dd($products);

        return view('front.shop.commands.show', compact('command', 'products'));
    }
}
