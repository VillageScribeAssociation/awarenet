<?

//--------------------------------------------------------------------------------------------------
//|	counts the number of comments owned by some other object
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which owns comments [string]
//arg: refUID - UID of object which owns comments [string]

function comments_count($args) {
	global $db;
	$html = '';						// return value (integer) [string]	

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '0'; }
	if (false == array_key_exists('refModel', $args)) { return '0'; }
	if (false == array_key_exists('refUID', $args)) { return '0'; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refModel='" . $db->addMarkup($args['refModel']) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($args['refUID']) . "'";

	$total = $db->countRange('comments_comment', $conditions);
	$html = (int)$total . '';

	return $html;
}

?>
