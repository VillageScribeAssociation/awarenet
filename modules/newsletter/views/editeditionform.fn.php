<?

	require_once($kapenta->installPath . 'modules/newsletter/models/edition.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a Edition object
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Newsletter_Edition object [string]
//opt: UID - UID of a Newsletter_Edition object, overrides raUID [string]
//opt: editionUID - UID of a Newsletter_Edition object, overrides raUID [string]

function newsletter_editeditionform($args) {
	global $user;
	global $utils;
	global $theme;

	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	$raUID = '';
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('editionUID', $args)) { $raUID = $args['editionUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Newsletter_Edition($raUID);	//% the object we're editing [object:Newsletter_Edition]

	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('newsletter', 'newsletter_edition', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/newsletter/views/editeditionform.block.php');
	$labels = $model->extArray();
	$labels['abstract64'] = $utils->b64wrap($labels['abstract']);
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
