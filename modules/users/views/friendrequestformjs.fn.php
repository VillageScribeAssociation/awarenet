<?php

	require_once($kapenta->installPath . 'modules/users/models/friendships.set.php');

//--------------------------------------------------------------------------------------------------
//|	show form to make a friend request via AJAX
//--------------------------------------------------------------------------------------------------
//arg: friendUID - UID of a Users_User object to make friend request with [string]

function users_friendrequestformjs($args) {
	global $kapenta;
	global $kapenta;
	global $theme;

	$ajw = "<span class='ajaxwarn'>";	//%	for error messages [string]
	$friendUID = '';					//%	UID of a Users_User object [string]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments, user role and existing request / relationship
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { return ''; }
	if (false == array_key_exists('friendUID', $args)) { return '(friendUID not specified)'; }

	$friendUID = $args['friendUID'];

	if (false == $kapenta->db->objectExists('users_user', $friendUID)) { return '(friend is unknown user)'; }

	$set = new Users_Friendships($kapenta->user->UID);
	
	if (true == $set->hasConfirmed($friendUID)) { return $ajw . "You are already friends.</span>"; }
	if (true == $set->hasUnconfirmed($friendUID)) { return $ajw . "Already requested.</span>"; }
	if ($friendUID == $kapenta->user->UID) { return $ajw . "You cannot friend yourself.</span>"; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	
	$block = $theme->loadBlock('modules/users/views/friendrequestformjs.block.php');
	$html = $theme->replaceLabels($args, $block);

	return $html;
}


?>
