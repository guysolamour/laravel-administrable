<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Testimonial;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;


class TestimonialController extends BaseController
{
    use FormBuilderTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $testimonials = config('administrable.extensions.testimonial.model')::last()->get();

        return back_view('extensions.testimonials.testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = $this->getForm(new (config('administrable.extensions.testimonial.model')), config('administrable.extensions.testimonial.back.form'));

        return back_view('extensions.testimonials.testimonials.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $this->getForm(new (config('administrable.extensions.testimonial.model')), config('administrable.extensions.testimonial.back.form'));

        $form->redirectIfNotValid();

        config('administrable.extensions.testimonial.model')::create($request->all());

        flashy(Lang::get('administrable::extensions.testimonials.controller.create'));

        return redirect()->to(back_route('extensions.testimonial.testimonial.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug)
    {
        $testimonial = config('administrable.extensions.testimonial.model')::where('slug', $slug)->firstOrFail();

        return back_view('extensions.testimonials.testimonials.show', compact('testimonial'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit(string $slug)
    {
        $testimonial = config('administrable.extensions.testimonial.model')::where('slug', $slug)->firstOrFail();

        $form = $this->getForm($testimonial, config('administrable.extensions.testimonial.back.form'));

        return back_view('extensions.testimonials.testimonials.edit', compact('testimonial', 'form'));
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
        $testimonial = config('administrable.extensions.testimonial.model')::where('slug', $slug)->firstOrFail();

        $form = $this->getForm($testimonial, config('administrable.extensions.testimonial.back.form'));
        $form->redirectIfNotValid();
        $testimonial->update($request->all());

        flashy(Lang::get('administrable::extensions.testimonial.controller.update'));

        return redirect()->to(back_route('extensions.testimonial.testimonial.index'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug)
    {
        $testimonial = config('administrable.extensions.testimonial.model')::where('slug', $slug)->firstOrFail();

        $testimonial->delete();

        flashy(Lang::get('administrable::extensions.testimonial.controller.delete'));

        return redirect()->to(back_route('extensions.testimonial.index'));
    }

}
