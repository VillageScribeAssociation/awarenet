<?

	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a Thread object
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Forums_Thread object [string]
//opt: UID - UID of a Forums_Thread object, overrides raUID [string]
//opt: threadUID - UID of a Forums_Thread object, overrides raUID [string]

function forums_editthreadform($args) {
    global $kapenta;

	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	$raUID = '';
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('threadUID', $args)) { $raUID = $args['threadUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Forums_Thread($raUID);	//% the object we're editing [object:Forums_Thread]

	if (false == $model->loaded) { return ''; }
	if (false == $kapenta->user->authHas('forums', 'forums_thread', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $kkapenta->theme->loadBlock('modules/forums/views/editthreadform.block.php');
	$labels = $model->extArray();
	$labels['UIDJsClean'] = $model->UID;
	$labels['content64'] = $kapenta->utils->b64wrap($labels['content']);
	// ^ add any labels, block args, etc here

	$html = $kapenta->theme->replaceLabels($labels, $block);

	return $html;
}

?>
