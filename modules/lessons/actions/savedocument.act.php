<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save a document subrecord
//--------------------------------------------------------------------------------------------------
//postarg: manifestUID - UID of a Course [string]
//postarg: documentUID - UID of a document in this course [string]

	//----------------------------------------------------------------------------------------------
	//	check POST variables and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	if (false == array_key_exists('manifestUID', $_POST)) { $kapenta->page->do404('no manifestUID'); }
	if (false == array_key_exists('documentUID', $_POST)) { $kapenta->page->do404('no documentUID'); }

	$model = new Lessons_Course($_POST['manifestUID']);
	if (false == $model->loaded) { $kapenta->page->do404('unknown manifest'); }

	$dUID = $_POST['documentUID'];

	$found = false;
	foreach($model->documents as $idx => $document) {
		if ($document['uid'] == $dUID) {
			$found = true;

			//--------------------------------------------------------------------------------------
			//	make the change
			//--------------------------------------------------------------------------------------

			foreach($model->dProperties as $key => $value) {
				$formField = $dUID . 'XX' . $key;
				if (true == array_key_exists($formField, $_POST)) {
					$document[$key] = $_POST[$formField];
				}
			}

			$model->documents[$idx] = $document;
			$check = $model->save();

			if (true == $check) {
				$kapenta->session->msg("Document updated: " . $document['title'] . ' (' . $dUID . ')', 'ok');
				$kapenta->page->do302('lessons/editmanifest/' . $model->UID);
			} else {
				echo $model->toXml();
				die();
			}

		}
	}

	if (false == $found) { $kapenta->page->do404('no such document in this manifest'); }

?>
