<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Filesystem\Filesystem;

class CreateCrudForm
{
    use MakeCrudTrait;

    /**
     * @var string
     */
    private $model;
    /**
     * @var array
     */
    private $fields;
    /**
     * @var array
     */
    private $actions;
    /**
     * @var string
     */
    private $breadcrumb;
    /**
     * @var string
     */
    private $theme;
    /**
     * @var string
     */
    private $slug;
    /**
     * @var bool
     */
    private $timestamps;
    /**
     * @var bool
     */
    private $entity;
    /**
     * @var bool
     */
    private $seeder;
    /**
     * @var bool
     */
    private $edit_slug;


    /**
     * @param string $model
     * @param array $fields
     * @param array $actions
     * @param string|null $breadcrumb
     * @param string $theme
     * @param string|null $slug
     * @param boolean $timestamps
     * @param boolean $entity
     * @param boolean $seeder
     */
    public function __construct(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, ?string $slug, bool $timestamps, bool $entity, bool $seeder, bool $edit_slug)
    {
        $this->model       = $model;
        $this->fields      = $fields;
        $this->actions     = $actions;
        $this->slug        = $slug;
        $this->breadcrumb  = $breadcrumb;
        $this->theme       = $theme;
        $this->slug        = $slug;
        $this->timestamps  = $timestamps;
        $this->entity      = $entity;
        $this->seeder      = $seeder;
        $this->edit_slug   = $edit_slug;

        $this->filesystem  = new Filesystem;
    }

    /**
     * @param string $model
     * @param array  $fields
     * @param array  $actions
     * @param string|null $breadcrumb
     * @param string $theme
     * @param string|null $slug
     * @param boolean $timestamps
     * @param boolean $entity
     * @param boolean $seeder
     * @param boolean $edit_slug
     * @return void
     */
    public static function generate(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, ?string $slug, bool $timestamps, bool $entity, bool $seeder, bool $edit_slug)
    {
        return (new CreateCrudForm($model, $fields, $actions, $breadcrumb, $theme, $slug, $timestamps, $entity, $seeder, $edit_slug))
            ->loadForm();
    }

    /**
     * @return string
     */
    private function loadForm(): string
    {
        if ($this->hasAction('create') || $this->hasAction('edit')) {
            $data_map = $this->parseName($this->model);
            $form_name = $data_map['{{singularClass}}'];

            $form_path = app_path("/Forms/" . $data_map['{{modelsFolder}}']);
            $form_stub = $this->TPL_PATH . '/forms/form.stub';
            $form_path = $form_path . "/{$form_name}Form.php";

            $complied = $this->compliedFile($form_stub, true, $data_map);

            // add fields
            $fields = $this->getFormFields();

            [$form_path, $complied] = $this->loadAndRegisterFormStub($form_name, $data_map);

            $this->registerFormFields($fields, $complied, $form_path);


            return $form_path;
        }

        return '';
    }









    /**
     * @param $form_name
     * @param $data_map
     * @return array
     */
    private function loadAndRegisterFormStub($form_name, $data_map): array
    {
        $form_path = app_path('Forms/' . $data_map['{{backNamespace}}']);

        $form_stub = $this->TPL_PATH . '/forms/form.stub';
        $form_path = $form_path . "/{$form_name}Form.php";

        $complied = $this->compliedFile($form_stub,  true, $data_map);

        return array($form_path, $complied);
    }


}
