@if(get_guard()->hasRole('super-' . config('administrable.guard'), config('administrable.guard')))
    @if(isset($group) && $group)
    <button class="btn btn-secondary" data-toggle="modal" title="{{ Lang::get('administrable::messages.view.pagemeta.addgroup') }}"
        data-target="#addPageMetaDataModalGroup{{ $group->getKey() }}">
        <i class="fas fa-plus"></i> &nbsp;</button>
    @else
    <button class="btn btn-primary" data-toggle="modal" title={{ Lang::get('administrable::messages.view.pagemeta.addfield') }}"
        data-target="#addPageMetaDataModal">
        <i class="fas fa-plus"></i> &nbsp;{{ Lang::get('administrable::messages.view.pagemeta.addfield') }}</button>
    @endif

<!-- Modal -->
@if(isset($group) && $group)
<div class="modal fade" id="addPageMetaDataModalGroup{{ $group->getKey() }}" tabindex="-1" role="dialog"
    aria-labelledby="addPageMetaDataModalLabel{{ $group->getKey() }}" aria-hidden="true">
    @else
    <div class="modal fade" id="addPageMetaDataModal" tabindex="-1" role="dialog"
        aria-labelledby="addPageMetaDataModalLabel" aria-hidden="true">
        @endif
        >
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    @if(isset($group) && $group)
                    <h5 class="modal-title" id="addPageMetaDataModalLabel{{ $group->getKey() }}">
                        {{ Lang::get('administrable::messages.view.pagemeta.addfield') }}
                        : {{ $group->name }} </h5>
                    @else
                    <h5 class="modal-title" id="addPageMetaDataModalLabel">{{ Lang::get('administrable::messages.view.pagemeta.addfield') }}</h5>
                    @endif
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ back_route('pagemeta.store', $page) }}" method="post" name="addPageMeta"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body" style="max-height: 34.375rem">
                        <input type="hidden" name="page_id" value="{{ $page->getKey() }}">

                        <div class="form-group d-flex justify-content-end">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary "><i class="fa fa-save"></i>
                                    &nbsp; {{ Lang::get('administrable::messages.default.save') }}</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><i class="fa fa-times"></i> &nbsp;{{ Lang::get('administrable::messages.default.cancel') }}</span>
                                </button>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="code{{ $page->getKey() }}">{{ Lang::get('administrable::messages.view.pagemeta.code') }} <span class="text-red">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="code" id="code{{ $page->id }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="name{{ $page->getKey() }}">{{ Lang::get('administrable::messages.view.pagemeta.name') }} <span class="text-red">*</span> </label>
                                    <input type="text" class="form-control" name="name" id="name{{ $page->id }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="title{{ $page->getKey() }}">{{ Lang::get('administrable::messages.view.pagemeta.title') }} </label>
                                    <input type="text" class="form-control" name="title" id="title{{ $page->id }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="">{{ Lang::get('administrable::messages.view.pagemeta.type') }}</label>
                                    <select name="type" id="image_field{{ $page->id }}" class="custom-select">
                                        @foreach(AdminModule::model('pagemeta')::TYPES as $type)
                                        <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label for="">{{ Lang::get('administrable::messages.view.pagemeta.group') }}</label>
                                    <select name="parent_id" id="group_field{{ $page->getKey() }}" class="custom-select ">
                                        @if(isset($group) && $group)
                                        <option value="{{ $group['id'] }}" selected>{{ $group['name'] }}</option>
                                        @else
                                        @foreach($page->metatags as $group_field)
                                        <option value="{{ $group_field['id'] }}" {{
                                            (isset($group) && $group && $group['code'] === $group_field['code']) ||
                                            ($group_field['code'] === AdminModule::model('pagemeta')::DEFAULT_GROUP_CODE) ? 'selected' : ''
                                        }}>
                                            {{ $group_field['name'] }}
                                        </option>
                                        @endforeach

                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content{{ $page->getKey() }}">{{ Lang::get('administrable::messages.view.pagemeta.content') }} </label>
                            <textarea name="textcontent" id="content{{ $page->getKey() }}" class="form-control"
                                data-tinymce></textarea>
                        </div>

                        <div class="form-group" style="display: none">
                            <label for="simpletextcontent{{ $page->getKey() }}">{{ Lang::get('administrable::messages.view.pagemeta.content') }} </label>
                            <textarea name="simpletextcontent" id="simpletextcontent{{ $page->getKey() }}"
                                class="form-control"></textarea>
                        </div>
                        <div class="form-group" style="display: none">
                            <label for="imageField">{{ Lang::get('administrable::messages.view.pagemeta.chooseimage') }}</label>
                            <input type="file" accept="image/*" class="form-control" id="imageField"
                                name="imagecontent">
                        </div>
                        <div class="form-group" style="display: none">
                            <label for="videoField">{{ Lang::get('administrable::messages.view.pagemeta.videourl') }}</label>
                            <input type="url" class="form-control" id="videoField" name="videocontent"
                                placeholder="https://youtube.com?v=74df8g585">
                        </div>
                        <div class="form-group" style="display: none">
                            <label for="attachedField">{{ Lang::get('administrable::messages.view.pagemeta.otherfiles') }}</label>
                            <input type="file" class="form-control" id="attachedField" name="attachedfilecontent">
                        </div>

                        <div class="thumbnail">
                            <img src="" alt="" class="img-fluid img-thumbnail" style="height: 145px; overflow: scroll; display: none">
                            <div class="embed-responsive embed-responsive-16by9" style="display: none">
                                <iframe class="embed-responsive-item" src="" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer btn-group">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i>
                            &nbsp;{{ Lang::get('administrable::messages.default.cancel') }}</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>
                            &nbsp;{{ Lang::get('administrable::messages.default.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @push('css')
    <style>
        .tox .tox-tinymce {
            height: 270px !important;
        }
    </style>
    @endpush
