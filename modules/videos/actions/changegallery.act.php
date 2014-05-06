<?php

    require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');
    require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//* action to change ownership of a video from one gallery to another
//--------------------------------------------------------------------------------------------------

    //----------------------------------------------------------------------------------------------
    //  check arguments and permissions
    //----------------------------------------------------------------------------------------------

    if ('public' === $kapenta->user->role) { $kapenta->page->do403(); }

    if (false === array_key_exists('videoUID', $_POST)) { 
        $kapenta->page->do404('video uid not given'); 
    }

    if (false === array_key_exists('galleryUID', $_POST)) { 
        $kapenta->page->do404('gallery uid not given'); 
    }

    //----------------------------------------------------------------------------------------------
    //  try load the video and gallery
    //----------------------------------------------------------------------------------------------

    $model = new Videos_Video($_POST['videoUID']);
    if (false === $model->loaded) { $kapenta->page->do404('Video not found'); }

    $owner = new Videos_Gallery($_POST['galleryUID']);
    if (false === $owner->loaded) { $kapenta->page->do404('Gallery not found'); }

    if (false === $kapenta->user->authHas('videos', 'videos_video', 'edit', $model->UID)) {
        $kapenta->page->do403('You cannot edit this video.');
    }

    //----------------------------------------------------------------------------------------------
    //  try change ownership
    //----------------------------------------------------------------------------------------------

    $model->refModule = 'videos';
    $model->refModel = 'videos_gallery';
    $model->refUID = $owner->UID;

    $report = $model->save();

    if ('' !== $report) {
        $kapenta->session->msg('Could not move video: ' . $report);
    }

    //----------------------------------------------------------------------------------------------
    //  redirect ot the new gallery
    //----------------------------------------------------------------------------------------------

    $kapenta->page->do302('videos/showgallery/' . $owner->UID);

?>
