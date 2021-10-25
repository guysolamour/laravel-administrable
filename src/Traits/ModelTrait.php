<?php

namespace Guysolamour\Administrable\Traits;

use Illuminate\Support\Str;



trait ModelTrait
{
    public function getRelatedForm(): string
    {
        if ($this->form) {
            return $this->form;
        }

        return $this->guestRelatedForm();
    }

    private function guestRelatedForm() :string
    {
        $form = $this->getFormInAppDirectory();

        if (class_exists($form)){
            return $form;
        }

        $form = $this->getFormInPackageDirectory();

        if (class_exists($form)){
            return $form;
        }

        return $form;
    }


    private function getFormInAppDirectory() :string
    {
        $form = Str::afterLast(get_class($this), config('administrable.models_folder') . '\\');

        return sprintf('\%s\Forms\%s\%sForm', get_app_namespace(), config('administrable.back_namespace'),  $form);
    }


    private function getFormInPackageDirectory() :string
    {
        $form = Str::afterLast(get_class($this), config('administrable.models_folder') . '\\');

        return sprintf('\Guysolamour\Administrable\Forms\%s\%sForm',  config('administrable.back_namespace'),  $form);
    }

    /**
     * Guest form name
     *
     * @return string
     */
    public function getFormNameAttribute(): string
    {
        return get_form_name($this);
    }


    /**
     * @param string $email
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function findByEmail($email)
    {
        return static::where('email', $email)->first();
    }

    /**
     * Get formated date
     *
     * @return string
     */
    public function getFormatedDateAttribute(): string
    {
        return $this->created_at->format('d/m/Y h:i');
    }


    /**
     * Get FormBuilder class name
     *
     * @return string
     */
    public function getFormClassName()
    {
        return get_form_class_name($this);
    }
}
