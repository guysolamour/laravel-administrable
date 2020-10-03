<?php

namespace Guysolamour\Administrable\Notifications\Back;

use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SuccessfulStorageFolderBackupNotification extends Notification
{
    /**
     *
     * @var string
     */
    private $filename;
    /**
     *
     * @var string
     */
    private $disk;

    /**
     * Create a notification instance.
     *
     * @param string $filename
     * @return void
     */
    public function __construct(string $filename, string $disk)
    {
        $this->filename = $filename;
        $this->disk     = $disk;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(config('administrable.storage_dump.notifications.mail.from.address', config('mail.from.address')), config('administrable.storage_dump.notifications.mail.from.name', config('mail.from.name')))
            ->subject(Lang::get('Succès de la sauvegarde du dossier storage de :application_name', ['application_name' => config('app.name')]))
            ->greeting(Lang::get('Bonjour :name', ['name' => $notifiable->full_name]))
            ->line(Lang::get(
                'Bonne nouvelle, une nouvelle sauvegarde du dossier storage de :application_name a été créée avec succès sur le disque nommé :disk_name. avec pour nom :filename',
                ['application_name' => config('app.name'), 'disk_name' => $this->disk, 'filename' => $this->filename]
            ));
    }
}
