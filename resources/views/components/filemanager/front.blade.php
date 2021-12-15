@imagemanager([
    'collection'        => config('administrable.media.collections.front.label'),
    'model'             =>  $model,
    'label'             =>  $label  ?? config('administrable.media.collections.front.description'),
    'type'              =>  $file_type ?? 'image'
])
