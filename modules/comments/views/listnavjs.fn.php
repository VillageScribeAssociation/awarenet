<?

	require_once($installPath . 'modules/comments/models/comments.mod.php');

//--------------------------------------------------------------------------------------------------
//	list all most recent x comments owned by a particular record on a given module
//--------------------------------------------------------------------------------------------------
// * $args['refUID'] = record which owns the comments
// * $args['refModule'] = module which owns the record
// * $args['num'] = number of records per page

function comments_listnavjs($args) {
	global $serverPath;
	if (authHas('comments', 'list', '') == false) { return false; }
	if (authHas('comments', 'view', '') == false) { return false; }
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }

	$num = 4;
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }
	$html = ''; $js = '';

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------

	$sql = "select * from comments "
		 . "where refModule='" . sqlMarkup($args['refModule']) . "' "
		 . "and refUID='" . sqlMarkup($args['refUID']) . "' "
		 . "order by createdOn DESC";

	$blockTemplate = loadBlock('modules/comments/views/summarynav.block.php');
	$scriptUrl = $serverPath . 'modules/comments/js/comments.js';

		
	$js .= "<script src='" . $scriptUrl . "' language='javascript'></script>";
	$js .= "<script language='javascript'>\n";
	$js .= "var commentsPageSize = " . $num .  ";\n";
	$js .= "var commentsPage = 0;\n";
	$js .= "var aryComments = new Array();\n";
	
	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$model = new comment();
			$model->loadArray($row);
			$ext = $model->extArray();

			$blockHtml = replaceLabels($ext, $blockTemplate);
			$blockHtml = expandBlocks($blockHtml, '');
	
			$js .= base64EncodeJs('b64c' . $ext['UID'], $blockHtml ,false);
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
