<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x comments owned by a particular record on a given module
//--------------------------------------------------------------------------------------------------
//arg: refUID - record which owns the comments [string]
//arg: refModel - type of object [string]
//arg: refModule - module which owns the record [string]
//opt: pageNo - result page to display [string]
//opt: num - number of records per page (default is 4) [string]

function comments_listnavjs($args) {
		global $kapenta;
		global $theme;
		global $user;
		global $utils;


	$pageNo = 1;	//%	page to show [int]
	$num = 4;		//%	default number of comments per page [int]
	$start = 0;	

	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }

	//TODO: permissions check here

	if (true == array_key_exists('refModule', $args)) { return '(refModule not given)'; }
	if (true == array_key_exists('refModel', $args)) { return '(refModel not given)'; }
	if (true == array_key_exists('refUID', $args)) { return '(refUID not given)'; }
	if (true == array_key_exists('num', $args)) { $num = $args['num']; }

	if (true == array_key_exists('pageNo', $args)) { 
		$pageNo = (int)$args['pageNo'];
	}

	//----------------------------------------------------------------------------------------------
	//	load comments from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($args['refUID']) . "'";
	$range = $kapenta->db->loadRange('comments_comment', '*', $conditions, 'createdOn DESC');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/comments/views/summarynav.block.php');

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
