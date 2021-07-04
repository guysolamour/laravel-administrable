@component('mail::message')
Bonjour <b>{{ $comment->getCommenterName() }}</b>, votre commentaire publié sur notre
plateforme vient de recevoir une réponse.


@component('mail::button', ['url' => route('front.' . Str::lower(class_basename($comment->commentable))  . '.show',$comment->commentable)])
Voir la réponse
@endcomponent


Merci, pour la confiance <br>
{{ config('app.name') }}
@endcomponent
