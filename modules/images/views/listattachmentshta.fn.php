<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/models/images.set.php');

//--------------------------------------------------------------------------------------------------
//|	list images attached to something formatted for injection into HyperTextArea
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have images attached [string]
//arg: refUID - UID of object which may have images attached [string]
//arg: hta - name of a a HyperTextArea on calling page [string]

function images_listattachmentshta($args) {
	global $kapenta;
	global $kapenta;
	global $theme;
	global $kapenta;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(images: no refModule given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(images: no refModel given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(images: no refUID given)'; }
	if (false == array_key_exists('hta', $args)) { return '(images: no hta given)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];
	$hta = $args['hta'];

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
	foreach($set->members as $item) {
		$html .= ''
		 . '[[:images::summaryhta'
		 . '::model=images_image'
		 . '::UID=' . $item['UID']
		 . '::hta=' . $hta
		 . ':]]';
	}

	$html = "\n$html<hr/>\n";

	return $html;
}

?>
