<?

//-------------------------------------------------------------------------------------------------
//	makes link for chatting with this user
//-------------------------------------------------------------------------------------------------
//arg: userUID - UID of the user whose login status we're cheking

function users_chatlink($args) {
	if (array_key_exists('userUID', $args) == false) { return ''; }
	$html = '';

	$sql = "select * from userlogin where userUID='" . sqlMarkup($args['userUID']) . "'";
	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		//-----------------------------------------------------------------------------------------
		// user is logged in to at least one awareNet server
		//-----------------------------------------------------------------------------------------
		$html .= "<a href='#' onClick=\"chatStart('" . $args['userUID'] . "');\">[chat]</a>";
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
