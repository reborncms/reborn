<?php

namespace Media;
//
/**
 * Installer for Media Module
 *
 * @package Media
 * @author RebornCMS Development Team
 **/
class MediaInstaller extends \Reborn\Module\AbstractInstaller
{

    public function install($prefix = null)
    {
        // Creating files table
        \Schema::table($prefix.'media_files', function($table)
        {
            $table->create();
            $table->increments('id');
            $table->string('name');
            $table->string('alt_text')->nullabel();
            $table->text('description')->nullable();
            $table->integer('folder_id');
            $table->integer('user_id');
            $table->string('filename');
            $table->string('filesize');
            $table->string('extension', 50);
            $table->string('mime_type');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('download')->default(0);
            $table->timestamps();
        });

        \Schema::table($prefix.'media_folders', function($table)
        {
            // Creating folder table
            $table->create();
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->integer('folder_id');
            $table->integer('user_id');
            $table->integer('depth');
            $table->timestamps();
        });
    }

    public function uninstall($prefix = null)
    {
        \Schema::drop($prefix.'media_files');
        \Schema::drop($prefix.'media_folders');
    }

    public function upgrade($v, $prefix = null)
    {
        return $v;
    }

} // END class MediaInstaller
