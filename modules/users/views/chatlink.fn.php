<?

//-------------------------------------------------------------------------------------------------
//|	makes link for chatting with this user		//TODO: fix this up
//-------------------------------------------------------------------------------------------------
//arg: userUID - UID of the user whose login status we're cheking [string]

function users_chatlink($args) {
	global $db;
	$html = '';

	if (false == array_key_exists('userUID', $args)) { return ''; }

	$sql = "select * from Users_Login where userUID='" . $db->addMarkup($args['userUID']) . "'";
	$result = $db->query($sql);
	if ($db->numRows($result) > 0) {
		//-----------------------------------------------------------------------------------------
		// user is logged in to at least one awareNet server
		//-----------------------------------------------------------------------------------------
		$html .= "<a href='#' onClick=\"kchatclient.createDiscussion('" . $args['userUID'] . "');\">[chat]</a>";
		//$html .= '[online]';
		
	} else {
		//-----------------------------------------------------------------------------------------
		// user is offline
		//-----------------------------------------------------------------------------------------
		$html .= '[offline]';
				
	}


	return $html;
}

?>
