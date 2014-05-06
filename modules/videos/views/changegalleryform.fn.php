<?php

    require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//* Shows a form for changing which gallery a video belongs to
//--------------------------------------------------------------------------------------------------

function videos_changegalleryform($args) {
    global $kapenta;
    $html = '';

    //----------------------------------------------------------------------------------------------
    //  check arguments and user permissions
    //----------------------------------------------------------------------------------------------
    if ('public' === $kapenta->user->role) { return $html; }

    if (true === array_key_exists('raUID', $args)) { $args['UID'] = $args['raUID']; }
    if (true === array_key_exists('videoUID', $args)) { $args['UID'] = $args['videoUID']; }
    if (false === array_key_exists('UID', $args)) { return '(no video specified)'; }

    if (false == $kapenta->user->authHas('videos', 'videos_video', 'edit', $args['UID'])) {
        return '(permission denied)';
    }

    //----------------------------------------------------------------------------------------------
    //  render the block
    //----------------------------------------------------------------------------------------------
    $html = $kapenta->theme->loadBlock('modules/videos/views/changegalleryform.block.php');
    $html = $kapenta->theme->replaceLabels($args, $html);
    return $html;
}

?>
