<?php

    require_once($kapenta->installPath . 'modules/images/models/images.mod.php');

//--------------------------------------------------------------------------------------------------
//* Shows a form for changing which gallery an image belongs to
//--------------------------------------------------------------------------------------------------

function images_changegalleryform($args) {
    global $kapenta;
    $html = '';

    //----------------------------------------------------------------------------------------------
    //  check arguments and user permissions
    //----------------------------------------------------------------------------------------------
    if ('public' === $kapenta->user->role) { return $html; }

    if (true === array_key_exists('raUID', $args)) { $args['UID'] = $args['raUID']; }
    if (true === array_key_exists('imageUID', $args)) { $args['UID'] = $args['imageUID']; }
    if (false === array_key_exists('UID', $args)) { return '(no image specified)'; }

    if (false == $kapenta->user->authHas('images', 'images_image', 'edit', $args['UID'])) {
        return '(permission denied)';
    }

    //----------------------------------------------------------------------------------------------
    //  render the block
    //----------------------------------------------------------------------------------------------
    $html = $kapenta->theme->loadBlock('modules/gallery/views/changegalleryform.block.php');
    $html = $kapenta->theme->replaceLabels($args, $html);
    return $html;
}

?>
