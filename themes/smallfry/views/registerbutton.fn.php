<?php

//-------------------------------------------------------------------------------------------------
//|	dirty hack for the smallfry theme TODO: fix
//-------------------------------------------------------------------------------------------------

function theme_registerbutton($args) {
	global $registry;
	global $user;

	if ('public' != $user->role) { return ''; }
	if ('yes' != $registry->get('users.allowpublicsignup')) { return ''; }

	$html = ''
	 . "<a href='%%serverPath%%users/signup/'>"
	 . "<img src='%%serverPath%%themes/%%defaultTheme%%/images/buttons/btn_register.png' border='0px' />"
	 . "</a>"
	 . "<br/><br/>\n";

	return $html;
}

?>
