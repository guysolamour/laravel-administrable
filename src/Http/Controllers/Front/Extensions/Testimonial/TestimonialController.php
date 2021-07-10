<?php

namespace Guysolamour\Administrable\Http\Controllers\Front\Extensions\Testimonial;

use Guysolamour\Administrable\Http\Controllers\BaseController;

class TestimonialController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $testimonials = config('administrable.extensions.testimonial.model')::online()->last()->paginate(9);

      return front_view('extensions.testimonials.index', compact('testimonials'));
    }
}
