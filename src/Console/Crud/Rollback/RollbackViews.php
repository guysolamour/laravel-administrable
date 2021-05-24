<?php

namespace Guysolamour\Administrable\Console\Crud\Rollback;

use Guysolamour\Administrable\Console\Crud\Generate\GenerateViews;

class RollbackViews extends GenerateViews
{
    public function run()
    {
        $path = $this->getPath();

        $this->crud->filesystem->deleteDirectory($path);

        $this->removeEntryInSidebarView();

        $this->removeRouteInHeader();

        $this->crud->filesystem->delete($path);

        return  'Views file removed at ' . $path;
    }

    private function removeEntryInSidebarView() :void
    {
        $sidebar_path =  resource_path("views/{$this->data_map['{{backLowerNamespace}}']}/partials/_sidebar.blade.php");

        $this->removeEntry($sidebar_path);
    }

    private function removeEntry(string $path) :void
    {
        $start_key = "<!-- {$this->data_map['{{sidebarViewModelWithSubfolder}}']} link -->";
        $end_key = "<!-- end {$this->data_map['{{sidebarViewModelWithSubfolder}}']} link -->";

        $header = delete_all_between($start_key, $end_key, $this->crud->filesystem->get($path));

        $this->crud->filesystem->writeFile($path, $header);
    }

    private function removeRouteInHeader() :void
    {
        $header_path =  resource_path("views/{$this->data_map['{{backLowerNamespace}}']}/partials/_header.blade.php");

        $this->removeEntry($header_path);
    }

}
