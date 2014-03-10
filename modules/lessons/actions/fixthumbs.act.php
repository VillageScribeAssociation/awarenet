<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');
	require_once($kapenta->installPath . 'modules/lessons/models/courses.set.php');
	require_once($kapenta->installPath . 'modules/lessons/inc/covers.inc.php');

//--------------------------------------------------------------------------------------------------
//*	extract video thumbnails from video lessons
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments, user role and server setup
	//----------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }
	$setName = 'videolessons';
	if ('' != $kapenta->request->ref) { $setName = $kapenta->request->ref; }

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');

	$toolReport = lessons_checkTooks();
	if ('' !== $toolReport) {
		echo $theme->expandBlocks(''
		 . "<div class='chatmessagered'>" . $toolReport . '</div>'
		 . '[[:theme::navatitlebox::ifscrollfooter:]]'
		);
		die();
	}

	$set = new Lessons_Courses($setName);

	foreach($set->members as $item) {
	
		echo ''
		 . "<div class='chatmessageblack'>"
		 . '<h2>' . $item['title'] . ' (' . $item['UID'] . ')</h2>'
		 . "</div>\n";

		$model = new Lessons_Course($item['UID']);
		$kapenta->fileMakeSubdirs('data/lessons/' . $model->UID . '/covers/x.txt');
		$kapenta->fileMakeSubdirs('data/lessons/' . $model->UID . '/thumbs/x.txt');

		foreach($model->documents as $docIdx => $doc) {

			if (false == lessons_hasCover($doc)) {

				//	set default file paths if none given
				if ((false == array_key_exists('cover', $doc)) || ('' == $doc['cover'])) {
					$doc['cover'] = ''
					 . 'data/lessons/' . $model->UID . '/covers/'
					 . $doc['uid'] . '.jpg';

					$model->documents[$docIdx]['cover'] = $doc['cover'];
				}

				if ((false == array_key_exists('cover', $doc)) || ('' == $doc['cover'])) {
					$doc['thumb'] = ''
					 . 'data/lessons/' . $model->UID . '/thumbs/'
					 . $doc['uid'] . '.jpg';

					$model->documents[$docIdx]['thumb'] = $doc['thumb'];
				}

				$report = lessons_extractImages($model->UID, $doc);

				if (false !== strpos($report, '<!-- fail -->')) {
					echo "<div class='chatmessagered'>$report</div>\n";
				} else {
					echo "<div class='chatmessagegreen'>$report</div>\n";
				}

			} else {
				echo ''
				 . "<div class='chatmessagegreen'>"
				 . "Cover exists: " . $doc['title'] . "<br/>"
				 . "<a href='" . $kapenta->serverPath . $doc['cover'] . "'>" 
				 . "<img src='" . $kapenta->serverPath . $doc['cover'] . "' />"
				 . "</a>"
				 . "</div>\n";
			}
			
		}

		$model->save();
	}


	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]');

?>
