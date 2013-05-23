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
    );
