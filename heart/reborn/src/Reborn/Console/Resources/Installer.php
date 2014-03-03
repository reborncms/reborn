<?php

namespace {module};

class {module}Installer extends \Reborn\Module\AbstractInstaller
{

    public function install($prefix = null)
    {
        \Schema::table($prefix.'{table}', function ($table) {
            $table->create();
            $table->increments('id');
            // Add Your data filed at here
            $table->timestamps();
        });
    }

    public function uninstall($prefix = null)
    {
        \Schema::dropIfExists($prefix.'{table}');
    }

    public function upgrade($dbVersion, $prefix = null) {}

}
