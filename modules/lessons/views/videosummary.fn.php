<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display video summary information for a course
//--------------------------------------------------------------------------------------------------
//args: UID - UID of a Lessons_Course object [string]

function lessons_videosummary($args) {
	global $session;

	$profile = $session->get('deviceprofile');	//% [string]
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
	$count = 10;
	$odd = false;

	$html .= "<table noborder width='100%'>\n  <tr>\n";

	foreach($model->documents as $doc) {

		$itemLink = ''
		 . '%%serverPath%%lessons/item'
		 . '/course_' . $model->UID
		 . '/document_' . $doc['uid'] . '/';

		if ('desktop' == $profile) {

			$html .= ''
			 . "    <td valign='top' width='50px'>\n"
			 . "      <a href='$itemLink'>\n"
			 . "      <img src='%%serverPath%%" . $doc['thumb'] . "' width='50px' class='rounded' />\n"
			 . "      </a>\n"
			 . "    </td>\n"
			 . "    <td valign='top' width='300px'>\n"
			 . "      <a href='$itemLink'>" . $doc['title'] . "</a>"
			 . "      <small>" . $doc['description'] . "</small>"
			 . "    </td>\n";

		} else {

			$html .= ''
			 . "    <td valign='top' width='140px'>\n"
			 . "      <a href='$itemLink'>\n"
			 . "      <img src='%%serverPath%%" . $doc['thumb'] . "' width='140px' class='rounded' />\n"
			 . "      </a>\n"
			 . "      <a href='$itemLink'>" . $doc['title'] . "</a>"
			 . "      <small>" . $doc['description'] . "</small>"
			 . "    </td>\n";

		}

		if (true == $odd) { $html .= "  </tr>\n  <tr>\n"; }

		

		$odd = !$odd;
	}

	$html .= "</table>\n";;

	return $html;
}

?>
