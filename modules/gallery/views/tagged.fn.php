<?

//--------------------------------------------------------------------------------------------------
//|	show  a list of all galleries sharing a given tag
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Tags_Tag object [string]
//opt: tagUID - overrides UID if present [string]
//opt: pageNo - TODO, pagination [string]
//opt: num - TODO, number of items per page [string]

function gallery_tagged($args) {
	global $db, $user, $theme;
	$html = '';									//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('tagUID', $args)) { $args['UID'] = $args['tagUID']; }
	if (false == array_key_exists('UID', $args)) { return ''; }
	$tagUID = $args['UID'];

	//----------------------------------------------------------------------------------------------
	//	get page of tagged gallery UIDs from tags module
	//----------------------------------------------------------------------------------------------
	$block = "[[:tags::listuids::refModule=gallery::refModel=gallery_gallery::tagUID=$tagUID:]]";
	$list = $theme->expandBlocks($block, '');
	$uids = explode("\n", trim($list));

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if ((0 == count($uids)) || ('' == $uids[0])) {
		$html .= "<div class='inlinequote'>Nothing with this tag at present.</div>";
		return $html;
	}

	foreach($uids as $galleryUID) { $html .= "[[:gallery::summary::raUID=$galleryUID:]]"; }
	return $html;
}

?>
