<?

//--------------------------------------------------------------------------------------------------
//|	form for granting users commit privileges on packages
//--------------------------------------------------------------------------------------------------
//ofgroup: admin
//arg: raUID - alias or UID of a Code_Package object [string]
//opt: packageUID - overrides raUID if present [string]
//opt: UID - overrides raUID if present [string]

function code_addpackageuserform($args) {
	global $user;
	global $theme;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('packageUID', $args)) { $args['raUID'] == $args['packageUID']; }
	if (true == array_key_exists('UID', $args)) { $args['raUID'] == $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return '(package not specified)'; }
	
	$model = new Code_Package($args['raUID']);
	if (false == $model->loaded) { return '(unkown package)'; }

	//TODO: permissions check here
	if ($user->role != 'admin') { return ''; }
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/code/views/addpackageuserform.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------


?>
