@once
@push('js')
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
@endpush
@endonce

@push('js')
<script>
    var tinymce_config = {
        model: @json($model),
        model_name: @json($model_name ?? get_class($model)),
        prefix: @json(config('administrable.auth_prefix_path')),
    }

    tinymce.init({
        selector: @json($selector ?? '[data-tinymce]'),
        language: 'fr_FR',

        plugins: [
        "imagemanager",
        "advlist autolink lists link image autoresize autosave charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen help",
        "insertdatetime  nonbreaking save table contextmenu directionality",
        "emoticons autoresize autosave template paste textcolor colorpicker textpattern"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent forecolor backcolor | link image  colorpicker",
        relative_urls: false,
        // image_list: [
        //     {title: 'My image 1', value: 'https://www.tinymce.com/my1.gif'},
        //     {title: 'My image 2', value: 'http://www.moxiecode.com/my2.gif'}
        // ],
        image_advtab: true,
        /* enable title field in the Image dialog*/
        image_title: true,
        /* enable automatic uploads of images represented by blob or data URIs*/
        // automatic_uploads: true,
        file_picker_types: 'image',
        // images_upload_url: 'postAcceptor.php',
        paste_data_images: true,
        file_picker_callback: function (cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            /*
            Note: In modern browsers input[type="file"] is functional without
            even adding it to the DOM, but that might not be the case in some older
            or quirky browsers like IE, so you might want to add it to the DOM
            just in case, and visually hide it. And do not forget do remove it
            once you do not need it anymore.
            */

            input.onchange = function () {
                var file = this.files[0];

                var reader = new FileReader();
                reader.onload = function () {
                    /*
                    Note: Now we need to register the blob in TinyMCEs image blob
                    registry. In the next release this part hopefully won't be
                    necessary, as we are looking to handle it internally.
                    */
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);

                    /* call the callback and populate the Title field with the file name */
                    cb(blobInfo.blobUri(), { title: file.name });
                };
                reader.readAsDataURL(file);
            };

            input.click();
        },
    });

    // function example_image_upload_handler (blobInfo, success, failure, progress) {
    //     var xhr, formData;

    //     xhr = new XMLHttpRequest();
    //     xhr.withCredentials = false;
    //     xhr.open('POST', 'postAcceptor.php');

    //     xhr.upload.onprogress = function (e) {
    //         progress(e.loaded / e.total * 100);
    //     };

    //     xhr.onload = function() {
    //         var json;

    //         if (xhr.status === 403) {
    //             failure('HTTP Error: ' + xhr.status, { remove: true });
    //             return;
    //         }

    //         if (xhr.status < 200 || xhr.status >= 300) {
    //             failure('HTTP Error: ' + xhr.status);
    //             return;
    //         }

    //         json = JSON.parse(xhr.responseText);

    //         if (!json || typeof json.location != 'string') {
    //             failure('Invalid JSON: ' + xhr.responseText);
    //             return;
    //         }

    //         success(json.location);
    //     };

    //     xhr.onerror = function () {
    //         failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
    //     };

    //     formData = new FormData();
    //     formData.append('file', blobInfo.blob(), blobInfo.filename());

    //     xhr.send(formData);
    // };

    // tinymce.init({
    //     selector: 'textarea',  // change this value according to your HTML
    //     images_upload_handler: example_image_upload_handler
    // });

</script>
@endpush
