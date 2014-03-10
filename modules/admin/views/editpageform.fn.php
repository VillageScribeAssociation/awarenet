<?

	require_once($kapenta->installPath . 'core/kpage.class.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing pages
//--------------------------------------------------------------------------------------------------

function admin_editpageform($args) {
		global $theme;
		global $kapenta;
		global $utils;
		global $user;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('xmodule', $args)) { return '(xmodule not specified)'; }
	if (false == array_key_exists('xpage', $args)) { return '(xpage not specified)'; }

	$fileName = 'modules/' . $args['xmodule'] . '/actions/' . $args['xpage'];
	if (false == $kapenta->fs->exists($fileName)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the page
	//----------------------------------------------------------------------------------------------
	$model = new KPage($fileName);
	if (false == $model->loaded) { return '(could not load page)'; }

	$labels = $model->toArray();
	$labels['moduleName'] = $args['xmodule'];
	$labels['pageName'] = $args['xpage'];

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	// sanitize content (prevent blocks running, </textarea>)
	foreach($labels as $label => $txt) {
		// for loading via javascript
		$labels[$label . 'Js64'] = $utils->base64EncodeJs($label . 'Js64', $txt, false);

		$labels[$label] = str_replace('[', '&#91;', $labels[$label]);			
		$labels[$label] = str_replace(']', '&#93;', $labels[$label]);			
		$labels[$label] = str_replace('<', '&lt;', $labels[$label]);			
		$labels[$label] = str_replace('>', '&gt;', $labels[$label]);
	}

	$block = $theme->loadBlock('modules/admin/views/editpageform.block.php');	// load form
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

?>
