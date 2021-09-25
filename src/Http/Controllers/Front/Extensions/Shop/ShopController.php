<?php

namespace App\Http\Controllers\Front\Shop;

use App\Models\Shop\Brand;
use Illuminate\Support\Str;
use App\Models\Shop\Product;
use Illuminate\Http\Request;
use App\Models\Shop\Category;
use Facades\App\Services\RecentlyView;
use App\Http\Controllers\Controller;

class ShopController extends Controller
{
    public function index()
    {
        $products   = Product::latest()->online()->principal()->with('media')->paginate(20);
        $categories = Category::principal()->with('children')->get();
        $brands     = Brand::get();

        return front_view('shop.index', compact('products', 'categories', 'brands'));
    }

    public function show(Product $product)
    {
        $product->load('attributes.terms', 'children');
        $product->children->each->load(['attributes.terms']);
        $product->children->each->append(['image', 'images']);
        $product->append(['image', 'images']);

        // $ids = RecentlyView::load($product->getRecentlyViewKey());
        // dd($ids);
        RecentlyView::push($product);

        $recently_viewed_products = RecentlyView::load($product);

        $categories = Category::principal()->with('children')->get();
        $brands     = Brand::get();

        return front_view('shop.show', compact('product', 'categories', 'brands', 'recently_viewed_products'));
    }

    public function category(Category $category)
    {
        $products   = $category->products()->with('media')->paginate(20);
        $categories = Category::principal()->with('children')->get();
        $brands     = Brand::get();

        return front_view('shop.category', compact('category', 'products', 'categories', 'brands'));
    }

    public function brand(Brand $brand)
    {
        $products   = $brand->products()->with('media')->paginate(20);
        $categories = Category::principal()->with('children')->get();
        $brands     = Brand::get();

        return view('front.shop.brand', compact('brand', 'products', 'categories', 'brands'));
    }

    public function search(Request $request)
    {

        $q = Str::lower($request->get('q'));

        $products = Product::online()->with('categories',  'media')
        ->where('name', 'LIKE', '%' . $q . '%')
            ->orWhere('description', 'LIKE', '%' . $q . '%')
            ->orWhere('short_description', 'LIKE', '%' . $q . '%')
            ->paginate(9)->withQueryString();


        $categories = Category::principal()->with('children')->get();
        $brands     = Brand::get();

        return view('front.shop.search', compact( 'products', 'categories', 'brands'));


        // $posts->withPath(route('front.extensions.blog.search', compact('q')));

    }


}
