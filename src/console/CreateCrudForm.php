<?php
namespace Guysolamour\Admin\Console;

class CreateCrudForm
{
    use MakeCrudTrait;

    /**
     * @var array
     */
    private $fields;
    /**
     * @var string
     */
    private $slug;
    /**
     * @var string
     */
    private $name;

    /**
     * CreateCrudForm constructor.
     * @param string $name
     * @param array $fields
     * @param string|null $slug
     */
    public function __construct(string $name, array $fields, ?string $slug)
    {
        $this->name = $name;
        $this->fields = array_chunk($fields,3);
        $this->slug = strtolower($slug);
    }

    /**
     * @param string $name
     * @param array $fields
     * @param string|null $slug
     * @return string
     */
    public static function generate(string $name, array $fields, ?string $slug) :string
    {
        return (new CreateCrudForm($name,$fields,$slug))
            ->loadForm();
    }

    /**
     * @return string
     */
    private function loadForm() :string
    {
        try {

            $data_map = $this->parseName($this->name);

            $form_name = $data_map['{{singularClass}}'];

            [$form_path, $complied] = $this->loadAndRegisterFormStub($form_name, $data_map);

            $this->createDirIfNotExists($form_path);

            // add fields
            $fields = $this->getFields();
            $this->registerFields($fields, $complied, $form_path);

            return $form_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * @param string $type
     * @return string
     */
    private function getType(string $type) :string
    {
        if (
            $type === 'string' || $type === 'decimal' || $type === 'double' ||
            $type === 'float')
        {
            return 'text';
        } elseif (
            $type === 'integer' || $type === 'mediumInteger')
        {
            return 'number';
        } elseif ($type === 'text' || $type === 'mediumText' || $type === 'longText') {
            return 'textarea';
        } elseif ($type === 'email') {
            return 'email';
        } elseif ($type === 'boolean' || $type === 'enum') {
            return 'checkbox';
        } elseif ($type === 'date') {
            return 'date';
        } elseif ($type === 'datetime') {
            return 'datetime';
        }else{
            return 'text';
        }
    }

    /**
     * @return string
     */
    private function getFields(): string
    {
        $fields = "\n";
        foreach ($this->fields as $field) {
            $fields .= '            ->add(' . "'{$field[0]}'" . ', ' . "'{$this->getType($field[1])}'" . ',[
                    \'rules\' => ' . "'$field[2]'" . '
                ])' . "\n";
        }
        // add slug field
        if (!is_null($this->slug)) {
            $fields .= '            ->add(' . "'{$this->slug}'" . ', ' . "'text'" . ',[
                ])' . "\n";

            $fields .= '            ->add(' . "'slug'" . ', ' . "'text'" . ',[
                ])' . "\n";
        }
        return $fields;
    }

    /**
     * @param $fields
     * @param $complied
     * @param $form_path
     */
    private function registerFields($fields, $complied, $form_path): void
    {
        $slug_mw_bait = '$this' . "\n";
        $form = str_replace($slug_mw_bait, $slug_mw_bait . $fields, $complied);
        file_put_contents($form_path, $form);
    }

    /**
     * @param $form_name
     * @param $data_map
     * @return array
     */
    private function loadAndRegisterFormStub($form_name, $data_map): array
    {
        $form_path = app_path('/Forms/Admin');

        $form_stub = $this->TPL_PATH . '/forms/form.stub';
        $form_path = $form_path . "/{$form_name}Form.php";

        $stub = file_get_contents($form_stub);
        $complied = strtr($stub, $data_map);
        return array($form_path, $complied);
    }



}
