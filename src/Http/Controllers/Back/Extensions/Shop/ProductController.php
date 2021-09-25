<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class ProductController extends BaseController
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = config('administrable.extensions.shop.models.product')::principal()->last()->get();

        return back_view('extensions.shop.products.index', compact('products'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = new (config('administrable.extensions.shop.models.product'));

        $product->load(['categories', 'brand',  'children.attribute', 'attributes']);

        $product->append(['complementary', 'complementary_products' ,'gallery', 'delivers_coverage_areas']);

        $product_attributes = config('administrable.extensions.shop.models.attribute')::get();

        $categories         = config('administrable.extensions.shop.models.category')::get();

        $brands             = config('administrable.extensions.shop.models.brand')::get();

        $product->children->each->append('gallery');

        $products = config('administrable.extensions.shop.models.product')::principal()->where('id', '!=', $product->getKey())->last()->get()->each->append('gallery');

        $products->each(fn ($item) => $item->children->each->append('gallery'));

        $coverage_areas = config('administrable.extensions.shop.models.coveragearea')::last()->get();
        $delivers       = config('administrable.extensions.shop.models.deliver')::with('areas')->last()->get();
        $types          = config('administrable.extensions.shop.models.product')::getTypes();

        return back_view('extensions.shop.products.create', compact(
            'product', 'product_attributes', 'categories', 'brands', 'products', 'coverage_areas', 'delivers',
            'types'
        ));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required',
            'price'            => 'required',
            'stock_management' => 'required',
        ]);

        $data = [
            'name'                    => $request->input('name'),
            'description'             => $request->input('description'),
            'price'                   => $request->input('price'),
            'type'                    => $request->input('type'),
            'width'                   => $request->input('width'),
            'height'                  => $request->input('height'),
            'weight'                  => $request->input('weight'),
            'download'                => $request->input('download'),
            'complementary_products'  => $request->input('complementary_products'),
            'command_note'            => $request->input('command_note'),
            'short_description'       => $request->input('short_description'),
            'has_review'              => $request->has('has_review'),
            'variable'                => $request->has('variable'),
            'online'                  => $request->has('online'),
            'brand_id'                => $request->input('brand_id'),
            'custom_fields'           => $request->input('custom_fields'),
        ];

        // si l'article n'est pas en promo, alors on passe le prix de promo à null et les dates
        // de debut et de fin
        if ($request->has('is_in_promotion')) {
            // valider si le prix de promotion est supereieur ou egale au prix actuel
            $data['promotion_price']    = $request->get('promotion_price');

            $start_end_promotion_dates = $this->getDatePickerFormatedDate('promotion_start_end_date');

            $data['promotion_start_at'] = Arr::first($start_end_promotion_dates);
            $data['promotion_end_at']   = Arr::last($start_end_promotion_dates);
        } else {
            $data['promotion_price']    = null;
            $data['promotion_start_at'] = null;
            $data['promotion_end_at']   = null;
        }


        // gestion du stock,
        if ($request->get('stock_management')) {
            $data['stock_management'] = true;
            $data['stock']            = $request->get('stock');
            $data['safety_stock']     = $request->get('safety_stock');
        } else {
            $data['stock_management'] = false;
            $data['stock']            = null;
            $data['safety_stock']     = null;
        }

        /**
         * @var \Guysolamour\Administrable\Models\Shop\Product
         */
        $product = config('administrable.extensions.shop.models.product')::create($data);

        // categories
        $product->saveCategories($request->input('categories'));


        // attributes
        $product->removeAttributes($request->input('deleted_attributes_id'));
        $product->saveAttributes($request->input('new_attributes'));

        // variations
        $product->removeVariations($request->input('deleted_variations_id'));
        $product->saveVariations($request->input('new_variations'));

        // Delivers Coverage Area Ptoces
        // $product->removeVariations($request->input('deleted_variations_id'));
        $product->saveDeliversCoverageAreas($request->input('new_deliver_coverage_areas'));

        // Suppression image variations
        $product->removeGalleryImages($request->input('deleted_variation_images_id'));

        flashy('L\' élément a bien été ajouté');

        return redirect_backroute('extensions.shop.product.index');
    }


      /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit(string $slug)
    {
        $product = config('administrable.extensions.shop.models.product')::where('slug', $slug)->firstOrFail();

        $product->load(['categories', 'brand',  'children.attribute' ,'attributes']);

        $product->append(['complementary', 'gallery' ,'delivers_coverage_areas']);

        $product_attributes = config('administrable.extensions.shop.models.product')::get();
        $categories = config('administrable.extensions.shop.models.category')::get();
        $brands     = config('administrable.extensions.shop.models.brand')::get();

        $product->children->each->append('gallery');

        $products = config('administrable.extensions.shop.models.product')::principal()->where('id', '!=', $product->getKey() )->last()->get()->each->append('gallery');

        $products->each(fn($item) => $item->children->each->append('gallery'));

        $coverage_areas = config('administrable.extensions.shop.models.coveragearea')::last()->get();
        $delivers = config('administrable.extensions.shop.models.deliver')::with('areas')->last()->get();

        $types = config('administrable.extensions.shop.models.product')::getTypes();

        return back_view('extensions.shop.products.edit', compact(
            'product', 'product_attributes', 'categories', 'brands', 'products', 'coverage_areas', 'delivers',
            'types'
        ));
    }


    private function validateSubmitData() :array
    {
        $request = request();

        $request->validate([
            'name'             => 'required',
            'price'            => 'required',
            'stock_management' => 'required',
        ]);

        $data =  [
            'name'                    => $request->input('name'),
            'description'             => $request->input('description'),
            'price'                   => $request->input('price'),
            'type'                    => $request->input('type'),
            'width'                   => $request->input('width'),
            'height'                  => $request->input('height'),
            'weight'                  => $request->input('weight'),
            'download'                => $request->input('download'),
            'complementary_products'  => $request->input('complementary_products'),
            'command_note'            => $request->input('command_note'),
            'short_description'       => $request->input('short_description'),
            'has_review'              => $request->has('has_review'),
            'variable'                => $request->has('variable'),
            'online'                  => $request->has('online'),
            'brand_id'                => $request->input('brand_id'),
            'custom_fields'           => $request->input('custom_fields'),
        ];

        // si l'article n'est pas en promo, alors on passe le prix de promo à null et les dates
        // de debut et de fin
        if ($request->has('is_in_promotion')) {
            $data['promotion_price']    = $request->get('promotion_price');

            $start_end_promotion_dates = $this->getDatePickerFormatedDate('promotion_start_end_date');

            $data['promotion_start_at'] = Arr::first($start_end_promotion_dates);
            $data['promotion_end_at']   = Arr::last($start_end_promotion_dates);
        } else {
            $data['promotion_price']    = null;
            $data['promotion_start_at'] = null;
            $data['promotion_end_at']   = null;
        }



        // gestion du stock,
        if ($request->get('stock_management')) {
            $data['stock_management'] = true;
            $data['stock']            = $request->get('stock');
            $data['safety_stock']     = $request->get('safety_stock');
        } else {
            $data['stock_management'] = false;
            $data['stock']            = null;
            $data['safety_stock']     = null;
        }

        return $data;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $slug)
    {
        /**
         * @var \Guysolamour\Administrable\Models\Shop\Product
         */
        $product = config('administrable.extensions.shop.models.product')::where('slug', $slug)->firstOrFail();

        $data = $this->validateSubmitData();

        $product->update($data);

        // categories
        $product->saveCategories($request->input('categories'));

        // attributes
        $product->removeAttributes($request->input('deleted_attributes_id'));
        $product->saveAttributes($request->input('new_attributes'));

        // variations
        $product->removeVariations($request->input('deleted_variations_id'));
        $product->saveVariations($request->input('new_variations'));

        // Delivers Coverage Area Ptoces
        // $product->removeVariations($request->input('deleted_variations_id'));
        $product->saveDeliversCoverageAreas($request->input('new_deliver_coverage_areas'));

        // Suppression image variations
        $product->removeGalleryImages($request->input('deleted_variation_images_id'));


        flashy('L\' élément a bien été modifié');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug)
    {
        /**
         * @var \Guysolamour\Administrable\Models\Shop\Product
         */
        $product = config('administrable.extensions.shop.models.product')::where('slug', $slug)->firstOrFail();
        $product->delete();

        flashy('L\' élément a bien été supprimé');

        return redirect_backroute('extensions.shop.product.index');
    }

    private function getDatePickerFormatedDate(string $field, ?Request $request = null): array
    {
        $request = $request ?: request();

        return array_map(function($date){
            return Carbon::parse(str_replace('/', '-', $date))->toDateTimeString();
        }, explode(' - ', request($field)));
    }
}
