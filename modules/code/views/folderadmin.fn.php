<?

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');
	
//--------------------------------------------------------------------------------------------------
//|	folder admin controls
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Code_File entry [string]
//opt: folderUID - alias or UID of a Code_File entry, overrides raUID if present [string]
//opt: UID - overrides raUID if present [string]

function code_folderadmin($args) {
	global $kapenta;
	global $theme;

	$html = '';						//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ($kapenta->user->role != 'admin') { return ''; }
	if (true == array_key_exists('folderUID', $args)) { $args['raUID'] = $args['folderUID']; }
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return '(folder UID not given)'; }

	$model = new Code_File($args['raUID']);
	if (false == $model->loaded) { return '(folder not found)'; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/code/views/folderadmin.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
