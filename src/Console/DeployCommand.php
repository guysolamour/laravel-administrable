<?php

namespace Guysolamour\Administrable\Console;


use Illuminate\Filesystem\Filesystem;


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
        if ($this->filesystem->exists($this->path)){
            $this->triggerError("Deployment scripts generation have already been done.");
        }

        $this->server = $this->option('server');
        if (!filter_var($this->server, FILTER_VALIDATE_IP)){
            $this->triggerError("The server ip [{$this->server}] is not a valid ip.");
        }

        $this->password = $this->option('password') ?: $this->secret('Give vault code for decrypting password file');


        // copy directory
        $this->filesystem->copyDirectory(
            self::TPL_PATH . '/deployment',
            $this->path . '/',
        );


        $this->compliedAndMoveFile(self::FILES_TO_MOVE);


        $this->triggerSuccess("Deploy scripts generate successfuly");
    }

    /**
     * @param array $files
     * @return void
     */
    protected function compliedAndMoveFile(array $files)
    {
        foreach ($files as $file ) {
            $makefile_path = $this->path . "/{$file}";
            $makefile = $this->compliedFile($makefile_path);
            $this->writeFile($makefile, $makefile_path);
            $this->filesystem->move($this->path . "/{$file}", base_path($file));

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
            '{{server}}'            =>  $this->server ?: '',
            '{{appname}}'           =>  config('app.name', ''),
            '{{appurl}}'            =>  config('app.url', ''),
            '{{appfirstname}}'      =>  config('app.first_name', ''),
            '{{applastname}}'       =>  config('app.last_name', ''),
            '{{ftphost}}'           =>  config('filesystems.disks.ftp.host', ''),
            '{{ftpusername}}'       =>  config('filesystems.disks.ftp.username', ''),
            '{{notifemail}}'        =>  config('mail.from.address', ''),
            '{{vaultcode}}'         =>  $this->password ?: '',
        ];
    }

    protected function addFileToGitIgnore(string $file)
    {
        $this->filesystem->append(base_path('.gitignore'), $file);
    }


}
