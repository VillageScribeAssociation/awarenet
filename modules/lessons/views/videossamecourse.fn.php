<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	list videos in the same course as the one being watched
//--------------------------------------------------------------------------------------------------
//args: UID - UID of a Lessons_Course object [string]

function lessons_videossamecourse($args) {
	global $theme;

	$html = '';									//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }
	$model = new Lessons_Course($args['UID']);
	if (false == $model->loaded) { return '(course not found: ' . $args['UID'] . ')'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$block = $theme->loadBlock('modules/lessons/views/videosummary.block.php');

	$html .= "<div class='block'><h2>" . $model->title . "</h2></div><div class='spacer'></div>";

	foreach($model->documents as $doc) {

		$doc['itemLink'] = ''
		 . '%%serverPath%%lessons/item'
		 . '/course_' . $model->UID
		 . '/document_' . $doc['uid'] . '/';

		$html .= $theme->replaceLabels($doc, $block);

		/*

		$html .= ''
		 . "<div class='block'>"
		 . "<table noborder>\n"
		 . "  <tr>\n"
		 . "    <td valign='top' width='50px'>\n"
		 . "      <a href='$itemLink'>\n"
		 . "      <img src='%%serverPath%%" . $doc['thumb'] . "' width='50px' class='rounded' />\n"
		 . "      </a>\n"
		 . "    </td>\n"
		 . "    <td valign='top' width='300px'>\n"
		 . "      <a href='$itemLink'>" . $doc['title'] . "</a>"
		 . "      <small>" . $doc['description'] . "</small>"
		 . "    </td>\n"
		 . "  </tr>\n"
		 . "</table>\n"
		 . "</div>\n";
		*/

	}


	return $html;
}

?>
