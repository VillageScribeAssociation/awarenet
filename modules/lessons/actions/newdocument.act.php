<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new document to a package 
//--------------------------------------------------------------------------------------------------
//postarg: manifestUID - UID of an installed course package [string]
//postarg: title - name of this document [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	if (false == array_key_exists('manifestUID', $_POST)) { $kapenta->page->do404('no manifest uid'); }
	if (false == array_key_exists('title', $_POST)) { 
		print_r($_POST);
		$kapenta->page->do404('no title uid');
	}

	$model = new Lessons_Course($_POST['manifestUID']);
	if (false == $model->loaded) { $kapenta->page->do404(); }

	$newDoc = array();

	foreach($model->dProperties as $key => $value) {
		if (true == array_key_exists($key, $_POST)) {
			$newDoc[$key] = trim($_POST[$key]);
		}
	}

	if (false == array_key_exists('uid', $newDoc)) { $newDoc['uid'] = $kapenta->createUID(); }

	$model->documents[] = $newDoc;

	$check = $model->save();

	if (true == $check) {
		$kapenta->page->do302('lessons/editmanifest/' . $model->UID);
	} else {
		echo "<textarea rows='20' cols='100'>" . $model->toXML() . "</textarea>";
	}

?>
