<?php

namespace Guysolamour\Administrable\Console\Crud\Generate;

use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\Crud\Field;


class GenerateSeed extends BaseGenerate
{
    public function run()
    {
        if (
            !$this->crud->generateFieldIsAllowedFor($this) ||
            !$this->crud->getSeeder()
            ) {
            return [false, 'Skip creating seed'];
        }

        $seeder_stub = $this->crud->getCrudTemplatePath('/migrations/seed.stub');
        $seeder_path = $this->getPath();
        $seeder = $this->crud->filesystem->compliedFile($seeder_stub, true, $this->data_map );

        $seeder = $this->generateSeederFields($seeder);

        $this->registerSeederInDatabaseSeeder();

        $this->crud->filesystem->writeFile(
            $seeder_path,
            $seeder,
        );

        return [$seeder, $seeder_path];
    }

    protected function generateSeederFields(string $seeder): string
    {
        $seed_fields = PHP_EOL;

        foreach ($this->crud->getFields() as $field) {

            if ($field->isSimpleManyToManyRelation() || $field->isPolymorphicRelation()){
                continue;
            }

            if ($field->isDaterange()) {
                $seed_fields .= $this->getDaterangeFakerType($field);
            } else {
                $seed_fields .= $this->getSeederFieldStub($field);
            }
        }
        $search = 'create([';
        
        return str_replace($search, $search . $seed_fields, $seeder);
    }

    protected function getSeederFieldStub(Field $field): string
    {
        $faker_type = $this->getSeederFieldFakerType($field->getType(), $field->getLength());

        if (empty($faker_type)) {
            $faker_type = $this->getSeederFieldFakerType($field->getName(), $field->getLength());
        }
        // default value
        if (empty($faker_type)) {
            $faker_type = '$faker->text()';
        }

        return $this->getSeederFieldStubTemplate($field->getName(), $faker_type);
    }

    protected function registerSeederInDatabaseSeeder() :string
    {

        $database_seeder_path = database_path('seeders/DatabaseSeeder.php');

        $search = "    {";

        foreach ($this->crud->getFields() as $field) {
            if ($field->isRelation()) {
                $related_model = Str::plural($field->getRelatedModelWithoutNamespace());
                $search = ' $this->call(' .  $related_model . 'TableSeeder::class' . ");";
            }
        }

        $database_seeder = $this->crud->filesystem->get($database_seeder_path);
        $seeder = $this->data_map['{{pluralClass}}'] . 'TableSeeder::class';

        $this->crud->filesystem->replaceAndWriteFile(
            $database_seeder,
            $search,
            <<<TEXT
            $search
                     \$this->call($seeder);
            TEXT,

            $database_seeder_path
        );

        return $database_seeder_path;
    }

    protected function getSeederFieldFakerType(string $key, ?int $length = null): string
    {
        switch ($key) {
            case 'string':
                $type = $length ? '$faker->text(' . $length . ')' : '$faker->text(50)';
                break;
            case 'json':
                $type = $length ? 'json_encode($faker->text(' . $length . ')' : 'json_encode($faker->text(150)';
                break;
            case 'image':
                $type = '$faker->imageUrl';
                break;
            case 'date':
                $type = '$faker->datetime';
                break;
            case 'datetime':
                $type = '$faker->datetime';
                break;
            case 'integer':
                $type = $length ? '$faker->numberBetween(0, ' . $length . ')' : '$faker->randomNumber()';
                break;
            case 'mediumInteger':
                $type = $length ? '$faker->numberBetween(0, ' . $length . ')' : '$faker->randomNumber()';
                break;
            case 'bigInteger':
                $type = $length ? '$faker->numberBetween(0, ' . $length . ')' : '$faker->randomNumber()';
                break;
            case 'text':
                $type = $length ? '$faker->realText(' . $length  . ')' : '$faker->realText(150)';
                break;
            case 'name':
                $type = '$faker->name';
                break;
            case 'city':
                $type = '$faker->city';
                break;
            case 'bool':
                $type = '$faker->randomElement([true, false])';
                break;
            case 'boolean':
                $type = '$faker->randomElement([true, false])';
                break;
            case 'phone_number':
                $type = '$faker->e164PhoneNumber';
                break;
            case 'country':
                $type = '$faker->country';
                break;
            case 'address':
                $type = '$faker->address';
                break;
            case 'first_name':
                $type = '$faker->firstName';
                break;
            case 'last_name':
                $type = '$faker->lastName';
                break;
            case 'job':
                $type = '$faker->jobTitle';
                break;
            case 'url':
                $type = '$faker->url';
                break;
            case 'color':
                $type = '$faker->hexColor';
                break;
            default:
                $type = ''; // important de le laisser vide
                break;
        }

        return $type;
    }

    protected function getDaterangeFakerType(Field $field): string
    {
        return <<<HTML
            {$this->getSeederFieldStubTemplate($field->getDateRangeStartFieldName(), 'now()')}{$this->getSeederFieldStubTemplate($field->getDateRangeEndFieldName(), 'now()->addMonth()')}
        HTML;
    }

    protected function getSeederFieldStubTemplate(string $name, string $faker_type): string
    {
        return PHP_EOL . "                '{$name}'  => " . "{$faker_type},";
    }


    protected function getPath() :string
    {
        return database_path("seeders/" . $this->data_map['{{pluralClass}}'] . 'TableSeeder.php');
    }

    public function getParsedName(?string $name = null): array
    {
        return array_merge($this->crud->getParsedName($name), [
            '{{seedCount}}'  =>  $this->crud->getSeeder(),
        ]);
    }

}
