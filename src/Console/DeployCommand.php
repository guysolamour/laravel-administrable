<?php

namespace Guysolamour\Administrable\Console;


use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class DeployCommand extends BaseCommand
{

    use CommandTrait;


    protected const VAULT_FILE = '.vault-pass';

    protected const FILES_TO_MOVE = ['Makefile', '.vault-pass'];




    protected $filesystem;

    /**
     * @var string
     */
    protected $path;



    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:deploy
                             {--d|path=.deployment : Relative to the current path }
                             {--s|server= : Server IP adress }
                             {--p|password= : Decryption password code }
                             {--f|force : Force scripts generation }
                             {--k|key : Generate Env app key }
                             ';

                             /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate deployment scripts';


    public function __construct()
    {
        parent::__construct();

        $this->filesystem = new Filesystem;
    }


    public function handle()
    {
        $this->path = base_path($this->option('path'));
        if ($this->filesystem->exists($this->path) && !$this->option('force')){
            $this->triggerError("Deployment scripts generation have already been done.");
        }

        $this->server = $this->option('server');
        if (!empty($this->server) && !filter_var($this->server, FILTER_VALIDATE_IP)){
            $this->triggerError("The server ip [{$this->server}] is not a valid ip.");
        }

        $this->password = $this->option('password') ?: $this->secret('Give vault code for decrypting password file');


        // copy directory
        if ($this->option('force')){
            $this->filesystem->deleteDirectory($this->path);
        }

        $this->filesystem->copyDirectory(
            self::TPL_PATH . '/deployment',
            $this->path . '/',
        );

        // Complied Env File
        $this->addAppKeyToEnvFile();


        $this->compliedAndMoveFile(self::FILES_TO_MOVE);


        $this->triggerSuccess("Deploy scripts generated successfuly");
    }

    /**
     * @param array $files
     * @return void
     */
    protected function compliedAndMoveFile(array $files)
    {
        foreach ($files as $file ) {
            $makefile_path = $this->path . "/tmp/{$file}";
            $makefile = $this->compliedFile($makefile_path);
            $this->writeFile($makefile, $makefile_path);
            $this->filesystem->move($this->path . "/tmp/{$file}", base_path($file));

            if (self::VAULT_FILE === $file) {
                $this->addFileToGitIgnore($file);
            }
        }
    }

    /**
     *
     * @param string|null $name
     * @return array
     */
    protected function parseName(?string $name = null): array
    {
        return [
            '{{server}}'            =>  Str::lower($this->server ?: ''),
            '{{appname}}'           =>  Str::lower(config('app.name', '')),
            '{{path}}'              =>  Str::lower($this->path),
            '{{appurl}}'            =>  Str::lower(config('app.url', '')),
            '{{appfirstname}}'      =>  Str::lower(config('app.first_name', '')),
            '{{applastname}}'       =>  Str::lower(config('app.last_name', '')),
            '{{ftphost}}'           =>  Str::lower(config('filesystems.disks.ftp.host', '')),
            '{{ftpusername}}'       =>  Str::lower(config('filesystems.disks.ftp.username', '')),
            '{{notifemail}}'        =>  Str::lower(config('mail.from.address', '')),
            '{{vaultcode}}'         =>  Str::lower($this->password ?: ''),
        ];
    }


    protected function addAppKeyToEnvFile()
    {
        if (!$this->option('key')){
            return;
        }

        $data_map = array_merge($this->parseName(), ['{{ vault_app_key }}'   =>  $this->getAppKey()]);

        $env_path = $this->path . '/templates/env.j2';
        $env_stub = $this->compliedFile($env_path, true, $data_map);

        $this->writeFile($env_stub, $this->path . '/templates/env.j2');
    }

    protected function getAppKey() :string
    {
        Artisan::call('key:generate --show');

       return str_replace(PHP_EOL,'', Artisan::output());
    }

    protected function addFileToGitIgnore(string $file)
    {
        $this->filesystem->append(base_path('.gitignore'), $file);
    }


}
