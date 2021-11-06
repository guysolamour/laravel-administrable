@imagemanager([
    'collection'        => config('administrable.media.collections.back.label'),
    'model'             =>  $model,
    'label'             =>  $label,
    'type'              =>  $file_type ?? 'image'
])
