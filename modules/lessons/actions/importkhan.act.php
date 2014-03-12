<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	import khan academy videos as lesson packages
//--------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do404(); }

	$fromDir = 'data/khanacademy/';
	$dirs = $kapenta->fs->listDir($fromDir, '', true);
	sort($dirs);

	$courses = array();

	foreach($dirs as $dir) {
		$dir = basename(substr($dir, 0, strlen($dir) - 1));
		echo $dir . "<br/>";

		//------------------------------------------------------------------------------------------
		//	make the course
		//------------------------------------------------------------------------------------------

		$cUID = $kapenta->createUID();
		$course = new Lessons_Course();
		$course->UID = $cUID;
		$course->title = $dir;
		$course->group = 'videolesson';
		$course->description = 'videolesson';
		$course->language = 'en';

		//------------------------------------------------------------------------------------------
		//	add the videos
		//------------------------------------------------------------------------------------------

		$files = $kapenta->fs->listDir($fromDir . '/'  . $dir . '/');
		sort($files);

		foreach($files as $file) {
			echo basename($file) . "<br/>\n";
			$dUID = $kapenta->createUID();
			$ext = 'flv';
			if (false !== strpos(strtolower($file), '.mp4')) { $ext = 'mp4'; }

			$newFile = ''
			 . 'data/lessons/' . $course->UID . '/documents/'
			 . str_replace(array(' ', '_') , array('-', '-'), basename($file));

			$doc = array(
				'uid' => $dUID,
				'type' => $ext,
				'title' => str_replace(array('.flv', '.mp4'), array('', ''), basename($file)),
				'description' => '',
				'cover' => '',
				'thumb' => '',
				'file' => $newFile
			);

			$kapenta->fileCopy($file, $newFile);
			$course->documents[$dUID] = $doc;
		}

		echo "<hr/>\n";
		echo "<pre>" . htmlentities($course->toXml()) . "</pre>";
		echo "<hr/>\n";

		$course->loaded = true;
		$course->save();

	}

?>
