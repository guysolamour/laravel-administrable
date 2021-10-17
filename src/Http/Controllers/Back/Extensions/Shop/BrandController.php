<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop;

use Illuminate\Http\Request;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class BrandController extends BaseController
{
    use FormBuilderTrait;

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = config('administrable.extensions.shop.models.brand')::last()->get();

        return back_view('extensions.shop.brands.index', compact('brands'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = $this->getForm(config('administrable.extensions.shop.models.brand'), config('administrable.extensions.shop.forms.back.brand'));

        return back_view('extensions.shop.brands.create',compact('form'));
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $this->getForm(config('administrable.extensions.shop.models.brand'), config('administrable.extensions.shop.forms.back.brand'));

        $form->redirectIfNotValid();

        $brand = config('administrable.extensions.shop.models.brand')::create($request->all());

        if ($request->ajax()){
            return  $brand;
        }

        flashy('L\' élément a bien été ajouté');

        return redirect_backroute('extensions.shop.brand.index');
    }





    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug)
    {
        $brand = config('administrable.extensions.shop.models.brand')::where('slug', $slug)->firstOrFail();

       return back_view('extensions.shop.brands.show', compact('brand'));
    }



      /**
     * Show the form for editing the specified resource.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function edit(string $slug)
    {
        $brand = config('administrable.extensions.shop.models.brand')::where('slug', $slug)->firstOrFail();

        $form = $this->getForm($brand, config('administrable.extensions.shop.forms.back.brand'));

        return back_view('extensions.shop.brands.edit', compact('brand','form'));
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
        $brand = config('administrable.extensions.shop.models.brand')::where('slug', $slug)->firstOrFail();

        $form = $this->getForm($brand, config('administrable.extensions.shop.forms.back.brand'));

        $form->redirectIfNotValid();
        $brand->update($request->all());

        flashy('L\' élément a bien été modifié');

        return redirect_backroute('extensions.shop.brand.index');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug)
    {
        $brand = config('administrable.extensions.shop.models.brand')::where('slug', $slug)->firstOrFail();
        $brand->delete();

        flashy('L\' élément a bien été supprimé');

        return redirect_backroute('extensions.shop.brand.index');
    }

}
