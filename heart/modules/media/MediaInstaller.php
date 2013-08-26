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

    public function install()
    {
        // Creating files table
        \Schema::table('media_files', function($table)
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
            $table->integer('order')->default(0);
            $table->text('module');
            $table->timestamps();
        });

        \Schema::table('media_folders', function($table)
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
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function uninstall()
    {
        \Schema::drop('media_files');
        \Schema::drop('media_folders');
    }

    public function upgrade($v)
    {
        return $v;
    }

} // END class MediaInstaller