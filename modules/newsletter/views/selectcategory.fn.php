<?php

//--------------------------------------------------------------------------------------------------
//|	creates an HTML form for selecting a notice category
//--------------------------------------------------------------------------------------------------
//opt: default - UID of a Newsletter_Category object [string] 

function newsletter_selectcategory($args) {
	global $user;
	global $kapenta;

	$default = '';
	$html = '';									//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }

	//----------------------------------------------------------------------------------------------
	//	load all categories
	//----------------------------------------------------------------------------------------------

	$range = $kapenta->db->loadRange('newsletter_category', '*', '', 'name');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html .= "<select name='category'>\n";
	foreach($range as $item) {
		$selected = '';
		if ($default == $item['UID']) { "selected='selected'"; }
		$html .= "<option value='" . $item['UID'] . "'$selected>" . $item['name'] . "</option>\n";
	}
	$html .= "</select>\n";

	return $html;
}

?>
