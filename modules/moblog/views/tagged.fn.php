<?

	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');
	//TODO: figure out how to get rid of this dependancy

//--------------------------------------------------------------------------------------------------
//|	show  a list of all moblog posts sharing a given tag
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Tags_Tag object [string]
//opt: tagUID - overrides UID if present [string]
//opt: pageNo - TODO, pagination [string]
//opt: num - TODO, number of items per page [string]

function moblog_tagged($args) {
		global $kapenta;
		global $kapenta;

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
	//	get list of blog posts from database
	//----------------------------------------------------------------------------------------------
	$sql = "SELECT moblog_post.UID as postUID from moblog_post, tags_index "
		 . "WHERE moblog_post.UID=tags_index.refUID "
		 . "AND tags_index.tagUID='" . $kapenta->db->addMarkup($tag->UID) . "' "
		 . "ORDER by moblog_post.title";

	$result = $kapenta->db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if (0 == $kapenta->db->numRows($result)) { return "(nothing with this tag at present)"; }

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$html .= "[[:moblog::summary::raUID=" . $row['postUID'] . ":]]";
	}

	return $html;
}


?>
