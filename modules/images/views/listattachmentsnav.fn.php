<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/models/images.set.php');

//--------------------------------------------------------------------------------------------------
//|	list attachments, formatted for nav (expect 300px wide)
//--------------------------------------------------------------------------------------------------
//arg: refModule - module of object which may have attached images [string]
//arg: refModel - type of object which may have attached images [string]
//arg: refUID - UID of object which may have attached images [string]

function images_listattachmentsnav($args) {
	global $kapenta;
	global $user;
	global $kapenta;
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
	//	load images attached to this object
	//----------------------------------------------------------------------------------------------

	$set = new Images_Images($refModule, $refModel, $refUID);
	if (0 == $set->count()) { return ''; }
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if ($set->count() > 2) {
		//------------------------------------------------------------------------------------------
		//	show small preview on longer lists
		//------------------------------------------------------------------------------------------
		foreach($set->members as $item) {
			$html .= ''
			 . "[[:images::summarynav::imageUID=" . $item['UID'] . "::behavior=editmodal:]]"
			 . "<div class='imagesspacer'></div>";
		}		

	} else {
		//------------------------------------------------------------------------------------------
		//	show large preview for first two items
		//------------------------------------------------------------------------------------------
		$page->requireJs('%%serverPath%%modules/images/js/editor.js');
		foreach($set->members as $item) {
			$html .= ''
			 . "[[:images::summarynavlarge::imageUID=" . $item['UID'] . "::behavior=editmodal:]]";
		}
	}

	$html = "\n$html<hr/>\n";

	return $html;
}

?>
