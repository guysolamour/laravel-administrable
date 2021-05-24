<?php

namespace Guysolamour\Administrable\Console\Administrable;

use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\BaseCommand;
use Guysolamour\Administrable\Console\YamlTrait;
use Illuminate\Database\Eloquent\Model;

class CreateAdministrableCommand extends BaseCommand
{
    use YamlTrait;

    /** * @var string|null  */
    protected $guard = null;

    /** @var int  */
    protected const PASSWORD_MIN_LENGTH = 8;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:create
                                {--u|username= : Guard username }
                                {--p|password= : Guard password }
                                {--e|email=    : Guard email }
                            ';


    protected $description = 'Create guard entry in database';



    private function getGuard() :string
    {
        return config('administrable.guard', 'admin');
    }

    private function getUsername() :string
    {
        $username = $this->option('username');

        if (!$username) {
            $username = $this->ask("Give guard username");
        }

        while (empty($username)) {
             $username = $this->ask("Give guard username");
        }

        return $username;
    }

    private function getEmail() :string
    {
        $email = $this->option('email');

        if (!$email) {
            $email = $this->ask("Give guard email");
        }

        while (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error(
                sprintf("The given email [%s] is not a valid email", $email)
            );
            $email = $this->ask("Give guard email");
        }

        return $email;
    }

    private function getPassword() :string
    {
        $password = $this->option('password');

        if (!$password) {
            $password = $this->secret(sprintf("Give guard password (%d digits)", self::PASSWORD_MIN_LENGTH));
        }

        while (empty($password) || Str::length($password) < self::PASSWORD_MIN_LENGTH) {
            $this->error("The password must be more or equal to " . self::PASSWORD_MIN_LENGTH . " characters");
            $password = $this->secret(sprintf("Give guard password (%d digits)", self::PASSWORD_MIN_LENGTH));
        }


        return $password;
    }

    public function handle()
    {
        $this->guard = $this->getGuard();

        // Check if package was installed
        if (!$this->checkIfPackageHasBeenInstalled()) {;
            throw new \Exception("The installation must be done before using this command. Please run [administrable:install] command.");
        }

        $username = $this->getUsername();
        $email    = $this->getEmail();
        $password = $this->getPassword();

        $this->createGuardEntry($username, $email, $password);
    }

    private function createGuardEntry(string $username, string $email, string $password) :void
    {
        $model = $this->getModelInstance();

        $model->pseudo      = $username;
        $model->email       = $email;
        $model->first_name  = $username;
        $model->last_name   = $username;
        $model->password    = $password;

        $model->save();

        $this->info(
            sprintf("The guard with [%s] username (pseudo) was successfully created. You can now log in to the back office.", $username)
        );
    }

    protected function getModel(): string
    {
        return Str::singular(Str::studly($this->guard));
    }

    private function getModelInstance(): Model
    {
        $model =  sprintf("%s\%s\%s", $this->getAppNamespace(), $this->getModelsFolder(), $this->getModel());

        return new $model;
    }
}
