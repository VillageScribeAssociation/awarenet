<?php

    require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
    require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//* action to change ownership of a video from one gallery to another
//--------------------------------------------------------------------------------------------------

    //----------------------------------------------------------------------------------------------
    //  check arguments and permissions
    //----------------------------------------------------------------------------------------------

    if ('public' === $kapenta->user->role) { $kapenta->page->do403(); }

    if (false === array_key_exists('imageUID', $_POST)) { 
        $kapenta->page->do404('video uid not given'); 
    }

    if (false === array_key_exists('imageUID', $_POST)) { 
        $kapenta->page->do404('gallery uid not given'); 
    }

    //----------------------------------------------------------------------------------------------
    //  try load the video and gallery
    //----------------------------------------------------------------------------------------------

    $model = new Images_Image($_POST['imageUID']);
    if (false === $model->loaded) { $kapenta->page->do404('Image not found'); }

    $owner = new Gallery_Gallery($_POST['galleryUID']);
    if (false === $owner->loaded) { $kapenta->page->do404('Gallery not found'); }

    if (false === $kapenta->user->authHas('images', 'images_image', 'edit', $model->UID)) {
        $kapenta->page->do403('You cannot edit this image.');
    }

    //----------------------------------------------------------------------------------------------
    //  try change ownership
    //----------------------------------------------------------------------------------------------

    $model->refModule = 'galery';
    $model->refModel = 'gallery_gallery';
    $model->refUID = $owner->UID;

    $report = $model->save();

    if ('' !== $report) {
        $kapenta->session->msg('Could not move image: ' . $report);
    }

    //----------------------------------------------------------------------------------------------
    //  redirect ot the new gallery
    //----------------------------------------------------------------------------------------------

    $kapenta->page->do302('gallery/show/' . $owner->UID);

?>
