@extends(back_view_path('layouts.base'))

@section('title', $page->name)

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <ol class="breadcrumb">
                             <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('page.index') }}">{{ Lang::get("administrable::messages.view.page.plural") }}</a></li>
                            <li class="breadcrumb-item active">{{ $page->name }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->

    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                <div class='row'>
                    <div class='col-md-12'>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h3 class="card-title">Page: {{ $page->name }}</h3>
                                <div class="btn-group float-right">
                                   @include(back_view_path('pages._addmetataform'))
                                    <a class="btn btn-info" href="{{ back_route('page.edit', $page) }}">
                                        <i class="fas fa-edit"></i> {{ Lang::get("administrable::messages.default.modify") }}</a>
                                </div>
                            </div>


                            <div class="card-body row">
                                <div class="col-md-12">
                                    <div class="row">
                                        {{-- add fields here --}}
                                         <div class="col-md-6">
                                            <p><span class="font-weight-bold">{{ Lang::get("administrable::messages.view.page.name") }}:</span></p>
                                            <p>
                                                {{ $page->name }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><span class="font-weight-bold">{{ Lang::get("administrable::messages.view.page.route") }}:</span></p>
                                            <p>
                                                {{ $page->route }}
                                            </p>
                                        </div>
                                    </div>
                                     <div class="row">
                                        <div class="col-md-12">
                                            <p><span class="font-weight-bold">{{ Lang::get("administrable::messages.view.page.route") }}:</span></p>
                                            @if($page->uri)
                                            <p class="pb-2 "><b>{{ Lang::get("administrable::messages.view.page.uri") }}: </b><a href="{{ $page->uri }}" class="text-primary" target="_blank">{{ $page->uri }}</a>
                                            </p>
                                            @else
                                            {{ Lang::get("administrable::messages.view.page.nouri") }}
                                            @endif
                                        </div>
                                 </div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="row" style="width: 100%">
                @foreach($page->metatags as $group)
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                        <button class="btn btn-link font-weight-bold text-uppercase text-dark" type="button">
                                {{ Lang::get('administrable::messages.view.pagemeta.group') }}: {{ $group->name }} ({{ $group->children->count() }})
                                @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                | {{ $group->code }}
                                @endrole
                            </button>
                            <div class="btn-group float-right">
                                <button class="btn btn-secondary btn-sm" title="Voir le contenu du groupe" data-toggle="collapse"
                                    data-target="#collapseExample{{ $group->id }}" aria-expanded="false"
                                    aria-controls="collapseExample{{ $group->id }}"><i class="fa fa-eye"></i></button>
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#editMetaModal{{ $group->id }}">
                                    <i class="fas fa-edit"></i> </button>
                                @include(back_view_path('pages._updatemetaform'), ['meta' => $group])
                                @include(back_view_path('pages._addmetataform'), ['group' => $group])
                                <a href="{{ back_route('pagemeta.destroy', [$page, $group]) }}" data-method="delete"
                                    data-confirm="{{ Lang::get('administrable::messages.view.pagemeta.destroy') }}"
                                    class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"><i
                                        class="fas fa-trash"></i> </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="collapse" id="collapseExample{{ $group->id }}">
                                <div class="row">
                                    @forelse($group->children->reverse() as $meta)
                                        @if ($meta->isImage())
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="media">
                                                            <img src="{{ $meta->image_url }}" class="mr-3" alt="{{ $meta->title }}" width="80" height="80" title="{{ $meta->title }}">
                                                            <div class="media-body">
                                                                @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                                                <h6 class="mt-0">Code: {{ $meta->code }}</h6>
                                                                @endrole
                                                                <hr>
                                                                <p>{{ $meta->title }}</p>
                                                                <div class="btn-group float-right">
                                                                    <a class="btn btn-info btn-sm" href="#" data-toggle="modal"
                                                                        data-target="#editMetaModal{{ $meta->id }}">
                                                                        <i class="fas fa-edit"></i> &nbsp;{{ Lang::get('administrable::messages.default.modify') }} </a>

                                                                    @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                                                    <a href="{{ back_route('pagemeta.destroy', [$page, $meta]) }}" data-method="delete"
                                                                        data-confirm="{{ Lang::get('administrable::messages.view.pagemeta.destroy') }}"
                                                                        class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"><i
                                                                            class="fas fa-trash"></i>
                                                                        &nbsp;{{ Lang::get('administrable::messages.default.delete') }}</a>
                                                                    @endrole
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($meta->isVideo())
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="media">
                                                            <div class="embed-responsive embed-responsive-16by9 mr-3" style="display: {{ $meta->isVideo() ? 'block' : 'none' }}; height: 200px;width: 50%;">
                                                                <iframe
                                                                    class="embed-responsive-item" src="{{$meta->video_url }}" allowfullscreen>
                                                                </iframe>
                                                            </div>
                                                            <div class="media-body">
                                                                @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                                                <h6 class="mt-0">Code: {{ $meta->code }}</h6>
                                                                @endrole
                                                                <hr>
                                                                <p>{{ $meta->title }}</p>
                                                                <div class="btn-group float-right">
                                                                    <a class="btn btn-info btn-sm" href="#" data-toggle="modal"
                                                                        data-target="#editMetaModal{{ $meta->id }}">
                                                                        <i class="fas fa-edit"></i> &nbsp;{{ Lang::get('administrable::messages.default.modify') }} </a>

                                                                    @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                                                    <a href="{{ back_route('pagemeta.destroy', [$page, $meta]) }}" data-method="delete"
                                                                        data-confirm="{{ Lang::get('administrable::messages.view.pagemeta.destroy') }}"
                                                                        class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"><i
                                                                            class="fas fa-trash"></i>
                                                                        &nbsp;{{ Lang::get('administrable::messages.default.delete') }}</a>
                                                                    @endrole
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($meta->isAttachedFile())
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="media">
                                                            <div class="media-body">
                                                                @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                                                <h6 class="mt-0">Code: {{ $meta->code }}</h6>
                                                                @endrole
                                                                <hr>
                                                                <p>{{ $meta->attachedfile->name }}</p>
                                                                <a class="badge badge-primary pt-2" href="{{ $meta->attachedfile->getUrl() }}" title="{{ $meta->attachedfile->getUrl() }}" target="_blank">Ouvrir le fichier dans un nouvel onglet</a>

                                                                <div class="btn-group float-right">
                                                                    <a class="btn btn-info btn-sm" href="#" data-toggle="modal"
                                                                        data-target="#editMetaModal{{ $meta->id }}">
                                                                        <i class="fas fa-edit"></i> &nbsp;{{ Lang::get('administrable::messages.default.modify') }} </a>

                                                                    @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                                                    <a href="{{ back_route('pagemeta.destroy', [$page, $meta]) }}" data-method="delete"
                                                                        data-confirm="{{ Lang::get('administrable::messages.view.pagemeta.destroy') }}"
                                                                        class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"><i
                                                                            class="fas fa-trash"></i>
                                                                        &nbsp;{{ Lang::get('administrable::messages.default.delete') }}</a>
                                                                    @endrole
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="media">
                                                            <div class="media-body">
                                                                @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                                                <h6 class="mt-0">Code: {{ $meta->code }}</h6>
                                                                @endrole
                                                                <hr>
                                                                <p>{{ $meta->title }}</p>

                                                                <p>{{ $meta->content }}</p>
                                                                <div class="btn-group float-right">
                                                                    <a class="btn btn-info btn-sm" href="#" data-toggle="modal"
                                                                        data-target="#editMetaModal{{ $meta->id }}">
                                                                        <i class="fas fa-edit"></i> &nbsp;{{ Lang::get('administrable::messages.default.modify') }} </a>

                                                                    @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                                                    <a href="{{ back_route('pagemeta.destroy', [$page, $meta]) }}" data-method="delete"
                                                                        data-confirm="{{ Lang::get('administrable::messages.view.pagemeta.destroy') }}"
                                                                        class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"><i
                                                                            class="fas fa-trash"></i>
                                                                        &nbsp;{{ Lang::get('administrable::messages.default.delete') }}</a>
                                                                    @endrole
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @include(back_view_path('pages._updatemetaform'), ['meta' => $meta])
                                    @empty
                                    <div class="col-12 text-center">
                                    {{ Lang::get('administrable::messages.view.pagemeta.emptygroup') }}
                                    </div>
                                    @endforelse
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
</div>

</div>
<!-- /.card-body -->

<!-- /.card -->

</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<x-administrable::tinymce :model="$page" />

@endsection
