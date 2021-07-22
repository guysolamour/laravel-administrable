<?php

namespace Guysolamour\Administrable\Console\Administrable;

use Guysolamour\Administrable\Console\BaseCommand;

class UpdateGuardCommand extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:guard:update
                                {--id=    : Guard id }
                                {--field= : Field to update }
                                {--value= : New value }
                            ';


    protected $description = 'Update guard entry in database';


    public function handle()
    {
        $this->validate('id', 'field', 'value');

        /**
         * @var \Illuminate\Database\Eloquent\Model
         */
        $guard = $this->getGuard();

        $guard->setAttribute($this->option('field'), $this->option('value'));

        $guard->save();

        $this->info("The guard [" . $this->option('field') . "] field has been updated");
    }

    private function validate(string ...$options) :bool
    {
        foreach ($options as $option) {
            if (!$this->option($option)){
                $this->triggerError("The {$option} option is required");
            }
        }

        return true;
    }

    private function getGuard(): \Illuminate\Database\Eloquent\Model
    {
        return get_guard_model_class()::find($this->option('id'));
    }
}
