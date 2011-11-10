<?

	require_once($kapenta->installPath . 'modules/images/inc/imageset.class.php');

//--------------------------------------------------------------------------------------------------
//|	AJAX UI for selecting which profile picture should be the default
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Users_User object [string]
//opt: userUID - overrides raUID if present [string]

function users_chooseavatar($args) {
	global $theme;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('userUID', $args)) { $args['raUID'] = $args['userUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(unkown user)'; }

	$model = new Users_User($args['raUID']);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$imgset = new Images_Imageset('users', 'users_user', $model->UID);
	
	if (0 == $imgset->count) {
		$html = $theme->loadBlock('modules/users/views/addapicture.block.php');
		return $html;
	}

	$html .= "<div id='divChooseAvatar'>";
	$html .= "[[:users::avatar::userUID=" . $model->UID . "::size=width300:]]";

	if (1 == $imgset->count) {
		$html .= "</div>";
		return $html;
	}

	$html .= "<p>Click on the thumbnails below to set your default profile picture.</p>";

	foreach($imgset->images as $objArray) {
		$block = "[[:images::thumbsm::imageUID=" . $objArray['UID'] . "::link=no:]]";
		$onClick = "users_setDefaultPicture('" . $model->UID . "', '" . $objArray['UID'] . "');";
		$html .= "<a href='javascript:void(0);' onClick=\"$onClick\">$block</a>";
	}

	$html .= "</div>"; 

	return $html;
}


?>
