<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display PDF in a frame to allow user comments and links to other documents
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

	$width = '1000';
	$height = '600';

	if ('desktop' !== $session->get('deviceprofile')) {
		//	temporary for now
		$width = '320';
		$height = '200';
	}

	$page->load('modules/lessons/actions/showpdf.page.php');

	foreach($doc as $key => $val) { $kapenta->page->blockArgs[$key] = $val; }

	if (true == array_key_exists('licence', $page->blockArgs)) {
		$kapenta->page->blockArgs['licence'] = $kapenta->page->blockArgs['licencename'];
	}

	$kapenta->page->blockArgs['courseUID'] = $req->args['course'];
	$kapenta->page->blockArgs['documentUID'] = $req->args['document'];
	$kapenta->page->blockArgs['document_title'] = $doc['title'];
	$kapenta->page->blockArgs['fileName'] = $doc['file'];
	$kapenta->page->blockArgs['coverImage'] = $kapenta->serverPath . $doc['cover'];
	$kapenta->page->blockArgs['width'] = $width;
	$kapenta->page->blockArgs['height'] = $height;
	$page->render();

?>
