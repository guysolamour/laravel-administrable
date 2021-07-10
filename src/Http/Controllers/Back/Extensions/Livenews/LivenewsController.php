<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Livenews;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class LivenewsController extends BaseController
{
    use FormBuilderTrait;

    public function index()
    {
        $livenews = config('administrable.extensions.livenews.model')::latest()->get();

        return back_view('extensions.livenews.livenews.index', compact('livenews'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = $this->getForm(new (config('administrable.extensions.livenews.model')), config('administrable.extensions.livenews.back.form'));

        return back_view('extensions.livenews.livenews.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $this->getForm(new (config('administrable.extensions.livenews.model')), config('administrable.extensions.livenews.back.form'));
        $form->redirectIfNotValid();

        config('administrable.extensions.livenews.model')::create($request->all());

        flashy(Lang::get('administrable::extensions.livenews.controller.create'));

        return redirect()->to(back_route('extensions.livenews.livenews.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $livenews = config('administrable.extensions.livenews.model')::where('id', $id)->firstOrFail();

        $form = $this->getForm($livenews, config('administrable.extensions.livenews.back.form'));

        return back_view('extensions.livenews.livenews.edit', compact('livenews', 'form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $livenews = config('administrable.extensions.livenews.model')::where('id', $id)->firstOrFail();

        $form = $this->getForm($livenews, config('administrable.extensions.livenews.back.form'));
        $form->redirectIfNotValid();

        $livenews->update($request->all());

        flashy(Lang::get('administrable::extensions.livenews.controller.update'));

        return redirect()->to(back_route('extensions.livenews.livenews.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $livenews = config('administrable.extensions.livenews.model')::where('id', $id)->firstOrFail();

        $livenews->delete();

        flashy(Lang::get('administrable::extensions.livenews.controller.delete'));

        return redirect()->to(back_route('extensions.livenews.livenews.index'));
    }
}
