<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop;

use Illuminate\Http\Request;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class AttributeController extends BaseController
{
    use FormBuilderTrait;

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attributes = config('administrable.extensions.shop.models.attribute')::last()->get();

        return back_view('extensions.shop.attributes.index', compact('attributes'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = $this->getForm(config('administrable.extensions.shop.models.attribute'), config('administrable.extensions.shop.forms.back.attribute'));

        return back_view('extensions.shop.attributes.create',compact('form'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $this->getForm(config('administrable.extensions.shop.models.attribute'), config('administrable.extensions.shop.forms.back.attribute'));

       $form->redirectIfNotValid();

        config('administrable.extensions.shop.models.attribute')::create($request->all());

       flashy('L\' élément a bien été ajouté');

       return redirect()->route('back.extensions.shop.attribute.index');
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug)
    {
        $attribute = config('administrable.extensions.shop.models.attribute')::where('slug', $slug)->firstOrFail();

        return back_view('extensions.shop.attributes.show', compact('attribute'));
    }



      /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit(string $slug)
    {
        $attribute = config('administrable.extensions.shop.models.attribute')::where('slug', $slug)->firstOrFail();

        $form = $this->getForm($attribute, config('administrable.extensions.shop.forms.back.attribute'));

        return back_view('extensions.shop.attributes.edit', compact('attribute','form'));
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
        $attribute = config('administrable.extensions.shop.models.attribute')::where('slug', $slug)->firstOrFail();
        $form = $this->getForm($attribute, config('administrable.extensions.shop.forms.back.attribute'));

        $form->redirectIfNotValid();

        $attribute->update($request->all());

        flashy('L\' élément a bien été modifié');

        return redirect()->to(back_route_path('extensions.shop.attribute.index'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug)
    {
        $attribute = config('administrable.extensions.shop.models.attribute')::where('slug', $slug)->firstOrFail();

        $attribute->delete();

        flashy('L\' élément a bien été supprimé');

        return redirect()->to(back_route_path('extensions.shop.attribute.index'));
    }


}
