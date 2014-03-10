<?php

//---------------------------------------------------------------------o -----------------------------
//*	popup window to edit course details
//--------------------------------------------------------------------------------------------------

	if (('admin' !== $user->role) && ('teacher' !== $user->role)) {
		$page->do403('You are not permitted to edit this course', true);
	}

	if ('' == $kapenta->request->ref) { $page->do404('Course not specified.', true); }

	if (false = $kapenta->fs->exists('data/lessons/' . $kapenta->request->ref)) {
		$page->do404('No such course.', true);
	}

	$kapenta->page->load('modules/lessons/actions/editcourse.page.php');
	$kapenta->page->blockArgs['UID'] = $kapenta->request->ref;
	$kapenta->page->render();

?>
