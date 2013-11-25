<?

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show tree of children starting at the root of a given package
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Code_Package object [string]
//opt: packageUID - overrides raUID if present [string]
//opt: UID - overrides raUID if present [string]

function code_showpackagetree($args) {
	global $db;
	$html = '';			//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('packageUID', $args)) { $args['raUID'] = $args['packageUID']; }
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return '(package not specified)'; }

	$model = new Code_Package($args['raUID']);
	if (false == $model->loaded) { return '(package not found)'; }

	$html = "[[:code::showtree::itemUID=" . $model->getRootFolder() . ":]]";	
	return $html;
}


//--------------------------------------------------------------------------------------------------

?>
