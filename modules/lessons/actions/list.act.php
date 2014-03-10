<?php

	require_once($kapenta->installPath . 'modules/lessons/models/courses.set.php');

//--------------------------------------------------------------------------------------------------
//*	list lesson packages installed on this server
//--------------------------------------------------------------------------------------------------

	if ('public' == $user->role) { $page->do404(); }

	$xgroup = 'videolessons';

	if ('' != $kapenta->request->ref) { $xgroup = $kapenta->request->ref; }

	$set = new Lessons_Courses($xgroup);
	if (false == $set->loaded) { $page->do404('No lessons yet added.'); }

	$kapenta->page->load('modules/lessons/actions/list.page.php');
	$kapenta->page->blockArgs['courseGroup'] = $xgroup;
	$kapenta->page->render();

?>
