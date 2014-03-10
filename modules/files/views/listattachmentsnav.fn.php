<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');
	require_once($kapenta->installPath . 'modules/files/models/files.set.php');

//--------------------------------------------------------------------------------------------------
//|	list attachments, formatted for nav (expect 300px wide)
//--------------------------------------------------------------------------------------------------
//arg: refModule - module of object which may have attached files [string]
//arg: refModel - type of object which may have attached files [string]
//arg: refUID - UID of object which may have attached files [string]

function files_listattachmentsnav($args) {
	global $kapenta;
	global $user;
	global $kapenta;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('refModule', $args)) { return '(no refModule given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID given)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

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
		 . "[[:files::summarynav::fileUID=" . $item['UID'] . "::behavior=editmodal:]]"
		 . "<div class='spacer'></div>";
	}		

	$html = "\n$html<hr/>\n";

	return $html;
}

?>
