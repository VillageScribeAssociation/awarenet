<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	
//--------------------------------------------------------------------------------------------------
//reqarg: manifest - UID of a Course [string]
//reqarg: document - UID of a document in this course [string]

	//----------------------------------------------------------------------------------------------
	//	check post args and user role
	//----------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	if (false == array_key_exists('manifest', $req->args)) { $page->do404('No manifest'); }
	if (false == array_key_exists('document', $req->args)) { $page->do404('No document'); }

	$model = new Lessons_Course($req->args['manifest']);
	if (false == $model->loaded) { $page->do404('unknown course'); }

	$document = array();
	$index = -1;

	foreach($model->documents as $idx => $doc) {
		if ($doc['uid'] == $req->args['document']) {
			$document = $doc;
			$index = $idx;
		}
	}

	if (0 == count($document)) { $page->do404('unknown document'); }

	$kapenta->fileMakeSubdirs('data/lessons/' . $model->UID . '/covers/x.txt');
	$kapenta->fileMakeSubdirs('data/lessons/' . $model->UID . '/thumbs/x.txt');

	if (false == $kapenta->fs->exists($document['file'])) { do404('file not found'); }

	//----------------------------------------------------------------------------------------------
	//	handle PDFs
	//----------------------------------------------------------------------------------------------

	if ('pdf' == $document['type']) {
		echo "Processing PDF: " . $document['file'] . "<br/>\n";		

		$jpgFile = 'data/lessons/' . $model->UID . '/covers/' . $document['uid'] . '_large.jpg';
		$coverFile = 'data/lessons/' . $model->UID . '/covers/' . $document['uid'] . '.jpg';
		$thumbFile = 'data/lessons/' . $model->UID . '/thumbs/' . $document['uid'] . '.jpg';

		$shellCmd = 'convert "' . $document['file'] . '"[0] -density 288 "' . $jpgFile . '"';
		echo "executing: " . htmlentities($shellCmd) . "<br/>\n";
		shell_exec($shellCmd);

		$shellCmd = 'convert "' . $jpgFile . '" -resize 300 ' . $coverFile;
		echo "executing: " . htmlentities($shellCmd) . "<br/>\n";
		shell_exec($shellCmd);

		$shellCmd = 'convert "' . $jpgFile . '" -resize 100x100^ -gravity center -extent 100x100 ' . $thumbFile;
		echo "executing: " . htmlentities($shellCmd) . "<br/>\n";
		shell_exec($shellCmd);

		if (true == $kapenta->fs->exists($coverFile)) {
			$model->documents[$index]['cover'] = $coverFile;	
			$model->save();
		}

		if (true == $kapenta->fs->exists($thumbFile)) {
			$model->documents[$index]['thumb'] = $thumbFile;	
			$model->save();
		}

	}

?>
