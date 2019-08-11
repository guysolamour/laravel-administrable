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

    private function loadForm() :string
    {
        try {

            $data_map = $this->parseName($this->name);

            $form_name = $data_map['{{singularClass}}'];

            $form_path = app_path('/Forms/Admin');

            $form_stub = $this->TPL_PATH.'/forms/form.stub';
            $form_path = $form_path . "/{$form_name}Form.php";

            $stub = file_get_contents($form_stub);
            $complied = strtr($stub, $data_map);

            $dir = dirname($form_path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // add fields
            $fields = "\n";
            foreach ($this->fields as $field) {
                $fields .= '            ->add('."'{$field[0]}'".', '. "'{$this->getType($field[1])}'" .',[
                    \'rules\' => '. "'$field[2]'" .'
                ])'."\n";
            }
            // add slug field
            if (!is_null($this->slug)) {
                $fields .= '            ->add('."'{$this->slug}'".', '. "'text'" .',[
                ])'."\n";

                $fields .= '            ->add('."'slug'".', '. "'text'" .',[
                ])'."\n";
            }

            $slug_mw_bait = '$this' . "\n";
            $form = str_replace($slug_mw_bait, $slug_mw_bait . $fields, $complied);
            //dd($form);
            file_put_contents($form_path, $form);

           // file_put_contents($form_path, $complied);


            return $form_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    private function getType(string $type) :string
    {
//        $htmlTypes = [
//            'text' => ['string','decimal','double','float','ipAdress'],
//            'number' => ['integer','mediumInteger'],
//            'textarea' => ['text','mediumText','longText'],
//            'email' => ['email'],
//            'checkbox' => ['boolean','enum'],
//            'date' => ['date'],
//            'datetime' => ['datetime'],
//        ];

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

}
