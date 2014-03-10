<?

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a Package object
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Code_Package object [string]
//opt: UID - UID of a Code_Package object, overrides raUID [string]
//opt: packageUID - UID of a Code_Package object, overrides raUID [string]

function code_editpackageform($args) {
	global $user;
	global $theme;
	global $utils;

	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	$raUID = '';
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('packageUID', $args)) { $raUID = $args['packageUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Code_Package($raUID);	//% the object we're editing [object:Code_Package]

	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('code', 'code_package', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/code/views/editpackageform.block.php');
	$labels = $model->extArray();
	$labels['descriptionJs64'] = $utils->base64EncodeJs(
		'descriptionJs64', 
		$labels['description'], 
		true
	);
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
