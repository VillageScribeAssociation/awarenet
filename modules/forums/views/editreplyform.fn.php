<?

	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing an already submitted reply
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a forum reply [string]
//opt: replyUID - overrides UID if present [string]

function forums_editReplyForm($args) {
	global $user;	
	global $theme;
	global $utils;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return '(Reply not specified)'; }

	$model = new Forums_Reply($args['UID']);
	if (false == $model->loaded) { return '(Reply not found)'; }

	//TODO: check user and time

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/forums/views/editreplyform.block.php');
	$ext = $model->extArray();
	$ext['content64'] = $utils->b64wrap($ext['content']);
	$html = $theme->replaceLabels($ext, $block);
	return $html;
}

?>
