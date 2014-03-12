<?

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display package details
//--------------------------------------------------------------------------------------------------
//arg: raUID - alais or UID of a Code_Package object [string]
//opt: UID - overrides raUID if present [string]
//opt: packageUID - overrides raUID if present [string]

function code_showpackage($args) {
	global $theme;
	global $kapenta;

	$html = '';			//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('packageUID', $args)) { $args['raUID'] = $args['packageUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(no package specified)'; }

	$model = new Code_Package($args['raUID']);
	if (false == $model->loaded) { return '(unkown package)'; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$block = $theme->loadBlock('modules/code/views/showpackage.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
