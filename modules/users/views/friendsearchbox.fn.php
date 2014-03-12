<?php

//--------------------------------------------------------------------------------------------------
//*	show a user search box for adding friends
//--------------------------------------------------------------------------------------------------
//arg: userUID - user context this request was made in - only shown on own page [string]

function users_friendsearchbox($args) {
	global $kapenta;
	global $theme;
	
	$html = '';							//%	rreturn value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { return ''; }

	if (false == array_key_exists('userUID', $args)) { return '(missing user context)'; }

	$userUID = $args['userUID'];
	if ($userUID != $kapenta->user->UID) { return ''; }

	//---------------------------------------------------------------------------------------------
	//	make the block
	//---------------------------------------------------------------------------------------------

	$html = ''
	 . "<script src='%%serverPath%%modules/users/js/friends.js'></script>"
	 . "[[:users::searchbox"
	 . "::cbicon=arrow_down_green.png"
	 . "::cbjs=users_showFriendRequestForm"
	 . ":]]\n";

	$html = $theme->ntb($html, 'Search for friends', 'divFriendSearch', 'show');

	return $html;
}

?>
