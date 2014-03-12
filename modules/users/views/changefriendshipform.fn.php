<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display form to change the parameters of a friendship record
//--------------------------------------------------------------------------------------------------
//arg: friendshipUID - UID of relationship record [string]

function users_changefriendshipform($args) {
		global $kapenta;
		global $theme;
		global $kapenta;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('friendshipUID', $args)) { return ''; }
	if (false == $kapenta->db->objectExists('users_friendship', $args['friendshipUID'])) { return ''; }
	//TODO: permissions check here

	$model = new Users_Friendship($args['friendshipUID']);
	if (false == $model->loaded) { return '(friendship not found)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/users/views/changefriendshipform.block.php');
	$labels = $model->extArray();
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
