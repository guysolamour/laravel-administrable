<!-- Modal -->

<div class="modal fade" id="editMetaModal{{ $meta->getKey() }}" tabindex="-1" role="dialog"
    aria-labelledby="addPageMetaDataModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPageMetaDataModalLabel">{{ Lang::get('administrable::messages.default.edition') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ back_route('pagemeta.update', [$page, $meta]) }}" method="post" name="addPageMeta"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="max-height: 34.375rem">
                    <input type="hidden" name="page_id" value="{{ $page->getKey() }}">

                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary "><i class="fa fa-save"></i>
                            &nbsp;{{ Lang::get('administrable::messages.default.save') }}</button>
                    </div>
                    <div class="form-row">

                        <div class="col">
                            <div class="form-group">
                                <label>{{ Lang::get('administrable::messages.view.pagemeta.code') }} <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="code" value="{{ $meta->code }}" readonly>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>{{ Lang::get('administrable::messages.view.pagemeta.name') }} <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $meta->name }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ Lang::get('administrable::messages.view.pagemeta.title') }} </label>
                                <input type="text" class="form-control" name="title" value="{{ $meta->title }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Lang::get('administrable::messages.view.pagemeta.type') }}</label>
                                <select name="type" class="custom-select" disabled>
                                    @foreach(AdminModule::model('pagemeta')::TYPES as $type)
                                    <option value="{{ $type['value'] }}"
                                        {{ $type['value'] == $meta->type ? 'selected' : '' }}>{{ $type['label'] }}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="type" value="{{ $meta->type }}">
                            </div>
                        </div>
                        @if($meta->parent)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Lang::get('administrable::messages.view.pagemeta.group') }}
                                    @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                        @if ($meta->parent)
                                        | {{ $meta->parent->code }}
                                        @endif
                                    @endrole
                                </label>
                                <select name="parent_id" class="custom-select" disabled>
                                    <option value="{{ $meta->parent->getKey() }}" selected>
                                        {{ $meta->parent->title }}
                                    </option>
                                </select>
                                <input type="hidden" name="parent_id" value="{{ $meta->parent->getKey() }}">
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="form-group" style="display: {{ $meta->isText() ? 'block' : 'none' }}">
                        <label for="content{{ $page->id }}">{{ Lang::get('administrable::messages.view.pagemeta.content') }} </label>
                        <textarea name="textcontent" id="content{{ $page->id }}" class="form-control"
                            data-tinymce>@if($meta->isText()){{ $meta->content }}@endif</textarea>
                    </div>

                    <div class="form-group" style="display: {{ $meta->isSimpleText() ? 'block' : 'none' }}">
                        <label for="simpletextcontent{{ $page->id }}">{{ Lang::get('administrable::messages.view.pagemeta.content') }} </label>
                        <textarea name="simpletextcontent" id="simpletextcontent{{ $page->id }}"
                            class="form-control">@if($meta->isSimpleText()){{ $meta->content }}@endif</textarea>
                    </div>


                    <div class="form-group" style="display: {{ $meta->isImage() ? 'block' : 'none' }}">
                        <label for="imageField">{{ Lang::get('administrable::messages.view.pagemeta.chooseimage') }}</label>
                        <input type="file" accept="image/*" class="form-control" id="imageField" name="imagecontent">
                        <input type="hidden" name="imagecontent" value="{{ $meta->content }}">
                    </div>
                    <div class="form-group" style="display: {{ $meta->isAttachedFile() ? 'block' : 'none' }}">
                        <label for="attachedField">{{ Lang::get('administrable::messages.view.pagemeta.otherfiles') }}</label>
                        <input type="file" class="form-control" id="attachedField" name="attachedfilecontent">
                        <input type="hidden" name="videocontent" value="{{ $meta->content }}">
                    </div>
                    <div class="form-group" style="display: {{ $meta->isVideo() ? 'block' : 'none' }}">
                        <label for="videoField">{{ Lang::get('administrable::messages.view.pagemeta.videourl') }}</label>
                        <input type="url" class="form-control" id="videoField" name="videocontent"
                            placeholder="https://youtube.com?v=74df8g585" value="{{ $meta->video_url }}">
                    </div>
                    <div class="thumbnail" >
                        <img  src="{{ $meta->isImage() ? $meta->image_url : '' }}" class="img-fluid img-thumbnail"
                            style="height: 145px; overflow: scroll; display: {{ $meta->isImage() ? 'block' : 'none' }}">
                        <div class=" embed-responsive embed-responsive-16by9"
                            style="display: {{ $meta->isVideo() ? 'block' : 'none' }}"">
                                <iframe class=" embed-responsive-item"
                                    src="{{ $meta->isVideo() ? $meta->video_url : '' }}" allowfullscreen>
                                </iframe>
                        </div>
                </div>

        </div>
        <div class="modal-footer btn-group">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-undo"></i>
                &nbsp;{{ Lang::get('administrable::messages.default.cancel') }}</button>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> &nbsp;{{ Lang::get('administrable::messages.default.save') }}</button>
        </div>
        </form>
    </div>
</div>
</div>
@push('css')
<style>
    .tox .tox-tinymce {
        height: 270px !important;
    }
</style>
@endpush
