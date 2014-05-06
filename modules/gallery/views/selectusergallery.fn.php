<?php

    require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//* Show a select box for the user's galleries
//--------------------------------------------------------------------------------------------------
//ref: UID or Alias of a Images_Image object [string]

function images_selectusergallery($args) {
    global $kapenta;
    $varName = 'galleryUID';
    $html = '';

    //----------------------------------------------------------------------------------------------
    //  check arguments
    //----------------------------------------------------------------------------------------------
    if (false === array_key_exists('UID', $args)) { return '(image UID not given)'; }

    //----------------------------------------------------------------------------------------------
    //  load video
    //----------------------------------------------------------------------------------------------
    $model = new Images_Image($args['UID']);
    if (false === $model->loaded) { return '(image not found)'; }

    //----------------------------------------------------------------------------------------------
    //  load user's other galleries
    //----------------------------------------------------------------------------------------------
    //$conditions = array("createdBy='" . $kapenta->db->addMarkup($model->createdBy) . "'");
    $conditions = array("createdBy='" . $model->createdBy . "'");
    $range = $kapenta->db->loadRange('gallery_gallery', '*', $conditions, 'title DESC');

//    echo "<pre>";
//    print_r($conditions);
//    print_r($range);
//    echo "</pre>";

    //----------------------------------------------------------------------------------------------
    //  make the block
    //----------------------------------------------------------------------------------------------
    foreach ($range as $item) {
        $selected = '';
        if ($item['UID'] === $model->refUID) {
            $selected = " selected='selected'";
        }
        $html .= "\t"
         . "<option value='" . $item['UID'] . "'" . $selected . ">"
         . $item['title'] 
         . "</option>\n";
    }

    $html = "<select name='" . $varName . "'>\n" . $html . "</select>\n";

    return $html;
}

?>
