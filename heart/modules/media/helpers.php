<?php

/**
 * This method solve the tree view or child folders.
 *
 * @return mixed
 * @author RebornCMS Development Team
 **/
if (! function_exists('folderTree')) {

    function folderTree($folders, $parent, $depth, $selected = 0,
    $style = '&nbsp;&#187;&nbsp;')
    {
        foreach($folders as $folder)
        {
            if($folder->folder_id == $parent)
            {
                if($selected == $folder->id){?>

                <option value="<?php echo $folder->id; ?>" selected = "selected">

                    <?php echo str_repeat($style, $folder->depth) . $folder->name; ?>

                </option>

                <?php }else{?>

                <option value="<?php echo $folder->id; ?>">

                    <?php echo str_repeat($style, $folder->depth) . $folder->name; ?>

                </option>

                <?php }
                folderTree($folders,$folder->id,$depth, $selected, $style);
            }
        }
    }

}

if (! function_exists('duplicate')) {
    function duplicate($name, $type = 'folder', $except = null)
    {

        $query = ('folder' == $type) ? Media\Model\Folders::where('name', $name) :
                    Media\Model\Files::where('name', $name);

        $query = (is_null($except)) ? $query->first() : 
                    $query->where('name', '!=', $name)->first();

        $finalName = $name;

        if (! is_null($query)) {
            $finalName = duplicate(increasemental($name), $type, $except);
        }

        return $finalName;

    }
}

/**
 * This function will solve name duplication
 *
 * @param String $name File or folder name
 * @param int $folderId Parent folder id
 * @param String $fof For files or folders. Avilable ('file', 'folder')
 * @param String $except except from
 *
 * @return String $name Name
 **/
if (! function_exists('duplication')) {

    function duplication($name, $folderId, $file = false, $except = null)
    {
        $names = null;

        if ($file) {
            $names = Media\Model\Files::where('folder_id', '=', $folderId)
                                        ->where('name', '!=', $except)->get();
        } else {
            $names = Media\Model\Folders::where('folder_id', '=', $folderId);

            if (!is_null($except)) $names->where('name', '!=', $except);

            $names = $names->get();
        }

        $nameArray = array();

        for ($i=0; $i < count($names); $i++) {
            $nameArray[$i] = $names[$i]['name'];
        }

        while (in_array($name, $nameArray)) {
            $name = increasemental($name);
        }

        return $name;
    }

}


/**
 * Rename with increasemental surfix like _1, _2, etc.
 * Default string is like this (name_1, name_2, name_3)
 * Maximum increasement number is 99
 *
 * @param String $target String to be increased
 *
 * @return String $target
 **/
if (! function_exists('increasemental')) {

    function increasemental($target)
    {
        $matching = preg_match_all('/^(\w*)_(\d\d?)$/', $target, $matches);

        if ($matching) {
            $count = ((int)$matches[2][0])+1;

            $target = $matches[1][0] . '_' . $count;
        } else {
            $target = $target.'_1';
        }

       return $target;
    }

}

/**
 * This method will return depth of
 *
 * @param int $id Id of parent folder
 *
 * @return void
 **/
if (! function_exists('defineDepth')) {

    function defineDepth($id)
    {
        if (0 === $id) {
            return 1;
        } else {
            $result = Media\Model\Folders::where('id', '=', $id)->first();

            return $result['depth']+1;
        }
    }

}

/**
 * Getting image width and height
 *
 * @param String $file File name with directory
 * @return array $size Width and height of expected img
 * @author RebornCMS Development Team
 **/
if (! function_exists('getImgDimension')) {

    function getImgDimension($file, $mime)
    {
        if (\File::is($file)) {
            $matching = preg_match_all('/^(image)\/(\w*)$/', $mime, $match);

            if ($matching) {
                $data = @getimagesize($file);

                return array('width' => $data[0], 'height' => $data[1]);
            }

            return array('width' => 0, 'height' => 0);
        }

        throw new RbException("File not found \"$file\"");
    }

}

/**
 * Scaling
 *
 * @param int $originWidth
 * @param int $originHeight
 * @param int $expected
 * @param String $scaleFor
 *
 * @return int $scaled Scaled value
 **/
if (! function_exists('doScale')) {
    function doScale($originWidth, $originHeight, $expected, $scaleFor = 'height')
    {
        switch ($scaleFor) {
            case 'height':
                $scaled = ($expected / $originWidth) * $originHeight;

                break;

            case 'width':
                $scaled = ($expected / $originHeight) * $originWidth;

                break;

            default:
                trigger_error("The third parameter is wrong!", E_USER_ERROR);

                break;
        }

        return (int) round($scaled);
    }
}
