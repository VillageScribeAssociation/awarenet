<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');
	require_once($kapenta->installPath . 'modules/lessons/inc/khan.inc.php');

//--------------------------------------------------------------------------------------------------
//*	development / administrative action 
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check $_POST argument and user role
	//----------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not posted'); }

	$courses = lessons_listKhan();
	$model = new Lessons_Course();
	$model->UID = '';

	foreach($courses as $course) {
		if ($course->UID == $_POST['UID']) { $model = $course; }
	}

	if ('' == $model->UID) { $kapenta->page->do404('Unknown course.'); }

	//----------------------------------------------------------------------------------------------
	//	scrape for youtube links
	//----------------------------------------------------------------------------------------------
	
	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');
	echo "<h1>Building Package: " . $model->UID . "</h1>\n";
	echo "<div class='chatmessageblack'><b>Course:</b> " . $model->title . "</div>\n";

	foreach($model->documents as $idx => $doc) {
		$youtubeId = lessons_getVideoId($doc['downloadfrom']);

		if ('' == $youtubeId) {
			echo ''
			 . "<div class='chatmessagered'>\n"
			 . "Getting Youtube ID: " . $doc['downloadfrom'] . "<br/>\n"
			 . "Couldn't find it :(<br/>\n"
			 . "</div>\n";
		} else {
			echo ''
			 . "<div class='chatmessagegreen'>\n"
			 . "Looking for youtube embed in: " . $doc['downloadfrom'] . "<br/>\n"
			 . "Found: $youtubeId<br/>\n"
			 . "</div>\n";

			$doc['attriburl'] = $doc['downloadfrom'];
			$doc['file'] = 'data/lessons/' . $model->UID . '/documents/' . $youtubeId . '.flv';
			$doc['downloadfrom'] = 'youtube://' . $youtubeId;
			$doc['language'] = 'en';
			$doc['type'] = 'flv';
			$doc['attribname'] = 'Khan Academy';
			$doc['licence'] = 'CC BY-NC-SA';
			$doc['licenceurl'] = 'http://creativecommons.org/licenses/by-nc-sa/3.0/us/';
			$model->documents[$idx] = $doc;

		}

	}

	echo ''
	 . "<div class='chatmessageblack'>"
	 . "<textarea rows='10' style='width:100%;'>" . $model->toXml() . "</textarea><br/>\n"
	 . "</div>\n";

	//----------------------------------------------------------------------------------------------
	//	save the manifest
	//----------------------------------------------------------------------------------------------
	
	$check = $model->save();
	if (true == $check) {

		$kapenta->fileMakeSubDirs('data/lessons/' . $model->UID . '/documents/x.x');
		$kapenta->fileMakeSubDirs('data/lessons/' . $model->UID . '/covers/x.x');
		$kapenta->fileMakeSubDirs('data/lessons/' . $model->UID . '/thumbs/x.x');

		echo ''
		 . "<div class='chatmessagegreen'>"
		 . "Saved.  You will now need to run the youtube download script, then "
		 . "<a href='{$kapenta->serverPath}lessons/rebuild/'>rebuild course lists</a> and "
		 . "<a href='{$kapenta->serverPath}lessons/fixthumbs/'>generate covers</a>.<br/>"
		 . "<pre>"
			 . "cd {$kapenta->installPath}modules/lessons/shell/\n"
			 . "php downloadcourse.sh.php {$model->UID}"
		 . "</pre>"
		 . "</div>\n";

	} else {
		echo ''
		 . "<div class='chatmessagered'>"
		 . "Could not save manifest, please check file permissions on data/lessons/"
		 . "</div>\n";
	}

	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]');

?>
