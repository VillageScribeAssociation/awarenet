<?php

//--------------------------------------------------------------------------------------------------
//*	list all courses in a media group by subject and grade
//--------------------------------------------------------------------------------------------------

function lessons_listcoursesnav($args) {
	global $kapenta;

	$subject = '';
	$grade = '';
	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if ('public' == $users->role) { return '[[:users::pleaselogin:]]'; }
	if (false == array_key_exists('group', $args)) { return '(group not specified)'; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------

	$conditions = array("mediagroup='" . $args['group'] . "'");
	$range = $kapenta->db->loadRange('lessons_collection', '*', $conditions, 'subject, grade');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	foreach($range as $item) {

		if ($item['subject'] != $subject) {
			$subject = $item['subject'];
			$html .= "<h2>$subject</h2>";
		}

		if ($item['grade'] != $grade) {
			$grade = $item['grade'];
			$html .= "<b>grade</b><br/>";
		}

		$html .= ''
		 . "<a href='%%serverPath%%lessons/showcourse/" . $item['UID'] . "'>"
		 . $item['title']
		 . "</a><br/>";
	}

	return $html;
}

?>
