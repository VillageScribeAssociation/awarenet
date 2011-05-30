<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x comments owned by a particular record on a given module, naarow for nav
//--------------------------------------------------------------------------------------------------
//arg: refModule - a kapenta module [string]
//arg: refModel - type of object which may have comments [string]
//arg: refUID - UID of object which owns comments [string]
//opt: num - number of records per page (default 10) [string]
//TODO: pagination here

function comments_listnav($args) {
	global $db, $user, $theme;
	$num = 10;	
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $user->authHas($refModule, $refModel, 'comments-show', $refUID)) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	//----------------------------------------------------------------------------------------------
	//	load a page of comments from the database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($args['refUID']) . "'";

	$range = $db->loadRange('comments_comment', '*', $conditions, 'createdOn DESC', $num);

	//$sql = "select * from Comments_Comment "
	//	 . "where refModule='" . $db->addMarkup($args['refModule']) . "' "
	//	 . "and refUID='" . $db->addMarkup($args['refUID']) . "' "
	//	 . "order by createdOn DESC limit " . $db->addMarkup($num) . "";

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/comments/views/summarynav.block.php');

	if (0 == count($range)) { return "(no comments at present)<br/>"; }


	foreach ($range as $row) {
		$model = new Comments_Comment();
		$model->loadArray($row);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
