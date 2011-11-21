<?

//-------------------------------------------------------------------------------------------------
//|	makes link for chatting with this user		//TODO: fix this up
//-------------------------------------------------------------------------------------------------
//arg: userUID - UID of the user whose login status we're cheking [string]

function users_chatlink($args) {
	global $db;
	global $user;

	$html = '';									//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return ''; }
	if ('public' == $user->role) { return ''; }		// do not disclose online status to public

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "status='active'";
	$conditions[] = "createdBy='" . $db->addMarkup($args['userUID']) . "'";
	$range = $db->loadRange('users_session', '*', $conditions);

	//$sql = "select * from users_login where userUID='" . $db->addMarkup($args['userUID']) . "'";

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) {
		$html .= '[offline]';		
	} else {
		$html .= ''
		 . "<a href='#' "
		 . "onClick=\"kchatclient.createDiscussion('" . $args['userUID'] . "');\""
		 . ">[chat]</a>";
	}

	return $html;
}

?>
