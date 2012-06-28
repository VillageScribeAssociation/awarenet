<?

//--------------------------------------------------------------------------------------------------
//|	list UIDs of tagged objects (plain text, one per line)
//--------------------------------------------------------------------------------------------------
//arg: tagUID - UID of a Tags_Tag object [string]
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of tagged objects [string]
//opt: num - maximum number of results to return, default is 10 (int) [string]
//opt: pageNo - results page, starting from 1, default is 1 (int) [string]

function tags_listuids($args) {
	global $db;

	$num = 10;				//%	max results to return [int]
	$pageNo = 1;			//%	page number [int]
	$txt = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('tagUID', $args)) { return $txt; }
	if (false == array_key_exists('refModule', $args)) { return $txt; }
	if (false == array_key_exists('refModel', $args)) { return $txt; }

	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pageNo', $args)) { $pageNo = (int)$args['pageNo']; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "tagUID='" . $db->addMarkup($args['tagUID']) . "'";
	$conditions[] = "refModule='" . $db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refModel='" . $db->addMarkup($args['refModel']) . "'";

	$start = ($pageNo - 1) * $num;

	$range = $db->loadRange('tags_index', '*', $conditions, 'createdOn', $num, $start);
	if (false === $range) { return $txt; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$uids = array();
	foreach($range as $item) { $uids[] = $item['refUID']; }
	$txt = implode("\n", $uids);

	return $txt;
}

?>
