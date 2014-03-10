<?

//--------------------------------------------------------------------------------------------------
//|	show tag could for entire system, a module, model or single object
//--------------------------------------------------------------------------------------------------
//opt: num - maximum number of tags to display, default is 20 (int)[string]

function tags_searchcloud($args) {
		global $kapenta;
		global $theme;
		global $kapenta;

	$html = '';
	$num = 20;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	load from database
	//----------------------------------------------------------------------------------------------
	$conditions = array("embedCount > 0");
	$range = $kapenta->db->loadRange('tags_tag', '*', $conditions, 'embedCount DESC', $num);
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) { return "<i>(no tags)</i>"; }

	$tags = array();
	foreach($range as $item) {
		$tags[$item['namelc']] = array(
			'UID' => $item['UID'],
			'name' => $item['name'],
			'weight' => $item['embedCount'],
			'link' => "javascript:Tags_Set('" . $item['namelc'] . "');"
		);
	}

	asort($tags);

	$html = $theme->makeTagCloud($tags);

	return $html;
}

?>
