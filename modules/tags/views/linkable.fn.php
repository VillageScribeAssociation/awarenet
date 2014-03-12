<?php

//--------------------------------------------------------------------------------------------------
//|	paginated tag search results
//--------------------------------------------------------------------------------------------------
//:	Note that this is the tag search at the top of the page - it is for searching for linkable 
//:	search results, rather than inserable ones.
//arg: q64 - base64 encoded search string
//opt: display - comma separated list of modules to display from [string]
//opt: pageNo - page of results, default is 1 (int) [string]
//opt: pageSize - number of items per page (default is 10) [string]

//note: this is not going to scale well, a more efficient system will have to be developed

function tags_linkable($args) {
	global $kapenta;
	global $kapenta;
	global $theme;

	//%	types which maybe attached [string]
	$display = 'projects_project,moblog_post,images_image,videos_video,files_file,gallery_gallery';
	$display = 'projects_project,moblog_post,images_image,users_user,gallery_gallery';

	$pageNo = 1;										//%	result page to display [string]
	$pageSize = 10;										//%	number of items per page [string]
	$offset = 0;										//%	starting position in set [string]
	$html = '';											//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { return ''; }
	if (false == array_key_exists('q64', $args)) { return '(query not given)'; }
	if (true == array_key_exists('display', $args)) { $display = $args['display']; }
	if (true == array_key_exists('pageNo', $args)) { $pageNo = (int)$args['pageNo']; }
	if (true == array_key_exists('pageSize', $args)) { $pageSize = (int)$args['pageSize']; }

	$displaySet = explode(',', $display);

	if ($pageNo < 1) { $pageNo = 1; }
	$offset = ($pageNo - 1) * $pageSize;

	//----------------------------------------------------------------------------------------------
	//	search for tags
	//----------------------------------------------------------------------------------------------
	$q = base64_decode($args['q64']);
	$q = str_replace(' ', '-', $q);
	$q = strtolower($q);

	$conditions = array();
	$conditions[] = "INSTR(namelc, '" . $kapenta->db->addMarkup($q) . "') > 0";
	$conditions[] = "objectCount <> '0'";
	$range = $kapenta->db->loadRange('tags_tag', '*', $conditions, 'namelc', 50);

	if (0 == count($range)) { return ''; }		// nothign to search

	//----------------------------------------------------------------------------------------------
	//	count matches and find starting offset
	//----------------------------------------------------------------------------------------------
	$cursor = 0;

	foreach($range as $item) {
		$idxcond = array("tagUID='" . $kapenta->db->addMarkup($item['UID']) . "'");
		$idxrange = $kapenta->db->loadRange('tags_index', '*', $idxcond);

		foreach($idxrange as $match) {
			if (true == in_array($match['refModel'], $displaySet)) {
				if (($cursor >= $offset) && ($cursor < ($offset + $pageSize))) {

					$html .= ''
					 . "[[:" . $match['refModule'] . '::summarylink'
					 . '::model=' . $match['refModel']
					 . '::raUID=' . $match['refUID']
					 . ':]]' . "\n"
					 . '[[:tags::listflat'
					 . '::refModule=' . $match['refModule']
					 . '::refModel=' . $match['refModel']
					 . '::refUID=' . $match['refUID']
					 . ':]]'
					 . '<hr/>';

				}
				$cursor += 1;
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$html = "<div style='background-color: #aaaaaa;'>\n" . $html . "</div>\n";

	return $html;
}

?>
