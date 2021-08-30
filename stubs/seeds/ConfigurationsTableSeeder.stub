<?php
namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigurationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        /**
        * @var \Guysolamour\Administrable\Settings\BaseSettings
        */
        $configuration = config('administrable.modules.configuration.model')::get();

        $configuration->update([
            'about'    => $faker->realText(150),
            'email'    => $faker->safeEmail,
            'postal'   => $faker->postcode,
            'area'     => $faker->city,
            'cell'     => $faker->phoneNumber,
            'phone'    => $faker->phoneNumber,
            'youtube'  => $faker->url,
            'facebook' => $faker->url,
            'twitter'  => $faker->url,
            'linkedin' => $faker->url,
            'whatsapp' => "00000",
            'logo'     => "00000",
        ]);

        DB::table('options')->insert([
            'name'  => 'app_paid',
            'value' =>  '1',
        ]);

        DB::table('options')->insert([
            'name'  => 'app_notpaid_default_message',
            'value' => "Votre site a été mis hors service pour cause de non paiement. Si vous êtes le propriétaire, prière de contacter le concepteur pour régulariser la situation.",
        ]);
    }
}
