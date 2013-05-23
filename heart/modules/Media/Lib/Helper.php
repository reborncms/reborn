<?php

namespace Media\Lib;

/**
 * Helper class for media module
 *
 * @package Media\Lib
 * @author RebornCMS Development Team
 **/
class Helper
{
    /**
     * This method solve the tree view or child folders.
     *
     * @return mixed
     * @author RebornCMS Development Team
     **/
    public static function folderTree($folders, $parent, $depth, $selected = 0,
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
                static::folderTree($folders,$folder->id,$depth, $selected, $style);
            }
        }
    }

} // END class Helper
