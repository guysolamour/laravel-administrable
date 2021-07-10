<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Blog;

use Illuminate\Http\Request;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;


class TagController extends BaseController
{
    use FormBuilderTrait;

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = config('administrable.extensions.blog.tag.model')::last()->get();

        return back_view('extensions.blog.tags.index', compact('tags'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = $this->getForm(new (config('administrable.extensions.blog.tag.model')), config('administrable.extensions.blog.tag.back.form'));

        return back_view('extensions.blog.tags.create', compact('form'));
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $form = $this->getForm(new (config('administrable.extensions.blog.tag.model')), config('administrable.extensions.blog.category.back.form'));
       $form->redirectIfNotValid();

       config('administrable.extensions.blog.tag.model')::create($request->all());

       flashy('L\' élément a bien été ajouté');

       return redirect()->route('back.extensions.blog.tag.index');
    }





    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug)
    {
        $tag = config('administrable.extensions.blog.tag.model')::where('slug', $slug)->firstOrFail();

       return back_view('extensions.blog.tags.show', compact('tag'));
    }



      /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit(string $slug)
    {
        $tag = config('administrable.extensions.blog.tag.model')::where('slug', $slug)->firstOrFail();

        $form = $this->getForm($tag, config('administrable.extensions.blog.category.back.form'));

        return back_view('back.extensions.blog.tags.edit', compact('tag','form'));
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
        $tag = config('administrable.extensions.blog.tag.model')::where('slug', $slug)->firstOrFail();

        $form = $this->getForm($tag, config('administrable.extensions.blog.category.back.form'));
        $form->redirectIfNotValid();
        $tag->update($request->all());

        flashy('L\' élément a bien été modifié');

        return redirect()->to(back_route('extensions.blog.tag.index'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug)
    {
        $tag = config('administrable.extensions.blog.tag.model')::where('slug', $slug)->firstOrFail();

        $tag->delete();

        flashy('L\' élément a bien été supprimé');

        return redirect()->to(back_route('extensions.blog.tag.index'));
    }

}
