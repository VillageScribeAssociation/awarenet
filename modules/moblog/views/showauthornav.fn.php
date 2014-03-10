<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary of author in the nav
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of post [string]
//opt: postUID - overrides raUID [string]

function moblog_showauthornav($args) {
		global $user;
		global $aliases;

	$html = '';				//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions	
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('postUID', $args)) { $args['raUID'] = $args['postUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Moblog_Post($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('moblog', 'moblog_post', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$userUID = $model->createdBy;
	$userRa = $aliases->getDefault('users_user', $userUID);

	$html = "<a href='/moblog/blog/" . $userRa . "'>";
	$html .= "[[:images::default::refUID=" . $userUID . "::size=width300::link=no:]]</a>\n";
	$html .= "[[:users::summarynav::userUID=" . $userUID . ":]]\n";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

