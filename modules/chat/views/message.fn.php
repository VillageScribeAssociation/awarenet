<?

	require_once($kapenta->installPath . 'modules/chat/models/inbox.mod.php');

//--------------------------------------------------------------------------------------------------
//|	render a single chat message from a user inbox
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Chat_Inbox (message) object [string]

function chat_message($args) {
	global $theme;
	global $user;
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }
	$model = new Chat_Inbox($args['UID']);
	if (false == $model->loaded) { return '(message not found)'; }
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/chat/views/message.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	return $html;
}

?>
