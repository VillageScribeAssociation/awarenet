<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x comments owned by a particular record on a given module
//--------------------------------------------------------------------------------------------------
//arg: refUID - record which owns the comments [string]
//arg: refModel - type of object [string]
//arg: refModule - module which owns the record [string]
//opt: num - number of records per page (default is 4) [string]

function comments_listnavjs($args) {
	global $db, $theme, $user, $utils;
	$num = 4;		//%	default number of comments per page [int]
	$html = '';		//%	return value [string]
	$js = '';		//%	javascript [string]

	$blockTemplate = $theme->loadBlock('modules/comments/views/summarynav.block.php');
	$scriptUrl = '%%serverPath%%modules/comments/js/comments.js';

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	//if ($user->authHas('comments', 'Comment_Comment', 'list', 'TODO:UIDHERE') == false) { return false; }
	//if ($user->authHas('comments', 'Comment_Comment', 'show', 'TODO:UIDHERE') == false) { return false; }
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }

	//----------------------------------------------------------------------------------------------
	//	load comments from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($args['refUID']) . "'";
	$range = $db->loadRange('comments_comment', '*', $conditions, 'createdOn DESC');

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$js .= "<script src='" . $scriptUrl . "' language='javascript'></script>";
	$js .= "<script language='javascript'>\n";
	$js .= "var commentsPageSize = " . $num .  ";\n";
	$js .= "var commentsPage = 0;\n";
	$js .= "var aryComments = new Array();\n";
	
	if (count($range) > 0) {
		foreach ($range as $row) {
			$model = new Comments_Comment();
			$model->loadArray($row);
			$ext = $model->extArray();

			$blockHtml = $theme->replaceLabels($ext, $blockTemplate);
			$blockHtml = $theme->expandBlocks($blockHtml, '');
	
			$js .= $utils->base64EncodeJs('b64c' . $ext['UID'], $blockHtml ,false);
			$js .= "aryComments.push(new Array(\"". $ext['UID'] ."\", b64c". $ext['UID'] ."));\n\n";

		}  
	} else {
		// nothing to do?
	}

	$js .= "";
	$js .= "</script>\n";

	$html = $js ."<div id='divCommentsJs'><span class='ajaxmsg'>Loading comments...</span></div>\n";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
