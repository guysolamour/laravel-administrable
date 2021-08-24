<?php

namespace Guysolamour\Administrable\Console;


use Illuminate\Support\Str;

class DeployCommand extends BaseCommand
{
    protected const FOLDERS_TO_CREATE = ['tmp', 'variables'];

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
                             {--d|path=.deployment : Relative to the current path }
                             {--s|server= : Server IP adress }
                             {--p|vault=.vault-pass : File name to decrypt ansible protected variables }
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
        if ($this->checkIfGenerationHasAlreadyBeenDone()){
            $this->triggerError("Scripts has already been generated. You can delete all files and run this command again!");
        }

        $this->password = $this->secret('Give vault code for decrypting password file');

        $this->createPathFolderDirectories();

        $this->compliedAndMoveScriptsFile();

        $this->addPathFolderToGitignore();

        $this->info("Deploy scripts generated successfuly.");
    }

    private function getPath() :string
    {
        return base_path($this->option('path'));
    }

    private function createPathFolderDirectories() :void
    {
        if(!self::FOLDERS_TO_CREATE){
            return;
        }

        foreach (self::FOLDERS_TO_CREATE as $folder) {
            $this->filesystem->createDirectoryIfNotExists($this->getPath() . DIRECTORY_SEPARATOR . $folder);
        }
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
            '{{path}}'              =>  Str::lower($this->getPath()),
            '{{appurl}}'            =>  Str::lower(config('app.url', '')),
            '{{appfirstname}}'      =>  Str::lower(config('app.first_name', '')),
            '{{applastname}}'       =>  Str::lower(config('app.last_name', '')),
            '{{ftphost}}'           =>  Str::lower(config('filesystems.disks.ftp.host', '')),
            '{{ftpusername}}'       =>  Str::lower(config('filesystems.disks.ftp.username', '')),
            '{{notifemail}}'        =>  Str::lower(config('mail.from.address', '')),
            '{{vaultcode}}'         =>  Str::lower($this->password ?: ''),
        ];
    }

    private function addPathFolderToGitignore() :void
    {
        $this->filesystem->append(base_path('.gitignore'), DIRECTORY_SEPARATOR .  $this->option('path'));
    }

	private function checkIfGenerationHasAlreadyBeenDone() :bool
	{
        return $this->filesystem->exists($this->getPath());
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
