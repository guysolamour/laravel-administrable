<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop;

use Illuminate\Http\Request;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class DeliverController extends BaseController
{
    use FormBuilderTrait;

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $delivers       = config('administrable.extensions.shop.models.deliver')::last()->get();
        $coverage_areas = config('administrable.extensions.shop.models.coveragearea')::last()->get();

        return back_view('extensions.shop.delivers.index', compact('delivers', 'coverage_areas'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = $this->getForm();

        $coverage_areas = config('administrable.extensions.shop.models.coveragearea')::last()->get();

        return back_view('extensions.shop.delivers.create', compact('form', 'coverage_areas'));
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $this->getForm(config('administrable.extensions.shop.models.deliver'), config('administrable.extensions.shop.forms.back.deliver'));

       $form = $this->getForm();
       $form->redirectIfNotValid();

       config('administrable.extensions.shop.models.deliver')::create($request->all());

       flashy('L\' élément a bien été ajouté');

        return redirect()->to(back_route_path('extensions.shop.deliver.index'));
    }





    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug)
    {
        $deliver = config('administrable.extensions.shop.models.deliver')::where('slug', $slug)->firstOrFail();

       return back_view('extensions.shop.delivers.show', compact('deliver'));
    }



      /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit(string $slug)
    {
        $deliver        = config('administrable.extensions.shop.models.deliver')::where('slug', $slug)->firstOrFail();

        $form           = $this->getForm($deliver, config('administrable.extensions.shop.forms.back.deliver'));

        $coverage_areas = config('administrable.extensions.shop.models.coveragearea')::last()->get();

        $deliver->append('area_prices');

        return back_view('extensions.shop.delivers.edit', compact('deliver','form', 'coverage_areas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $slug)
    {
        $deliver = config('administrable.extensions.shop.models.deliver')::where('slug', $slug)->firstOrFail();

        $form = $this->getForm($deliver, config('administrable.extensions.shop.forms.back.deliver'));
        $form->redirectIfNotValid();

        $deliver->update($request->all());

        flashy('L\' élément a bien été modifié');

        return redirect_backroute('extensions.shop.deliver.index');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug)
    {
        $deliver = config('administrable.extensions.shop.models.deliver')::where('slug', $slug)->firstOrFail();
        $deliver->delete();

        flashy('L\' élément a bien été supprimé');

        return redirect_backroute('extensions.shop.deliver.index');
    }
}
