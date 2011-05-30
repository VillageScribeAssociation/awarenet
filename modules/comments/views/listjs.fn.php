<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x comments owned by a particular record on a given module
//--------------------------------------------------------------------------------------------------
//arg: refUID - record which owns the comments [string]
//arg: refModule - module which owns the record [string]
//opt: num - number of records per page (default 10) [string]

function comments_listjs($args) {
	global $theme, $kapenta, $db, $user, $utils;

	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if ($user->authHas('comments', 'Comment_Comment', 'list', 'TODO:UIDHERE') == false) { return false; }
	if ($user->authHas('comments', 'Comment_Comment', 'show', 'TODO:UIDHERE') == false) { return false; }
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }

	$num = 10;
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }
	$html = ''; $js = '';

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------

	$sql = "select * from comments_comment "
		 . "where refModule='" . $db->addMarkup($args['refModule']) . "' "
		 . "and refUID='" . $db->addMarkup($args['refUID']) . "' "
		 . "order by createdOn DESC";

	$blockTemplate = $theme->loadBlock('modules/comments/views/summary.block.php');
	$scriptUrl = $kapenta->serverPath . 'modules/comments/js/comments.js';

		
	$js .= "<script src='" . $scriptUrl . "' language='javascript'></script>";
	$js .= "<script language='javascript'>\n";
	$js .= "var commentsPageSize = " . $num .  ";\n";
	$js .= "var commentsPage = 0;\n";
	$js .= "var aryComments = new Array();\n";
	
	$result = $db->query($sql);
	if ($db->numRows($result) > 0) {
		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
			$model = new Comments_Comment();
			$model->loadArray($row);
			$ext = $model->extArray();

			$blockHtml = $theme->replaceLabels($ext, $blockTemplate);
			$blockHtml = $theme->expandBlocks($blockHtml, '');
	
			$js .= $utils->base64EncodeJs('b64c' . $ext['UID'], $blockHtml ,false);
			$js .= "aryComments.push(new Array(\"" . $ext['UID'] . "\", b64c" . $ext['UID'] . "));\n\n";

		}  
	} else {
		// nothing to do?
	}

	$js .= "";
	$js .= "</script>\n";

	$html = $js . "<div id='divCommentsJs'><span class='ajaxmsg'>Loading comments...</span></div>\n";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
