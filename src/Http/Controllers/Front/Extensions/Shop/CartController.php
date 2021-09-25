<?php

namespace App\Http\Controllers\Front\Shop;

use App\Models\Shop\Brand;
use App\Models\Shop\Coupon;
use App\Models\Shop\Command;
use App\Models\Shop\Deliver;
use App\Models\Shop\Product;
use Illuminate\Http\Request;
use App\Models\Shop\Category;
use App\Models\Shop\CoverageArea;
use Facades\App\Models\Shop\Cart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Back\Shop\CommandSentNotification;

class CartController extends Controller
{
    public function addToShoppingCart(Product $product, Request $request)
    {
        Cart::add($product, $request->input('quantity', 1));

        if ($request->ajax()){
            return Cart::content();
        }

        flashy("Le produit {$product->name} a bien éyé ajouté au panier!");

        return back();
    }

    public function show()
    {
        $cart       = Cart::content();
        $categories = Category::principal()->with('children')->get();
        $coverage_areas = CoverageArea::has('delivers')->with('delivers')->get();
        $brands     = Brand::get();

        return view('front.shop.cart', compact('cart', 'coverage_areas','categories', 'brands'));
    }

    public function update(int $rowId, Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'instance' => 'required|string',
        ]);

        Cart::from($request->get('instance'))->update($rowId, ['quantity' => $request->get('quantity')]);

        return response()->json(['cart' => Cart::from($request->get('instance'))->content()]);
    }

    public function coupon(Request $request)
    {
        [$type, $message] = Coupon::apply($request->get('code'));

        flashy()->$type($message);

        return back();
    }

    public function removeItemInCartList($rowId, Request $request)
    {
        Cart::remove($rowId);

        if ($request->ajax()) {
            return response()->json(['cart' =>  Cart::content()]);
        }

        flashy('Le produit a bien été retiré du panier');

        return back();
    }


    public function checkout(Request $request)
    {
        $deliver = Deliver::find($request->input('deliver_id'));

        if (!$deliver){
            flashy()->error("Ce livreur n'est pas disponible pour le moment.");
            return redirect_frontroute('shop.cart.show');
        }

        $area = $deliver->areas()->where('id', $request->input('deliver_area'))->first();

        if (!$area) {
            flashy()->error("Cette zone de livraison n'est pas déservie par cet livreur.");
            return redirect_frontroute('shop.cart.show');
        }


        $user           = auth()->user();
        $shopping_cart  = $user ? $user->shopping_cart : Cart::content();

        $categories     = Category::principal()->with('children')->get();
        $brands         = Brand::get();

        return front_view("shop.checkout", compact(
            'user',  'shopping_cart', 'area',
            'categories', 'brands', 'deliver'));
    }

    public function command(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required',
            'phone_number'   => 'required',
            'deliver'        => 'required',
            'deliver'        => 'required',
        ]);

        if ($validator->fails()) {
            flashy()->error('Erreur lors de la validation du formulaire');
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $command = Command::create([
            'name'         => $request->get('name'),
            'phone_number' => $request->get('phone_number'),
            // 'email'        => $request->get('email'),
            'address'      => $request->get('address'),
            // 'city'         => $request->get('city'),
            // 'country'      => $request->get('country'),
            'products'     => Cart::rawContent(),
            'globals'      => Cart::rawGlobals(),
            'deliver'      => $request->get('deliver'),
            'user_id'      => $request->user() ?? null,
        ]);

        // envoyez email aux administrateurs
        Notification::send(get_guard_notifiers(), new CommandSentNotification($command));

        // vider le panier
        Cart::clear();


        flashy("Votre commande a bien été effectuée");

        return redirect_frontroute('shop.index')->with('success', 'Votre commande a bien été effectuée');
    }



}
