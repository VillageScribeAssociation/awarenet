<?

//--------------------------------------------------------------------------------------------------
//*	displays dependant deleted items (eg, deleted comments on a deleted blog post)
//--------------------------------------------------------------------------------------------------
//arg: ownerUID - UID of a deleted object which may own other deleted objects [string]

function revisions_listdependantnav($args) {
	global $kapenta;
	global $user;
	global $revisions;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('ownerUID', $args)) { return '(no owner UID)'; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array("owner='" . $kapenta->db->addMarkup($args['ownerUID']) . "'");
	$range = $kapenta->db->loadRange('revisions_deleted', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	foreach($range as $item) {
		$itemUrl = '%%serverPath%%revisions/showdeleted/' . $item['UID'];
		$label = $item['refModule'] . '::' . $item['refModule'] . '::' . $item['refUID'];
		$itemLink = "<a href='" . $itemUrl . "'>" . $label . "</a>";
		$html .= $itemLink . "<br/>\n";
	}

	if (0 == count($range)) {
		$html .= "<div class='inlinequote'>None found.</div>\n";
	}

	return $html;
}

?>
