<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x comments owned by a particular record on a given module
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to check for permissions on [string]
//arg: refModel - type of object which may on comments [string]
//arg: refUID - object which may own comments [string]
//opt: num - number of records per page [string]

function comments_list($args) {
	global $db, $user, $theme;
	$num = 10;
	$html = '';	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no module)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no model)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no UID)'; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	$auth = false;
	// blanket permissions on comments
	if (true == $user->authHas('comments', 'Comment_Comment', 'list')) { $auth = true; }
	if (true == $user->authHas('comments', 'Comment_Comment', 'show')) { $auth = true; }
	// permission granted by reference module
	if (true == $user->authHas($refModule, $refModel, 'comments-show', $refUID)) { $auth = true; }
	
	if (false == $auth) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------

	$sql = "select * from Comments_Comment "
		 . "where refModule='" . $db->addMarkup($args['refModule']) . "' "
		 . "and refUID='" . $db->addMarkup($args['refUID']) . "' "
		 . "order by createdOn DESC limit " . $db->addMarkup($num) . "";

	$blockFile = 'modules/comments/views/summary.block.php';

	$result = $db->query($sql);
	if ($db->numRows($result) > 0) {
		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
			$model = new Comments_Comment();
			$model->loadArray($row);
			$html .= $theme->replaceLabels($model->extArray(), $theme->loadBlock($blockFile));
		}  
	} else {
		$html .= "(no comments at present)";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
