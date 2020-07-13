<?php

namespace Guysolamour\Administrable\Console;

use Illuminate\Support\Str;

class CreateAdministrableCommand extends BaseCommand
{

    use CommandTrait;

    /**
     * @var string|null
     */
    protected $guard = null;



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


    public function __construct()
    {
        parent::__construct();

        

        $this->guard = $this->getGuard();
    }






    public function handle()
    {
        // Check if package was installed
        if ($this->checkIfPackageHasBeenInstalled()) {
            throw new \Exception("The installation must be done before using this command. Please run [administrable:install] command.");
        }
        

        $username = $this->option('username');
        if (!$username) {
            $username = $this->ask("Give guard username");
        }

        $email = $this->option('email');

        if (!$email) {
            $email = $this->ask("Give guard email");
        }

        $password = $this->option('password');
        if (!$password) {
            $password = $this->secret("Give guard password");
        }

        // Validate data
        if (empty($username)) {
            $this->error("The given pseudo (username) [%s] can not be empty");
            return;
        }

        if (empty($email)) {
            $this->error("The given email can not be empty");
            return;
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error(
                sprintf("The given email [%s] is not a valid email", $email)
            );
            return;
        }

        if (empty($password)) {
            $this->error("The password can not be empty");
            return;
        } else if (Str::length($password) <= 7) {
            $this->error("The password must be more than 7 characters");
            return;
        }

        $this->createGuardEntry($username, $email, $password);
    }

    /**
     * Register guard entry in database
     *
     * @param string $username
     * @param string $email
     * @param string $password
     * @return void
     */
    private function createGuardEntry(string $username, string $email, string $password)
    {
        $model = $this->getModelWithNamespace();
        $guard = new $model;

        $guard->pseudo      = $username;
        $guard->email       = $email;
        $guard->first_name  = $username;
        $guard->last_name   = $username;
        $guard->password    = $password;

        $guard->save();

        $this->info(
            sprintf("The guard with [%s] username (pseudo) was successfully created. You can now log in to the back office.", $username)
        );
    }


    /**
     * @return string
     */
    private function getModel(): string
    {
        return Str::singular(Str::studly($this->guard));
    }

    /**
     * @return string
     */
    private function getModelsFolder(): string
    {
        return $this->getCrudConfiguration('folder', 'Models');
    }

    /**
     * @return string
     */
    private function getModelWithNamespace(): string
    {
        return sprintf("%s\%s\%s", $this->getNamespace(), $this->getModelsFolder(), $this->getModel());
    }
}
