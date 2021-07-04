@component('mail::message')
{{ $note->comment }}

Merci, pour la confiance <br>
{{ config('app.name') }}
@endcomponent
