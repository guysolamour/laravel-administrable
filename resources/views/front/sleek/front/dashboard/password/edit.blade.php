@extends(front_view_path('layouts.dashboard'))

<x-administrable::seotags :force="true" title="Edition de profil" />

@section('content')

<div class="content">
    <!-- Top Statistics -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header card-header-border-bottom">
                    <h2>Modification de mot de passe</h2>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif
                    <form action="{{ route('front.dashboard.password.change') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group col-md-12 ">
                            <input type="password" name="password" class="form-control input-lg  @error('password') is-invalid @enderror"
                                id="password" placeholder="Nouveau mot de passe" required >
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 ">
                            <input type="password" name="password_confirmation" required class="form-control input-lg" id="password_confirmation"
                                placeholder="Confirmation nouveau mot de passe">
                        </div>


                        <div class="d-flex justify-content-end mt-5">
                            <button type="submit" class="btn btn-primary btn-default">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>



</div>

@endsection
