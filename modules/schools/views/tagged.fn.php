<?

	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');
	//TODO: figure out how to get rid of this dependancy

//--------------------------------------------------------------------------------------------------
//|	show  a list of all schools sharing a given tag
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Tags_Tag object [string]
//opt: tagUID - overrides UID if present [string]
//opt: pageNo - TODO, pagination [string]
//opt: num - TODO, number of items per page [string]

function schools_tagged($args) {
		global $db;
		global $user;

	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('tagUID', $args)) { $args['UID'] = $args['tagUID']; }
	if (false == array_key_exists('UID', $args)) { return ''; }

	$tag = new Tags_Tag($args['UID']);
	if (false == $tag->loaded) { return '(unknown tag)'; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	get list of schools from database
	//----------------------------------------------------------------------------------------------
	$sql = "SELECT schools_school.UID as schoolUID from schools_school, tags_index "
		 . "WHERE schools_school.UID=tags_index.refUID "
		 . "AND tags_index.tagUID='" . $db->addMarkup($tag->UID) . "' "
		 . "ORDER by schools_school.name";

	$result = $db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if (0 == $db->numRows($result)) { return "(nothign with this tag at present)"; }

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$html .= "[[:schools::summary::raUID=" . $row['schoolUID'] . ":]]";
	}

	return $html;
}


?>
