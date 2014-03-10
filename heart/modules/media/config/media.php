<?php

return array(
        'allow' => array(
            'default'   => array('jpg', 'jpeg', 'gif', 'png', 'tif', 'pdf', 'doc',
                'docx', 'rtf', 'txt', 'zip', 'rar', 'gz', 'tar'),
            'ck'        => array('jpg', 'jpeg', 'gif', 'png'),
            'other'     => array('jpg', 'jpeg', 'gif', 'png'),
            ),
        'upload_path'   => UPLOAD . date('Y') . DS . date('m') . DS,
        'file_rename'   => true,
        'create_dir'    => true,
        'encrypt'       => true,
        'default_name'  => 'RBFolder',
        'upload_config' => array(
                'encName'   => true,
                'path'      => UPLOAD . date('Y') . DS . date('m') . DS,
                'prefix'    => 'rb_',
                'createDir' => true,
                'rename'    => true,
                'recursive' => true,
                'overwrite' => false,
                'allowedExt'    => array(
                        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'txt', 'rtf', 'doc',
                        'docx','xls', 'xlsx', 'pdf', 'zip', 'tar', 'rar', 'mp3',
                        'wav', 'wma',
                    ),
            ),
    );
