@imagemanager([
    'collection'        => config('administrable.media.collections.front.label'),
    'model'             =>  $model,
    'label'             =>  $label,
    'type'              =>  $file_type ?? 'image'
])
