<?php

namespace Guysolamour\Administrable\Jobs;

use Carbon\Carbon;
use Illuminate\Foundation\Bus\Dispatchable;

class RemoveOrphanTemporaryFiles
{
    use Dispatchable;


    public function handle()
    {
        /**
         * @var \Illuminate\Database\Eloquent\Collection
         */
        $temporary_files = config('administrable.modules.filemanager.temporary_model')::whereDate('created_at', '<=', Carbon::yesterday())->get();

        if ($temporary_files->isEmpty()){
            return;
        }

        $temporary_files->each->delete();
    }
}
