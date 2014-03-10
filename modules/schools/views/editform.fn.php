<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the edit form
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or schools entry [string]

function schools_editform($args) {
		global $theme;
		global $user;
		global $utils;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Schools_School($args['raUID']);
	if (false == $user->authHas('schools', 'schools_school', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$ext['descriptionJs64'] = $utils->base64EncodeJs('descriptionJs64', $ext['description']);
	$ext['description64'] = $utils->b64wrap($ext['description']);
	$block = $theme->loadBlock('modules/schools/views/editform.block.php');
	$html = $theme->replaceLabels($ext, $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
