<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarize an item for inserting into a HyperTextArea
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID or alias of a Files_File object [string]
//arg: hta - name of a HyperTextArea on the client page [string]
//opt: model - type of object to be inserted (will always be files_file for this module) [string]
//opt: UID - overrides raUID if present [string]
//opt: fileUID - overrides raUID if present [string]

function files_summaryhta($args) {
	global $kapenta;
	global $theme;
	
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { return ''; }

	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('fileUID', $args)) { $args['raUID'] = $args['fileUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(no file specified)'; }
	if (false == array_key_exists('hta', $args)) { return '(no hta specified)'; }

	$model = new Files_File($args['raUID']);
	if (false == $model->loaded) { return '(file not found)'; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/files/views/summaryhta.block.php');
	
	$labels = $model->extArray();
	$labels['hta'] = $args['hta'];

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
