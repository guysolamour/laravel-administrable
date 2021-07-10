<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Blog;

use Illuminate\Http\Request;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class PostController extends BaseController
{
    use FormBuilderTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = config('administrable.extensions.blog.post.model')::with('categories')->last()->get();

        return back_view('extensions.blog.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $post = new (config('administrable.extensions.blog.post.model'));
        $post->setRelation('categories', collect());
        $post->setRelation('tags', collect());

        $form = $this->getForm($post, config('administrable.extensions.blog.post.back.form'));

        $categories = config('administrable.extensions.blog.category.model')::latest()->get();

        $tags = config('administrable.extensions.blog.tag.model')::latest()->get();

        return back_view('extensions.blog.posts.create', compact('form', 'categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $this->getForm(new (config('administrable.extensions.blog.post.model')), config('administrable.extensions.blog.post.back.form'));
        $form->redirectIfNotValid();

        config('administrable.extensions.blog.post.model')::create($request->all());

        flashy('L\' élément a bien été ajouté');

        return redirect()->to(back_route('extensions.blog.post.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug)
    {
        $post = config('administrable.extensions.blog.post.model')::where('slug', $slug)->firstOrFail();

        return back_view('extensions.blog.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit(string $slug)
    {
        $post = config('administrable.extensions.blog.post.model')::where('slug', $slug)->firstOrFail();

        $post->load(['categories', 'tags']);

        $form = $this->getForm($post, config('administrable.extensions.blog.post.back.form'));

        $categories = config('administrable.extensions.blog.category.model')::last()->get();

        $tags  = config('administrable.extensions.blog.tag.model')::last()->get();

        return back_view('extensions.blog.posts.edit', compact('post', 'form', 'categories', 'tags'));
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
        $post = config('administrable.extensions.blog.post.model')::where('slug', $slug)->firstOrFail();

        $form = $this->getForm($post, config('administrable.extensions.blog.post.back.form'));
        $form->redirectIfNotValid();

        $post->update($request->all());

        flashy('L\' élément a bien été modifié');

        return redirect()->to(back_route('extensions.blog.post.index'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug)
    {
        $post = config('administrable.extensions.blog.post.model')::where('slug', $slug)->firstOrFail();

        $post->delete();

        flashy('L\' élément a bien été supprimé');

        return back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string'
        ]);

        $category = config('administrable.extensions.blog.category.model')::create($data);

        return $category;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addTag(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string'
        ]);

        $tag = config('administrable.extensions.blog.tag.model')::create($data);

        return $tag;
    }

}
