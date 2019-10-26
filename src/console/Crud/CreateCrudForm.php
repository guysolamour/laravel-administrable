<?php
namespace Guysolamour\Administrable\Console\Crud;

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
        $this->fields = $fields;
        $this->slug = $slug;
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
     * @return string
     */
    private function getFields(): string
    {
        $fields = "\n";
        foreach ($this->fields as $field) {
            // on doit ajouter les rules si ces derniers ne sont pas vides

            if ($this->isRelationField($field['type'])){
                $fields .= '            ->add(' . "'{$this->getFieldType($field['name'])}'" . ', ' . "'{$this->getType($field['type'])}'" . ',[
                    "class" => \\' . "{$field['type']['relation']['model']}::class," . '
                    "property" => \'' . "{$field['type']['relation']['property']}'," . '
                    "label" => \'' . "{$this->getRelationModelWithoutId(ucfirst($field['name']))}'," . '
                    "rules" => ' . "'required'," . '
                    "query_builder" => ' . "function(\\".$field['type']['relation']['model']. ' $' .strtolower($this->modelNameWithoutNamespace($field['type']['relation']['model'])) . "){
                        return $". strtolower($this->modelNameWithoutNamespace($field['type']['relation']['model'])) .";
                    }"

                    . '
                    
                ])' . "\n";
            }
            else if (!empty($field['rules'])){
                $fields .= '            ->add(' . "'{$this->getFieldType($field['name'])}'" . ', ' . "'{$this->getType($field['type'])}'" . ',[
                    \'rules\' => ' . "'{$this->getRules($field['rules'])}'" . '
                ])' . "\n";
            }else {
                $fields .= '            ->add(' . "'{$this->getFieldType($field['name'])}'" . ', ' . "'{$this->getType($field['type'])}'" . ',[
                ])' . "\n";
            }

        }
        // add slug field
        if (!is_null($this->slug)) {
//            $fields .= '            ->add(' . "'{$this->slug}'" . ', ' . "'text'" . ',[
//                ])' . "\n";

            $fields .= '            ->add(' . "'slug'" . ', ' . "'text'" . ',[
                ])' . "\n";
        }
        return $fields;
    }

    private function getRules(string $rule) :string
    {
        if ($rule === 'req'){
            return 'required';
        }
        return $rule;
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

    private function getFieldType(string $field) :string
    {
        if ($field === 'image') {
            return 'file';
        }
        return strtolower($field);
    }



}
