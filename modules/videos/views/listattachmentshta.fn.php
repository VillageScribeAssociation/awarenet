<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');
	require_once($kapenta->installPath . 'modules/videos/models/videos.set.php');

//--------------------------------------------------------------------------------------------------
//|	list videos attached to something formatted for injection into HyperTextArea
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have videos attached [string]
//arg: refUID - UID of object which may have videos attached [string]
//arg: hta - name of a a HyperTextArea on calling page [string]

function videos_listattachmentshta($args) {
	global $kapenta;
	global $user;
	global $theme;
	global $kapenta;
	global $kapenta;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(videos: no refModule given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(videos: no refModel given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(videos: no refUID given)'; }
	if (false == array_key_exists('hta', $args)) { return '(videos: no hta given)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];
	$hta = $args['hta'];

	if (false == $kapenta->moduleExists($refModule)) { return "(Unknown module: $refModule)"; }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { return "(missing owner object)"; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	load videos attached to this object
	//----------------------------------------------------------------------------------------------
	$kapenta->page->requireJs('%%serverPath%%modules/videos/js/editor.js');
	$set = new Videos_Videos($refModule, $refModel, $refUID);
	if (0 == $set->count()) { return ''; }
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/videos/views/summaryhta.block.php');


	foreach($set->members as $item) {
		$html .= "[[:videos::summaryhta::model=videos_video::hta=$hta::UID=" . $item['UID'] . ":]]";
	}

	$html = "\n$html<hr/>\n";

	return $html;
}

?>
