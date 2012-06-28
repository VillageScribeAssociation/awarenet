<?

//--------------------------------------------------------------------------------------------------
//|	simple flat list of tags owned by something
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have tags [string]
//arg: refUID - UID of object which may have tags [string]

function tags_listflat($args) {
	global $kapenta;
	global $db;
	global $theme;
	global $user;

	$link = 'module';		//%	by default allow owner to display tagged items [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }
	
	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { return '(no such module)'; }
	if (false == $db->objectExists($refModel, $refUID)) { return '(no such owner)'; }
	if (false == $user->authHas($refModule, $refModel, 'tags-manage', $refUID)) { return ''; }

	if (true == array_key_exists('link', $args)) { $link = $args['link']; }

	//----------------------------------------------------------------------------------------------
	//	load any tags from database
	//----------------------------------------------------------------------------------------------	
	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";

	$range = $db->loadRange('tags_index', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------	

	if (0 == count($range)) { return ''; }

	$blocks = array();
	foreach($range as $row) {
		$blocks[] = "[[:tags::name::tagUID=". $row['tagUID'] ."::link=$link::module=$refModule:]]";
	}

	$html = ''
	 . "<div id='divTags$refUID' style='text-align: right; padding-top: 5px; padding-bottom: 3px;'>"
	 . implode(" ", $blocks)
	 . "</div>";

	return $html;
}

?>
