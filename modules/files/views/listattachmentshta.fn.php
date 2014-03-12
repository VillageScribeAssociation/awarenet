<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');
	require_once($kapenta->installPath . 'modules/files/models/files.set.php');

//--------------------------------------------------------------------------------------------------
//|	list files attached to something formatted for injection into HyperTextArea
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have files attached [string]
//arg: refUID - UID of object which may have files attached [string]
//arg: hta - name of a a HyperTextArea on calling page [string]

function files_listattachmentshta($args) {
	global $kapenta;
	global $kapenta;
	global $theme;
	global $kapenta;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(files: no refModule given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(files: no refModel given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(files: no refUID given)'; }
	if (false == array_key_exists('hta', $args)) { return '(files: no hta given)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];
	$hta = $args['hta'];

	if (false == $kapenta->moduleExists($refModule)) { return "(Unknown module: $refModule)"; }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { return "(missing owner object)"; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	load files attached to this object
	//----------------------------------------------------------------------------------------------

	$set = new Files_Files($refModule, $refModel, $refUID);
	if (0 == $set->count()) { return ''; }
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	foreach($set->members as $item) {
		$html .= ''
		 . '[[:files::summaryhta'
		 . '::model=files_file'
		 . '::UID=' . $item['UID']
		 . '::hta=' . $hta
		 . ':]]';
	}

	$html = "\n$html<hr/>\n";

	return $html;
}

?>
