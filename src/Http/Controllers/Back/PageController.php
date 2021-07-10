<?php

namespace Guysolamour\Administrable\Http\Controllers\Back;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Guysolamour\Administrable\Module;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class PageController extends BaseController
{
    use FormBuilderTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = Module::getModel('page')::latest('id')->get();

        return back_view('pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(get_admin()->isConceptorAdmin(), 401);

        $form = $this->getForm(Module::getModel('page'), Module::backForm('page'));

        return back_view('pages.create', compact('form'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(get_admin()->isConceptorAdmin(), 401);

        $form = $this->getForm(Module::getModel('page'), Module::backForm('page'));
        $form->redirectIfNotValid();

        Module::model('page')::create($request->all());

        flashy(Lang::get("administrable::messages.controller.page.create"));

        return redirect()->to(back_route('page.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug)
    {
        $page = Module::model('page')::where('slug', $slug)->firstOrFail();
        $page->load(['metatags' => fn ($query) => $query->groupField()]);

        return back_view('pages.show', compact('page'));
    }

    /**
     * Show the form for editing a new resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit(string $slug)
    {
        $page = Module::model('page')::where('slug', $slug)->firstOrFail();
        $form = $this->getForm($page, Module::backForm('page'));

        return back_view('pages.edit', compact('page', 'form'));
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
        $page = Module::model('page')::where('slug', $slug)->firstOrFail();

        $form = $this->getForm($page, Module::backForm('page'));
        $form->redirectIfNotValid();

        $page->update($request->all());

        flashy(Lang::get("administrable::messages.controller.page.update"));

        return redirect()->to(back_route('page.show', $page));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug)
    {
        $page = Module::model('page')::where('slug', $slug)->firstOrFail();

        abort_unless(get_admin()->isConceptorAdmin(), 401);
        $page->delete();

        flashy(Lang::get("administrable::messages.controller.page.delete"));

        return redirect()->to(back_route('page.index'));
    }

    /**
     * @param string $slug
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeMetaTag(string $slug, Request $request)
    {
        $page = Module::model('page')::where('slug', $slug)->firstOrFail();

        $request->validate($this->getValidationRules());

        $data = [
            'code'      => $request->get('code'),
            'name'      => $request->get('name'),
            'title'     => $request->get('title'),
            'type'      => $request->get('type'),
            'parent_id' => $request->get('parent_id'),
            'content'   => $this->getContentFieldValue($request)
        ];

        if ('group' === get_meta_type(request('type'))) {
            $data['parent_id'] = null;
        }

        $pagemeta = $page->metatags()->create($data);

        $this->saveImage($pagemeta, $request);

        flashy(Lang::get("administrable::messages.controller.metatag.create"));

        return back();
    }


    public function updateMetaTag($page, int $id, Request $request)
    {
        $pagemeta = Module::model('meta')::where('id', $id)->firstOrFail();

        $request->validate($this->getValidationRules(true));

        $pagemeta->update([
            'name'    => $request->get('name'),
            'title'   => $request->get('title'),
            'content' => $this->getContentFieldValue($request),
        ]);
        $this->saveImage($pagemeta, $request);

        flashy(Lang::get("administrable::messages.controller.metatag.update"));

        return back();
    }


    public function deleteMetaTag($page, int $id)
    {
        $pagemeta = Module::model('meta')::where('id', $id)->firstOrFail();

        abort_unless(get_admin()->isConceptorAdmin(), 401);

        $pagemeta->delete();

        flashy(Lang::get("administrable::messages.controller.metatag.delete"));

        return back();
    }

    private function getValidationRules(bool $edit = false): array
    {
        $rules =  [
            'code'     => 'required',
            'name'     => 'required',
            'title'    => 'nullable',
            'content'  => 'nullable',
            'type'     => 'required',
            'page_id'  => 'required',
        ];

        if (!$edit) {
            if ('image' === get_meta_type(request('type'))) {
                $rules['imagecontent'] = 'required|image';
            }

            if ('attachedfile' === get_meta_type(request('type'))) {
                $rules['attachedfilecontent'] = 'required|file';
            }
        }

        return $rules;
    }


    /**
     *
     * @param  \Illuminate\Http\Request $request
     * @return string|null
     */
    private function getContentFieldValue($request): ?string
    {
        $types = Module::model('pagemeta')::TYPES;

        foreach ($types as $key => $type) {
            if ($request->get('type') == $type['value']) {
                return $request->get("{$key}content");
            }
        }

        return null;
    }


    /**
     * @param int $pagemeta
     * @param \Illuminate\Http\Request $id
     * @return void
     */
    private function saveImage(int $id,Request $request) :void
    {
        $pagemeta = Module::model('meta')::where('id', $id)->firstOrFail();
        if ('image' === get_meta_type(request('type'))) {
            $key = 'imagecontent';
        } else if ('attachedfile' === get_meta_type(request('type'))) {
            $key = 'attachedfilecontent';
        } else {
            $key = null;
        }

        if ($request->file($key)) {
            $media = $pagemeta->addMediaFromRequest($key)->toMediaCollection(config('administrable.media.collections.attachments.label'));

            $pagemeta->update(['content' => $media->getKey()]);
        }
    }
}
