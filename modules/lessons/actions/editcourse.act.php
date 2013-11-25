<?php

//---------------------------------------------------------------------o -----------------------------
//*	popup window to edit course details
//--------------------------------------------------------------------------------------------------

	if (('admin' !== $user->role) && ('teacher' !== $user->role)) {
		$page->do403('You are not permitted to edit this course', true);
	}

	if ('' == $req->ref) { $page->do404('Course not specified.', true); }

	if (false = $kapenta->fs->exists('data/lessons/' . $req->ref)) {
		$page->do404('No such course.', true);
	}

	$page->load('modules/lessons/actions/editcourse.page.php');
	$kapenta->page->blockArgs['UID'] = $req->ref;
	$page->render();

?>
