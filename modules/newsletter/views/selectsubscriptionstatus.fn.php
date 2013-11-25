<?php

//--------------------------------------------------------------------------------------------------
//|	Makes an HTML select element showing possible subscription status types
//--------------------------------------------------------------------------------------------------
//opt: status - current status [string]

function newsletter_selectsubscriptionstatus($args) {
	global $user;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the form element
	//----------------------------------------------------------------------------------------------
	$options = array(
		'subscribed',
		'unconfirmed',
		'cancelled',
		'removed'
	);

	$html .= "<select name='status'>\n";
	foreach($options as $item) { $html .= "<option value='$item'>$item</option>\n"; }
	$html .= "</select>\n";

	return $html;
}

?>
