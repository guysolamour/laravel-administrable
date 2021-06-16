<?php

namespace Guysolamour\Administrable\Console\Storage;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Guysolamour\Administrable\Console\BaseCommand;
use Guysolamour\Administrable\Console\Storage\Zip;
use Guysolamour\Administrable\Notifications\Back\SuccessfulStorageFolderBackupNotification;

class StorageDumpCommand extends BaseCommand
{
    const DEFAULT_DISK     = 'storagedump';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:storage:dump
                            {--f|folder= : Folder to backup inside the storage folder }
                            {--d|disk=* : Folder to backup inside the storage folder }
                            {--l|limit= : Number of dumps to keep }
                            {--s|send_notification : Send notification }
                             ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zip and send storage folder to a specific disk';


    protected Filesystem $filesystem;


    public function __construct()
    {
        parent::__construct();

        $this->filesystem = new Filesystem;
    }

    public function handle()
    {
        $this->info('Beginning task ...');

        $zip_file = (new Zip)
                ->setPath($this->getFilePath())
                ->addFolder($this->getFolder())
                ->save();
        $this->info('Zip file successfully created');

        $this->saveFileToDisks($zip_file);

        $this->filesystem->delete($zip_file);
        $this->info('Zip file successfully deleted');

        $this->info('Storage folder dumped successfully ...');
    }

    private function saveFileToDisks(string $file) :void
    {
        foreach ($this->getDisks() as $disk) {
            // send to disk
            $this->info("Sending zip file to {$disk} disk");
            Storage::disk($disk)->put(
                $this->getDumpFolder($disk),
               $this->filesystem->get($file)
            );

            $this->deletePreviousBackup($disk);

            $this->sendNotification($disk);
        }
    }

    private function sendNotification(string $disk) :void
    {
        if ($this->option('send_notification')) {
            $notifiables = get_guard_model()::getNotifiables()->get();
            $notification = config('administrable.storage_dump.notifications.mail.class', SuccessfulStorageFolderBackupNotification::class);

            if ($notifiables->isNotEmpty() && class_exists($notification)) {
                $this->info('Sending notification....');
                Notification::send($notifiables, new $notification($this->getFileName(), $disk));
            }
        }
    }

    private function checkIfDiskIsSupported(string $disk): bool
    {
        return !empty(Arr::get(config('filesystems.disks', []), $disk, []));
    }

    private function isDefaultDisk(string $disk): bool
    {
        return $disk === self::DEFAULT_DISK;
    }

    private function getDumpFolder(string $disk): string
    {
        $dump_folder = $this->getFileName();

        if (!$this->isDefaultDisk($disk)) {
            $dump_folder = config('administrable.storage_dump.dump_folder', 'storagedump') . "{$disk}/"  . $dump_folder;
        }
        return $dump_folder;
    }

    private function deletePreviousBackup(string $disk)
    {
        $this->info("Deleting {$disk} disk last previous backup");

        $folder = $this->isDefaultDisk($disk) ? null : config('administrable.storage_dump.dump_folder', 'storagedump') . "/{$disk}/";
        $backup_files  = collect(Storage::disk($disk)->allFiles($folder));

        if ($backup_files->count() > $this->getLimit()) {
            Storage::disk($disk)->delete($backup_files->first());
        }
    }

    private function getDisks(): array
    {
        $disks = $this->option('disk');

        if (!$disks){
            $disks = config('administrable.storage_dump.disks', ['ftp']);
        }

        foreach ($disks as $disk) {
            if (!$this->checkIfDiskIsSupported($disk)) {
                throw new \Exception("[{$disk}] disk is not supported");
            }
        }

        if (!in_array(self::DEFAULT_DISK, $disks)){
            array_push($disks, self::DEFAULT_DISK);
        }

        return $disks;
    }

    private function getFileName() :string
    {
        return config('administrable.storage_dump.filename', 'storage_dump') . '-' . date('Y_m_d_His') . '.zip';
    }

    private function getLimit() :int
    {
        return $this->option('limit') ?? config('administrable.storage_dump.limit', 5);
    }

    private function getFilePath() :string
    {
        $temporary_directory = config('administrable.storage_dump.temporary_directory', public_path());

        return $temporary_directory . DIRECTORY_SEPARATOR . $this->getFileName();
    }

    private function getFolder(): string
    {
        return Str::finish(storage_path('app/' . $this->option('folder')), '/');
    }
}
