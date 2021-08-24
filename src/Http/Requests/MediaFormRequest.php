<?php

namespace Guysolamour\Administrable\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'type'       => ['required', 'string', 'in:file,image'],
            'collection' => ['required', 'string'],
            'file'       => [
                'required',
                request('type'),
                'mimetypes:' . $this->getMimesTypesRule(),
            ],
        ];

        if (config('administrable.media.should_validate_size')) {
            $rules['file'][] = 'max:' . $this->getMaxFileSize();
        }

        return $rules;
    }

    private function getMaxFileSize(): int
    {
        $type = request('type');

        return config("administrable.media.max_{$type}_size");
    }

    private function getMimesTypesRule(): string
    {
        $type = request('type');

        return join(',', config("administrable.media.valid_mimetypes.{$type}"));
    }
}
