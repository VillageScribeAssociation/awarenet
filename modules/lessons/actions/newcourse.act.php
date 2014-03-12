<?php
	
	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');
	require_once($kapenta->installPath . 'modules/lessons/inc/install.inc.php');

//--------------------------------------------------------------------------------------------------
//*	add a new course
//--------------------------------------------------------------------------------------------------
//postarg:

	//----------------------------------------------------------------------------------------------
	//	check POST args and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	$model = new Lessons_Course($_POST['uid']);
	if (true == $model->loaded) { $kapenta->page->do403('UID already exists'); }

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'uid':				$model->UID = trim($value);				break;
			case 'lang':			$model->lang = trim($value);			break;
			case 'title':			$model->title = trim($value);			break;
			case 'description':		$model->description = trim($value);		break;
			case 'group':			$model->group = trim($value);			break;
		}
	}

	if (('' == $model->title) || ('' == $model->UID)) { $kapenta->page->do404('missing field'); }
	$model->loaded = true;

	//echo "<textarea rows='10' cols='80'>" . $model->toXml() . "</textarea>\n";

	$check = $model->save();
	if (false == $check) { $kapenta->page->do404("Could not save manifest."); }

	$kapenta->page->do302('lessons/editmanifest/' . $model->UID);

?>
