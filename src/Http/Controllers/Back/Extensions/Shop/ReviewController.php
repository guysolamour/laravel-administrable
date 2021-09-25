<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop;

use Illuminate\Http\Request;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class ReviewController extends BaseController
{
    use FormBuilderTrait;

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reviews = config('administrable.extensions.shop.models.review')::with('product')->last()->get();

        return back_view('extensions.shop.reviews.index', compact('reviews'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = $this->getForm(config('administrable.extensions.shop.models.review'), config('administrable.extensions.shop.forms.back.review'));

        return back_view('extensions.shop.reviews.create', compact('form'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $this->getForm(config('administrable.extensions.shop.models.review'), config('administrable.extensions.shop.forms.back.review'));

       $form->redirectIfNotValid();

       $review = new (config('administrable.extensions.shop.models.review')([
           'content'    => $request->input('content'),
           'approved'   => $request->input('approved'),
           'product_id' => $request->input('product_id')
       ]));


       if ($note = $request->input('note')){
            $review->note = $note;
       }

       if ($request->input('guest_author')){
          $review->name         = $request->input('name');
          $review->email        = $request->input('email');
          $review->phone_number = $request->input('phone_number');
       }else {
           $review->author_type = get_class(get_guard());
           $review->author_id   = get_guard('id');
       }

       $review->save();

       flashy('L\' élément a bien été ajouté');

        return redirect_backroute('extensions.shop.review.index');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $review = config('administrable.extensions.shop.models.review')::where('id', $id)->firstOrFail();

       return back_view('extensions.shop.reviews.show', compact('review'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $review = config('administrable.extensions.shop.models.review')::where('id', $id)->firstOrFail();

        $form = $this->getForm($review, config('administrable.extensions.shop.forms.back.review'));

        return back_view('extensions.shop.reviews.edit', compact('review','form'));
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
        $review = config('administrable.extensions.shop.models.review')::where('id', $id)->firstOrFail();

        $form = $this->getForm($review, config('administrable.extensions.shop.forms.back.review'));
        $form->redirectIfNotValid();

        $review->update($request->all());

        flashy('L\' élément a bien été modifié');

        return redirect_backroute('extensions.shop.review.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve(int $id)
    {
        $review = config('administrable.extensions.shop.models.review')::where('id', $id)->firstOrFail();

        $review->update([
            'approved' => true,
        ]);

        flashy('L\' avis a bien été approuvé');

        return back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $review = config('administrable.extensions.shop.models.review')::where('id', $id)->firstOrFail();
        $review->delete();

        flashy('L\' élément a bien été supprimé');

        return redirect_backroute('extensions.shop.review.index');
    }
}
