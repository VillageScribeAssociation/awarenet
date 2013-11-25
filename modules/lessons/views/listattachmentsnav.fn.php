<?

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');
	require_once($kapenta->installPath . 'modules/lessons/models/stub.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list attachments, formatted for nav (expect 300px wide)
//--------------------------------------------------------------------------------------------------
//arg: refModule - module of object which may have attached resources (ie, lessons)) [string]
//arg: refModel - type of object which may have attached resources (ie, Lessons_Collection) [string]
//arg: refUID - UID of Lessons_Collection object [string]

function lessons_listattachmentsnav($args) {
	global $kapenta;
	global $user;
	global $db;
	global $theme;

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

	if ('lessons' != $refModule) { return ''; }
	if (false == $kapenta->moduleExists($refModule)) { return "(Unknown module: $refModule)"; }
	if (false == $db->objectExists($refModel, $refUID)) { return "(missing owner object)"; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	load videos attached to this object
	//----------------------------------------------------------------------------------------------

	$set = new Lessons_Course($refModule, $refModel, $refUID);
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
			 . "[[:lessons::stubsummarynav::stubUID=" . $item['UID'] . "::behavior=editmodal:]]"
			 . "<div class='spacer'></div>";
		}

	} else {
		//------------------------------------------------------------------------------------------
		//	show large preview for first two items
		//------------------------------------------------------------------------------------------
		foreach($set->members as $item) {
			//TODO: large view
			$html .= ''
			 . "[[:lessons::stubsummarynav::area=nav1::stubUID=" . $item['UID'] . ":]]"
			 . "<div class='spacer'></div>\n<br/>";
		}
	}

	$html = "\n$html<hr/>\n";

	$html = $theme->expandBlocks($html, 'nav1');

	return $html;
}

?>
