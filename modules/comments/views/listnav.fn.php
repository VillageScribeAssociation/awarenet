<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x comments owned by a particular record on a given module, naarow for nav
//--------------------------------------------------------------------------------------------------
//arg: refModule - a kapenta module [string]
//arg: refModel - type of object which may have comments [string]
//arg: refUID - UID of object which owns comments [string]
//opt: pageNo - current result page to display [string]
//opt: num - number of records per page (default 10) [string]

function comments_listnav($args) {
	global $db;
	global $user;
	global $theme;
	
	$pageNo = 1;		//%	result page to display [int]
	$num = 4;			//%	number of comments per page [int]
	$start = 0;			//%	offset in database results [int]
	$html = '';			//%	return value [string]

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
	if (true == array_key_exists('pageNo', $args)) { 
		$pageNo = (int)$args['pageNo'];
		$start = (($pageNo - 1) * $num);
	}

	//----------------------------------------------------------------------------------------------
	//	count comments on this item
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refModel='" . $db->addMarkup($args['refModel']) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($args['refUID']) . "'";

	$total = $db->countRange('comments_comment', $conditions);
	if ($start >= $total) {
		return '<!-- end of results -->';
	}

	//----------------------------------------------------------------------------------------------
	//	load a page of comments from the database
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('comments_comment', '*', $conditions, 'createdOn DESC', $num, $start);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/comments/views/summarynav.block.php');

	if (0 == count($range)) { 
		return "<div class='inlinequote'>No comments.</div>";
	}

	foreach ($range as $row) {
		$model = new Comments_Comment();
		$model->loadArray($row);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
