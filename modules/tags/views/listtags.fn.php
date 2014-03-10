<?

//--------------------------------------------------------------------------------------------------
//|	list all tags belonging to a specified object
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have tags [string]
//arg: refUID - UID of object which may have tags [string]

function tags_listtags($args) {
		global $kapenta;
		global $kapenta;
		global $theme;
		global $user;

	$html = '';		//%	return value [string]

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
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { return '(no such owner)'; }
	if (false == $user->authHas($refModule, $refModel, 'tags-manage', $refUID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load any tags from database
	//----------------------------------------------------------------------------------------------	
	$conditions = array();
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($refModule) . "'";
	$conditions[] = "refModel='" . $kapenta->db->addMarkup($refModel) . "'";
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($refUID) . "'";

	$range = $kapenta->db->loadRange('tags_index', '*', $conditions);
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if (0 == count($range)) { 
		$html .= "(this item has no tags as yet)<br/><br/>";

	} else {
		$table = array();
		$table[] = array('Tag', 'Mag', 'Added by', 'Added on', '[x]');

		foreach($range as $row) {
			$rmUrl = "%%serverPath%%tags/removetag/" . $row['UID'];
			$rmLink = "<a href='" . $rmUrl . "'>[untag]</a>";
			$tagName = "[[:tags::name::tagUID=" . $row['tagUID'] . ":]]";
			$tagCount = "[[:tags::count::tagUID=" . $row['tagUID'] . ":]]";
			$userName = "[[:users::name::userUID=" . $row['createdBy'] . ":]]";
			$table[] = array($tagName, $tagCount, $userName, $row['createdOn'], $rmLink);
		}

		$html = $theme->arrayToHtmlTable($table, true, true);
	}

	return $html;
}


?>
