<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x comments owned by a particular record on a given module
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of obejct which may have comments [string]
//arg: refUID - UID of object which may own comments [string]
//opt: num - number of records per page (default 10) [string]
//TODO: discover if this is still used by anything and delete if not

function comments_listjs($args) {
		global $theme;
		global $kapenta;
		global $kapenta;
		global $user;
		global $utils;


	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (false == $user->authHas('comments', 'Comment_Comment', 'show')) { return false; }
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }

	$num = 10;
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }
	$html = ''; $js = '';

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	//TODO: use db->loadRange() for this

	$sql = "select * from comments_comment "
		 . "where refModule='" . $kapenta->db->addMarkup($args['refModule']) . "' "
		 . "and refUID='" . $kapenta->db->addMarkup($args['refUID']) . "' "
		 . "order by createdOn DESC";

	$blockTemplate = $theme->loadBlock('modules/comments/views/summary.block.php');
	$scriptUrl = $kapenta->serverPath . 'modules/comments/js/comments.js';

		
	$js .= "<script src='" . $scriptUrl . "' language='javascript'></script>";
	$js .= "<script language='javascript'>\n";
	$js .= "var commentsPageSize = " . $num .  ";\n";
	$js .= "var commentsPage = 0;\n";
	$js .= "var aryComments = new Array();\n";
	
	$result = $kapenta->db->query($sql);
	if ($kapenta->db->numRows($result) > 0) {
		while ($row = $kapenta->db->fetchAssoc($result)) {
			$row = $kapenta->db->rmArray($row);
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
