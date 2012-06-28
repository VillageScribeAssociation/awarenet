<?

//--------------------------------------------------------------------------------------------------
//|	show tag could for entire system, a module, model or single object
//--------------------------------------------------------------------------------------------------
//opt: refModule - name of a kapenta module [string]
//opt: refModel - if present owner must be this type of object [string]
//opt: refUID -UID of an object which may have tags [string]
//opt: num - maximum number of tags to display, default is 20 (int)[string]
//opt: channel - RESERVED [string]

function tags_cloud($args) {
	global $db;
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
	//TODO: figure out how to eliminate this join	

	$moduleCheck = '';
	$modelCheck = '';
	$UIDCheck = '';

	if (true == array_key_exists('refModule', $args)) {
		if (true == $kapenta->moduleExists($args['refModule'])) {
			$moduleCheck = "AND tags_index.refModule='" . $db->addMarkup($args['refModule']) . "'";
		}
	}

	if (true == array_key_exists('refModel', $args)) 
		{ $modelCheck = "AND tags_index.refModel='" . $db->addMarkup($args['refModel']) . "'"; }

	if (true == array_key_exists('refUID', $args)) 
		{ $UIDCheck = "AND tags_index.refUID='" . $db->addMarkup($args['refUID']) . "'"; }

	$sql = "SELECT tags_tag.* FROM tags_index, tags_tag "
		 . "WHERE tags_index.tagUID=tags_tag.UID "
		 . "$moduleCheck $modelCheck $UIDCheck "
		 . "ORDER BY tags_tag.objectCount "
		 . "LIMIT $num";

	//echo $sql;

	$result = $db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	proprocess and sort tags by name
	//----------------------------------------------------------------------------------------------
	if (0 == $db->numRows($result)) { return "<i>(no tags)</i>"; }

	$tags = array();
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$tags[$row['namelc']] = array(
			'UID' => $row['UID'],
			'name' => $row['name'],
			'weight' => $row['objectCount']
		);
	}

	asort($tags);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$links = array();
	$url = "tags/show/";

	//TODO: fix the following, this is clunky:
	if (true == array_key_exists('url', $args)) { $url = $args['url']; }

	foreach($tags as $tag) {
		$links[]= "<a href='%%serverPath%%" . $url . $tag['name'] . "'>" . $tag['name'] . "</a>";
	}

	$html = implode(", ", $links);
	return $html;
}

?>
