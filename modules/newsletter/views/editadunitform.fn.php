<?

	require_once($kapenta->installPath . 'modules/newsletter/models/adunit.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a Adunit object
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Newsletter_Adunit object [string]
//opt: adunitUID - UID of a Newsletter_Adunit object, overrides UID [string]

function newsletter_editadunitform($args) {
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
	if (true == array_key_exists('adunitUID', $args)) { $raUID = $args['adunitUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Newsletter_Adunit($raUID);	//% the object we're editing [object:Newsletter_Adunit]

	if (false == $model->loaded) { return ''; }
	if (false == $kapenta->user->authHas('newsletter', 'newsletter_adunit', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/newsletter/views/editadunitform.block.php');
	$labels = $model->extArray();
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
