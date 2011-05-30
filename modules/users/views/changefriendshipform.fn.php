<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display form to change the parameters of a friendship record
//--------------------------------------------------------------------------------------------------
//arg: friendshipUID - UID of relationship record [string]

function users_changefriendshipform($args) {
	global $db, $theme, $user;
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('friendshipUID', $args)) { return ''; }
	if (false == $db->objectExists('users_friendship', $args['friendshipUID'])) { return ''; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$model = new Users_Friendship($args['friendshipUID']);
	$block = $theme->loadBlock('modules/users/views/changefriendshipform.block.php');
	$html = $theme->replaceLabels($model->extArray();, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
