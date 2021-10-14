<?php

namespace Guysolamour\Administrable\Console;


use Illuminate\Support\Str;

class DeployCommand extends BaseCommand
{

    private $filesystem;

    /**
     * @var string
     */
    private $password;



    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:deploy:scripts
                             {--s|server= : Server IP adress }
                             {--p|vault=deploy-vault-pass : File name to decrypt ansible protected variables }
                             ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate deployment scripts';


    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }


    public function handle()
    {
        if ($this->checkIfGenerationHasAlreadyBeenDone()){
            $this->triggerError("Scripts has already been generated. You can delete all files and run this command again!");
        }

        $this->password =  $this->secret('Give vault code for decrypting password file');

        $this->createTemporaryFolder();

        $this->compliedAndMoveScriptsFile();

        $this->addPathFolderToGitignore();

        $this->info("Deploy scripts generated successfuly.");
    }


    private function createTemporaryFolder() :void
    {
        $this->filesystem->createDirectoryIfNotExists(storage_path('app/deploy/tmp'));
    }

    private function compliedAndMoveScriptsFile() :void
    {
        $files = $this->filesystem->files($this->getTemplatePath() . '/deployment', true);

        foreach ($files as $file ) {
            $content = $this->filesystem->compliedFile($file->getRealPath(), true, $this->getParsedName());

            $this->filesystem->writeFile(base_path() . DIRECTORY_SEPARATOR . $file->getRelativePathname(), $content);
        }
    }

    public function getParsedName(?string $name = null): array
    {
        return [
            '{{server}}'            =>  Str::lower($this->getServer() ?: ''),
            '{{appname}}'           =>  Str::lower(config('app.name', '')),
            '{{appurl}}'            =>  Str::afterLast(Str::lower(config('app.url', '')), '//'),
            '{{appfirstname}}'      =>  Str::lower(config('administrable.app_first_name', '')),
            '{{applastname}}'       =>  Str::lower(config('administrable.app_last_name', '')),
            '{{ftphost}}'           =>  Str::lower(config('filesystems.disks.ftp.host', '')),
            '{{ftpusername}}'       =>  Str::lower(config('filesystems.disks.ftp.username', '')),
            '{{notifemail}}'        =>  Str::lower(config('mail.from.address', '')),
            '{{vaultcode}}'         =>  $this->password ?: '',
            '{{vault}}'             =>  $this->option('vault'),
        ];
    }

    private function addPathFolderToGitignore() :void
    {
        $this->filesystem->append(base_path('.gitignore'), DIRECTORY_SEPARATOR .  'deploy-passwords.yml' . PHP_EOL . $this->option('vault') );
    }

	private function checkIfGenerationHasAlreadyBeenDone() :bool
	{
        return $this->filesystem->exists(base_path('deploy.sh'));
	}

	private function getServer() :?string
	{
        $server = $this->option('server');

        if (!empty($server) && !filter_var($server, FILTER_VALIDATE_IP)) {
            $this->triggerError("The server ip [{$server}] is not a valid ip v4 adress.");
        }

        return $server;
    }
}
