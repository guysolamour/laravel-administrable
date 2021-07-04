
@if(isset($reply) && $reply === true)
<div id="comment-{{ $comment->getKey() }}" class="media m-4">
@else
<li id="comment-{{ $comment->getKey() }}" class="media m-4">
@endif
        @if($comment->commenter && get_class($comment->commenter) === get_guard_model_class())
            <img class="mr-3"
                src="{{ $comment->commenter->avatar }}"
                alt="{{ $comment->commenter->pseudo ?: $comment->commenter->name }} avatar">
        @else
            <img class="mr-3"
                src="{{ Gravatar::get($comment->commenter->email ?? $comment->guest_email) }}"
                alt="{{ $comment->commenter->name ?? $comment->guest_name }} avatar">
        @endif

        <div class="media-body">
            @if($comment->commenter && get_class($comment->commenter) === get_guard_model_class())
            <h5 class="mt-0 mb-1">{{ $comment->commenter->pseudo ?: $comment->commenter->name }} <small class="text-muted">-
                    {{ $comment->created_at->diffForHumans() }}</small></h5>
            @else
            <h5 class="mt-0 mb-1">{{ $comment->commenter->name ?? $comment->guest_name }} <small class="text-muted">-
                    {{ $comment->created_at->diffForHumans() }}</small></h5>
            @endif

            <div style="white-space: pre-wrap;">{!! $comment->comment !!}</div>

            <div class="btn-group mt-4">
                @can('reply', $comment)
                <button data-toggle="modal" data-target="#reply-modal-{{ $comment->getKey() }}"
                    class="btn btn-sm btn-primary"><i class="fa fa-undo"></i>&nbsp;Répondre</button>
                @endcan
                @can('update', $comment)
                <button data-toggle="modal" data-target="#comment-modal-{{ $comment->getKey() }}"
                    class="btn btn-sm btn-info"><i class="fa fa-edit"></i>&nbsp; Editer</button>
                @endcan
                @can('delete', $comment)
                <a href="{{ front_route('comments.destroy', $comment->getKey()) }}"
                    data-alert="Etes vous sûr de bien vouloir supprimer ce commentaire ?"
                    data-form="#comment-delete-form-{{ $comment->getKey() }}"
                    class="btn btn-sm btn-danger "> <i class="fa fa-trash"></i>&nbsp; Supprimer</a>

                <form id="comment-delete-form-{{ $comment->getKey() }}"
                    action="{{ front_route('comments.destroy', $comment->getKey()) }}" method="POST" style="display: none;">
                    @honeypot
                    @method('DELETE')
                    @csrf
                </form>

                @endcan
            </div>

            {{-- @can('edit-comment', $comment) --}}
            <div class="modal fade" id="comment-modal-{{ $comment->getKey() }}" data-backdrop="static" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form method="POST" action="{{ front_route('comments.update', $comment->getKey()) }}">
                            @method('PUT')
                            @csrf
                            @honeypot
                            <div class="modal-header">
                                <h5 class="modal-title">Edition de commentaire</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="content">Editer votre message ici:</label>
                                    <textarea required class="form-control" name="content"
                                        rows="3">{{ $comment->comment }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase"
                                    data-dismiss="modal">Annuler</button>
                                <button type="submit"
                                    class="btn btn-sm btn-outline-success text-uppercase">Editer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- @endcan --}}

            {{-- @can('reply-to-comment', $comment) --}}
            <div class="modal fade" id="reply-modal-{{ $comment->getKey() }}" data-backdrop="static" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form method="POST" action="{{ front_route('comments.reply', $comment->getKey()) }}">
                            @csrf
                            @honeypot
                            <div class="modal-header">
                                <h5 class="modal-title">Répondre à ce commentaire</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="content">Entrer votre message ici:</label>
                                    <textarea required class="form-control" name="content" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase"
                                    data-dismiss="modal">Annuler</button>
                                <button type="submit"
                                    class="btn btn-sm btn-outline-success text-uppercase">Répondre</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- @endcan --}}

            <br />{{-- Margin bottom --}}

            {{-- Recursion for children --}}
            @if($grouped_comments->has($comment->getKey()))
                @foreach($grouped_comments[$comment->getKey()] as $child)
                    @include('administrable::front.comments._comment', [
                        'comment'          => $child,
                        'reply'            => true,
                        'grouped_comments' => $grouped_comments
                    ])
                @endforeach
            @endif

        </div>
@if(isset($reply) && $reply === true)
</div>
@else
</li>
@endif
