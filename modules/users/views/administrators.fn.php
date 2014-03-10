<?

//--------------------------------------------------------------------------------------------------
//|	make a list of administrators, links to profiles
//--------------------------------------------------------------------------------------------------

function users_administrators($args) {
	global $kapenta;
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array("role='" . $kapenta->db->addMarkup('admin') . "'");
	$range = $kapenta->db->loadRange('users_user', '*', $conditions, 'firstname, surname');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$links = array();
	foreach($range as $item) {
		$links[] = '[[:users::namelink::userUID=' . $item['UID'] . ':]]';
	}

	$html = implode(", ", $links);

	return $html;
}


?>
