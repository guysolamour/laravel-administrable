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
        $form = Str::afterLast(get_class($this), config('administrable.models_folder') . '\\');

        return sprintf('\%s\Forms\%s\%sForm', get_app_namespace(), config('administrable.back_namespace'),  $form);
    }

    public function getViewsFolder()
    {
        if ($this->views_folder) {
            return $this->views_folder;
        }

        return $this->guestViewsFolder();
    }

    private function guestViewsFolder(): string
    {
        return Str::plural(Str::slug(class_basename($this)));
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
