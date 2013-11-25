<?
	
	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the icon appropriate to a given Code_File object
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Code_File object [string]
//opt: fileUID - overrides raUID if present [string]
//opt: UID - overrides raUID if present [string]
//opt: link - include link to display this item (yes|no) [string]

function code_showfileicon($args) {
	global $theme;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('fileUID', $args)) { $args['raUID'] = $args['fileUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(file UID not specified)'; }

	$model = new Code_File($args['raUID']);
	if (false == $model->loaded) { return '(unknown file: ' . $args['raUID'] . ' (' . $model->UID . '))'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$icon = 'unknown';	

	switch($model->type) {
		case 'folder':	$icon = 'folder';
	}

	$html = "<img src='%%serverPath%%modules/code/icons/" . $icon . ".png' border='0' />";

	return $html;
}

?>
