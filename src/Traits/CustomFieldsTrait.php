<?php

namespace Guysolamour\Administrable\Traits;

use Illuminate\Support\Arr;


trait CustomFieldsTrait
{
    private function getCustomFieldName() :string
    {
        return $this->custom_attributes_name ?? 'custom_fields';
    }


    public function hasCustomField(string $fieldName): bool
    {
        $name = $this->getCustomFieldName();

        return Arr::has($this->$name, $fieldName);
    }

    /**
     * Get the value of custom property with the given name.
     *
     * @param string $propertyName
     * @param mixed $default
     *
     * @return mixed
     */
    public function getCustomField(string $fieldName, $default = null)
    {
        $name = $this->getCustomFieldName();

        return Arr::get($this->$name, $fieldName, $default);
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return $this
     */
    public function setCustomField(string $name, $value): self
    {
        $name = $this->getCustomFieldName();

        $customFields = $this->$name;

        Arr::set($customFields, $name, $value);

        $this->custom_fields = $customFields;

        return $this;
    }


    public function forgetCustomField(string $name): self
    {
        $name = $this->getCustomFieldName();

        $customFields = $this->$name;

        Arr::forget($customFields, $name);

        $this->$name = $customFields;

        return $this;
    }
}
