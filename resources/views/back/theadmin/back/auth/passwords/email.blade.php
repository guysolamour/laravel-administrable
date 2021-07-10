@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.passwordupdate'))

@section('content')
    <div class="row min-h-fullscreen center-vh p-20 m-0">
      <div class="col-12">
        <div class="card card-shadowed px-50 py-30 w-400px mx-auto" style="max-width: 100%">
          <h5 class="text-uppercase">{{ Lang::get('administrable::messages.view.auth.passwordupdate') }}</h5>
          <br>
          @if (session('status'))
              <div class="alert alert-success" role="alert">
                  {{ session('status') }}
              </div>
          @endif
          <form class="form-type-material"  action="{{ route(config('administrable.guard') .'.password.email') }}" method="post">
            @csrf
            @honeypot
            <div class="form-group">
              <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" value="{{ old('email') }}">
              <label for="email">{{ Lang::get('administrable::messages.view.auth.email') }}</label>
               @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>

            <br>
            <button class="btn btn-bold btn-block btn-primary" type="submit"> {{ Lang::get('administrable::messages.view.auth.sendupdatelink') }}</button>
          </form>
        </div>
      </div>


      <footer class="col-12 align-self-end text-center fs-13">
          <p class="mb-0"><small>Copyright © {{ date('Y') }} <a href="https://aswebagency.com">AswebAgency</a>. {{ Lang::get("All rights reserved.") }}</small></p>
      </footer>
    </div>

@endsection
