@extends(back_view_path('layouts.base'))

@section('title', $post->title)

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1>Ajout</h1> --}}
                </div>
                <div class="col-sm-6">
                     <div class='float-sm-right'>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.blog.post.index') }}">Articles</a></li>
                            <li class="breadcrumb-item active">{{ $post->title }}</li>
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
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip"
                        title="Réduire">
                        <i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class='row'>
                    <div class="col-md-8">

                        <div class="col-12">
                            <section style="margin-bottom: 2rem;">

                                <div class="btn-group-horizontal">
                                    <a href="{{ back_route('extensions.blog.post.edit', $post) }}" class="btn btn-info" data-toggle="tooltip" data-placement="top"
                                        title="Editer"><i class="fas fa-edit"></i>Editer</a>
                                    <a href="{{ back_route('extensions.blog.post.destroy', $post) }}" data-method="delete"
                                        data-toggle="tooltip" data-placement="top" title="Supprimer"
                                        data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?" class="btn btn-danger"><i
                                            class="fa fa-trash"></i> Supprimer</a>
                                </div>
                            </section>
                            {{-- add fields here --}}
                            <div class="pb-2">
                                <p><span class="font-weight-bold">Titre:</span></p>
                                <p>
                                    {{ $post->title }}
                                </p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="pb-2">
                                <p><span class="font-weight-bold">Catégories:</span></p>
                                <p>
                                    @forelse ($post->categories as $category)
                                        <a href="{{ back_route('extensions.blog.category.show', $category) }}"
                                            class=" bg-azure p-2">{{ $category->name }}</a>
                                    @empty
                                        -
                                    @endforelse
                                </p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="pb-2">
                                <p><span class="font-weight-bold">Etiquettes:</span></p>
                                <p>
                                   @forelse ($post->tags as $tag)
                                      <a href="{{ back_route('extensions.blog.tag.show', $tag) }}"
                                          class=" bg-azure p-2">{{ $tag->name }}</a>
                                   @empty
                                      -
                                   @endforelse
                                </p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="pb-2">
                                <p><span class="font-weight-bold">Contenu:</span></p>
                                <p>
                                    {!! $post->content !!}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <div class="card card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">Publication</h3>
                            </div>
                            <div class="card-body">
                                <p>En ligne : {{ $post->online ? 'Oui' : 'Non' }}</p>
                                <p>Autoriser les commentaires : {{ $post->allow_comment ? 'Oui' : 'Non' }}</p>

                            </div>
                        </div>
                        <div class="card card-secondary">

                            <div class="card-body">
                                <div class="form-group">
                                    <label for="publish_date">Date de publication:</label>
                                    <p>{{ $post->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="publish_time">Heure de publication: </label>
                                    <p>{{ $post->created_at->format('h:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                         @include(back_view_path('media._show'), [
                           'model' => $post,
                        ])
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

@endsection
