<?php

namespace Guysolamour\Administrable\Console;


use Illuminate\Support\Str;

class DeployCommand extends BaseCommand
{
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

        if ($this->checkIfGenerationHasAlreadyBeenDone()){
            $this->triggerError("Deployment scripts generation have already been done.");
        }

        $this->server = $this->getServer();


        $this->password = $this->option('password') ?: $this->secret('Give vault code for decrypting password file');


        if ($this->option('force')){
            $this->filesystem->deleteDirectory($this->path);
        }

        $this->filesystem->copyDirectory(
            $this->getTemplatePath() . '/deployment',
            $this->path . '/',
        );

        $this->compliedAndMoveFile();


        $this->info("Deploy scripts generated successfuly.");
    }

    protected function compliedAndMoveFile() :void
    {
        foreach (self::FILES_TO_MOVE as $file ) {
            $makefile_path = $this->path . "/tmp/{$file}";
            $makefile = $this->filesystem->compliedFile($makefile_path, true, $this->getParsedName());

            $this->filesystem->writeFile($makefile_path, $makefile);
            $this->filesystem->move($this->path . "/tmp/{$file}", base_path($file));

            if (self::VAULT_FILE === $file) {
                $this->addFileToGitIgnore($file);
            }
        }
    }

    public function getParsedName(?string $name = null): array
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

    protected function addFileToGitIgnore(string $file) :void
    {
        $this->filesystem->append(base_path('.gitignore'), $file);
    }

	private function checkIfGenerationHasAlreadyBeenDone() :bool
	{
        return $this->filesystem->exists($this->path) && !$this->option('force');
	}

	private function getServer() :string
	{
        $server = $this->option('server');

        if (!empty($server) && !filter_var($server, FILTER_VALIDATE_IP)) {
            $this->triggerError("The server ip [{$server}] is not a valid ip.");
        }

        return $server;
    }
}
