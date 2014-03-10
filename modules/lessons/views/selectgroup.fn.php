<?php

//--------------------------------------------------------------------------------------------------
//*	make a form element to select a course group
//--------------------------------------------------------------------------------------------------
//opt: default - name of a course type

function lessons_selectgroup($args) {
	global $kapenta;

	$default = 'videolessons';
	$html = '';								//%	return value [string]

	$groups = explode('|', $kapenta->registry->get('lessons.groups'));

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------

	if (true == array_key_exists('default', $args)) { $defualt = $args['default']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	
	$html .= "<select name='group'>\n";
	foreach($groups as $group) {
		if ($default == $group) {
			$html .= "<option value='$group' selected='selected'>$group</option>\n";
		} else {
			$html .= "<option value='$group'>$group</option>\n";
		} 
	}
	$html .= "</select>\n";

	return $html;
}

?>
