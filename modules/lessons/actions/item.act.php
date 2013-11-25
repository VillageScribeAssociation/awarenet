<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	download an item
//--------------------------------------------------------------------------------------------------
//arg: course - UID fo an installed course
//arg: document - UID of a document belonging to this course

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('course', $req->args)) { $page->do404('Course not specified'); }
	if (false == array_key_exists('document', $req->args)) { $page->do404('Document not specified'); }

	$model = new Lessons_Course($req->args['course']);
	if (false == $model->loaded) { $page->do404('Course not found'); }
	if (false == $model->has($req->args['document'])) { $page->do404('Document not found'); }

	$doc = $model->documents[$req->args['document']];

	switch($doc['type']) {
		case 'flv':
			$page->do302('lessons/play/course_' . $req->args['course'] . '/document_' . $req->args['document'] . '/');
			break;

		case 'pdf':
			$page->do302('lessons/showpdf/course_' . $req->args['course'] . '/document_' . $req->args['document'] . '/');
			break;

		default:
			$page->do302($doc['file']);
			break;
	}

?>
