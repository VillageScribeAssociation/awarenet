<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	editform
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of post to edit [string]

function moblog_editform($args) {
		global $theme;
		global $user;
		global $utils;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Moblog_Post($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('moblog', 'moblog_post', 'edit', $model->UID)) { return ''; }

	if (('' == $model->content) && ('no' == $model->published)) {
		$model->published = 'yes';
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$ext['content64'] = $utils->b64wrap($ext['content']);
	$block = $theme->loadBlock('modules/moblog/views/editform.block.php');
	$html = $theme->replaceLabels($ext, $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
