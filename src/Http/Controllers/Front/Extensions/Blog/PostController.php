<?php

namespace Guysolamour\Administrable\Http\Controllers\Front\Extensions\Blog;

use Illuminate\Http\Request;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class PostController extends BaseController
{
    public function index()
    {
        $page = get_meta_page('blog');

        $posts = config('administrable.extensions.blog.post.model')::online()->with('categories')->last()->paginate(9);

        $categories = config('administrable.extensions.blog.tag.model')::withCount('posts')->last()->get();

        return front_view('extensions.blog.index', compact('page', 'posts', 'categories'));
    }

    public function show(string $slug)
    {
        $post = config('administrable.extensions.blog.post.model')::where('slug', $slug)->firstOrFail();

        return front_view('extensions.blog.show', compact('post'));
    }

    public function category(string $slug)
    {
        $category = config('administrable.extensions.blog.category.model')::where('slug', $slug)->firstOrFail();

        $posts = $category->posts()->online()->with('categories', 'approvedComments')->last()->paginate(5);

        return front_view('extensions.blog.category', compact('category', 'posts'));
    }

    public function tag(string $slug)
    {
        $tag = config('administrable.extensions.blog.tag.model')::where('slug', $slug)->firstOrFail();

        $posts = $tag->posts()->online()->with('tags', 'approvedComments')->last()->paginate(5);

        return front_view('extensions.blog.tag', compact('tag', 'posts'));
    }

    public function search(Request $request)
    {
        $q = strtolower($request->get('q'));

        $posts = config('administrable.extensions.blog.post.model')::online()->with('categories', 'approvedComments')
        ->where('title', 'LIKE', '%' . $q . '%')
            ->orWhere('content', 'LIKE', '%' . $q . '%')
            ->paginate(9);

        $posts->withPath(route('front.extensions.blog.search', compact('q')));
        $page = get_meta_page('search');

        return back_view('extensions.blog.search', compact('posts', 'page'));
    }

}
