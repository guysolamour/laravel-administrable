<?php
namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Support\Str;

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
    private $model;

    /**
     * CreateCrudForm constructor.
     * @param string $model
     * @param array $fields
     * @param string|null $slug
     */
    public function __construct(string $model, array $fields, ?string $slug)
    {
        $this->model = $model;
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

            $data_map = $this->parseName($this->model);

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
                //dd(Str::plural(Str::slug($this->modelNameWithoutNamespace($this->getRelatedModel($field)))));

                if ($this->isMorphsFIeld($field)){
                    if ($this->isImagesMorphRelation($field)){
                    $morph_field_name = $this->getMorphFieldName($field);
                    $fields .= '            ->add(' . "'{$morph_field_name}'" . ', ' . "'hidden'" . ',[
                    \'label\' => ' . "'{$morph_field_name}'"   . ',
                    \'rules\' => ' . "'required'," . '
                    \'attr\' => ' . "['id' => '{$morph_field_name}', 'class' => '{$morph_field_name}']" . '
               ])' . "\n";
                    }
                }else {
                    $fields .= '            ->add(' . "'{$this->getFieldName($field['name'])}'" . ', ' . "'{$this->getType($field['type'])}'" . ',[
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
            }
            else if($field['name'] === 'image') {
                $fields .= '            ->add(' . "'{$this->getFieldName($field['name'])}'" . ', \'hidden\',[
                \'label\' => ' . "'{$this->getFieldName($field['name'])}'"   . ',
                '. $this->getRules($field['rules']) .'
                \'attr\' => [\'id\' => \''. $this->getFieldName($field['name']) .'\'],
                ])' . "\n";
            }
            else {
                $fields .= '            ->add(' . "'{$this->getFieldName($field['name'])}'" . ', ' . "'{$this->getType($field['type'])}'" . ',[
                \'label\' => '. "'{$this->getFieldName($field['name'])}'"   .',
                '. $this->getRules($field['rules']) .'
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
            $rule =  'required';
        }
        return !empty($rule) ? "'rules' => '$rule'," : $rule;
    }

    /**
     * @param $fields
     * @param $complied
     * @param $form_path
     */
    private function registerFields($fields, $complied, $form_path): void
    {
        $search = '$this' . "\n";
        $form = str_replace($search, $search . $fields, $complied);
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

    private function getFieldName(string $field) :string
    {
        return strtolower($field);
    }



}
