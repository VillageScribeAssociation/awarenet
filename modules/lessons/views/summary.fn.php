<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a summary fo a course
//--------------------------------------------------------------------------------------------------
//arg: courseUID - UID of a Course package

function lessons_summary($args) {
	global $user;
	global $theme;
	global $kapenta;

	$html = '';							//%	return value [string]
	$coverset = '';						//%	covers of everything in this set [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return ''; }
	if (false == array_key_exists('courseUID', $args)) { return '(courseUID not given)'; }

	$model = new Lessons_Course($args['courseUID']);
	if (false == $model->loaded) { return '(course not found: ' . $args['courseUID'] . ')'; }

	//----------------------------------------------------------------------------------------------
	//	gather the covers
	//----------------------------------------------------------------------------------------------

	if ('textbooks' == $model->group) {
		foreach($model->documents as $doc) {
			if (('' != $doc['cover']) && ($kapenta->fs->exists($doc['cover']))) {

				$itemLink = "lessons/item/course_" . $model->UID . "/document_" . $doc['uid'] . "/";

				$coverset .= ''
				 . "<a href='%%serverPath%%$itemLink' alt='" . $doc['title'] . "'>"
				 . "<img "
					 . "src='%%serverPath%%" . $doc['cover'] . "' "
					 . "width='240px' "
					 . "border='1px black' "
					 . "style='display: inline;' "
					 . "class='rounded' "
				 . "/>"
				 . "</a>\n";
			}
		}
	} else {
		$coverset = '[[:lessons::videosummary::UID=' . $model->UID . ':]]';
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/lessons/views/summary.block.php');
	$labels = $model->toArray();

	$labels['coverset'] = $coverset;
	$labels['editLink'] = '';

	if ('videolesson' == $labels['description']) { $labels['description'] = ''; }

	if ('admin' == $user->role) {
		$labels['editLink'] = ''
		 . "<br/>\n"
		 . "<a href='%%serverPath%%lessons/editmanifest/" . $model->UID . "'>[edit]</a>\n";
	}

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
