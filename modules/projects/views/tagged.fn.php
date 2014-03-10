<?

	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');
	//TODO: figure out how to get rid of this dependancy

//--------------------------------------------------------------------------------------------------
//|	show  a list of all projects sharing a given tag
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Tags_Tag object [string]
//opt: tagUID - overrides UID if present [string]
//opt: pageNo - TODO, pagination [string]
//opt: num - TODO, number of items per page [string]

function projects_tagged($args) {
		global $kapenta;
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
	//	get list of projects from database
	//----------------------------------------------------------------------------------------------
	$sql = "SELECT projects_project.UID as projectUID from projects_project, tags_index "
		 . "WHERE projects_project.UID=tags_index.refUID "
		 . "AND tags_index.tagUID='" . $kapenta->db->addMarkup($tag->UID) . "' "
		 . "ORDER by projects_project.title";

	$result = $kapenta->db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if (0 == $kapenta->db->numRows($result)) { return "(nothing with this tag at present)"; }

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$html .= "[[:projects::summary::raUID=" . $row['projectUID'] . ":]]";
	}

	return $html;
}


?>
