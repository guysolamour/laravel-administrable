<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop;

use Guysolamour\Administrable\Http\Controllers\BaseController;


class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = config('administrable.extensions.shop.models.order')::with('client')->latest()->get();

        return back_view('extensions.shop.orders.index', compact('orders'));
    }
}
