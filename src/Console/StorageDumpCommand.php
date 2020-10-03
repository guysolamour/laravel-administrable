<?php

namespace Guysolamour\Administrable\Console;

use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Guysolamour\Administrable\Notifications\Back\SuccessfulStorageFolderBackupNotification;


class StorageDumpCommand extends BaseCommand
{
    const DUMPS_TO_KEEP = 5;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:storage:dump
                            {--l|limit= : Number of dumps to keep }
                            {--s|send : Send notification }
                             ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zip and send storage folder to a specific disk';


    public function __construct()
    {
        parent::__construct();

        $this->filesystem = new Filesystem;
    }


    public function handle()
    {

        $limit = (int) ($this->option('limit') ?? config('administrable.storage_dump.limit', self::DUMPS_TO_KEEP));
        $temporary_directory = config('administrable.storage_dump.temporary_directory', public_path());

        $disks = config('administrable.storage_dump.disks',['ftp']);
        $dump_folder = config('administrable.storage_dump.dump_folder', 'storagedump');

        // create zip
        $filename = $this->getFileName();
        $file = create_zip_archive_from_folder($temporary_directory . '/' . $filename, storage_path());

        foreach ($disks as $disk) {
            // send to disk
            Storage::disk($disk)->put(
                $dump_folder . "/{$disk}/" . $filename,
                $this->filesystem->get($file)
            );

            // delete previous backup
            $backup_files  = collect(Storage::disk($disk)->allFiles($dump_folder . '/' . $disk));
            if ($backup_files->count() > $limit) {
                Storage::disk($disk)->delete($backup_files->first());
            }

            // send notifications
            if ($this->option('send')) {
                $notifiable = $this->getNotifiable();

                $notification = config('administrable.storage_dump.notifications.mail.class', SuccessfulStorageFolderBackupNotification::class);

                if ($notifiable){
                    $notifiable->notify(new $notification($filename, $disk));
                }
            }
        }

        // delete local zip file
        $this->filesystem->delete($file);

    }

    /**
     * @return string
     */
    private function getFileName() :string
    {
        return Str::lower(config('administrable.storage_dump.filename', 'storage_dump') . '-' . date('Y_m_d_His') . '.zip');
    }

    /**
     *
     * @return Admin|null
     */
    private function getNotifiable()
    {
        return Admin::whereIn('email', config('administrable.emails', []))->first();
    }

}
