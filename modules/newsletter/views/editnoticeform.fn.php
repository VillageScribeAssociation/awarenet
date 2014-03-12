<?

	require_once($kapenta->installPath . 'modules/newsletter/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a Notice object
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Newsletter_Notice object [string]
//opt: noticeUID - UID of a Newsletter_Notice object, overrides UID [string]

function newsletter_editnoticeform($args) {
	global $kapenta;
	global $utils;
	global $theme;

	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	$raUID = '';
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('noticeUID', $args)) { $raUID = $args['noticeUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Newsletter_Notice($raUID);	//% the object we're editing [object:Newsletter_Notice]

	if (false == $model->loaded) { return ''; }
	if (false == $kapenta->user->authHas('newsletter', 'newsletter_notice', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/newsletter/views/editnoticeform.block.php');
	$labels = $model->extArray();
	$labels['content64'] = $utils->b64wrap($labels['content']);
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
