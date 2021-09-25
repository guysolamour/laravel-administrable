<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop;

use Illuminate\Http\Request;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class CategoryController extends BaseController
{
    use FormBuilderTrait;

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = config('administrable.extensions.shop.models.category')::last()->get();

        return back_view('extensions.shop.categories.index', compact('categories'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = $this->getForm(config('administrable.extensions.shop.models.category'), config('administrable.extensions.shop.forms.back.category'));

        return back_view('extensions.shop.categories.create',compact('form'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $this->getForm(config('administrable.extensions.shop.models.category'), config('administrable.extensions.shop.forms.back.category'));

       $form->redirectIfNotValid();

        config('administrable.extensions.shop.models.brand')::create($request->all());

       flashy('L\' élément a bien été ajouté');

       return redirect_backroute('extensions.shop.category.index');
    }



    /**
     * Display the specified resource.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug)
    {
        $category = config('administrable.extensions.shop.models.category')::where('slug', $slug)->firstOrFail();

       return back_view('extensions.shop.categories.show', compact('category'));
    }



      /**
     * Show the form for editing the specified resource.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function edit(string $slug)
    {
        $category = config('administrable.extensions.shop.models.category')::where('slug', $slug)->firstOrFail();
        $form = $this->getForm($category, config('administrable.extensions.shop.forms.back.category'));

        return back_view('extensions.shop.categories.edit', compact('category','form'));
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
        $category = config('administrable.extensions.shop.models.category')::where('slug', $slug)->firstOrFail();

        $form = $this->getForm($category, config('administrable.extensions.shop.forms.back.category'));
        $form->redirectIfNotValid();

        $category->update($request->all());

        flashy('L\' élément a bien été modifié');

        return redirect_backroute('extensions.shop.category.index');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug)
    {
        $category = config('administrable.extensions.shop.models.category')::where('slug', $slug)->firstOrFail();

        $category->delete();

        flashy('L\' élément a bien été supprimé');

        return redirect_backroute('extensions.shop.category.index');
    }

}
