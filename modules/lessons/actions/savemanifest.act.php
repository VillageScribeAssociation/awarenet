<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');
	require_once($kapenta->installPath . 'modules/lessons/inc/install.inc.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a course manifest
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST args and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('No UID given'); }

	$model = new Lessons_Course($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Unknown course.'); }

	//echo "<textarea rows='40' cols='100'>";
	//print_r($_POST);
	//echo "</textarea>\n";

	foreach($_POST as $key => $value) {
		$value = trim($value);
		switch($key) {
			case 'title':			$model->title = $value;					break;
			case 'description':		$model->description = $value;			break;
			case 'language':		$model->language = $value;				break;
			case 'group':			$model->group = $value;					break;
		}
	}

	$check = $model->save();

	if (true == $check) {
		$kapenta->page->do302('lessons/editmanifest/' . $model->UID);
	} else {
		$kapenta->page->do404('Could not save manifest.');
	}

	//echo "<textarea rows='40' cols='100'>" . $model->toXml() . "</textarea>\n";

?>
