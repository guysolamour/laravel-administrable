@imagemanager([
    'collection'        => config('administrable.media.collections.back.label'),
    'model'             =>  $model,
    'label'             =>  $label ?? config('administrable.media.collections.back.description'),
    'type'              =>  $file_type ?? 'image'
])
