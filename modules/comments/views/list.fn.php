<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x comments owned by a particular record on a given module
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to check for permissions on [string]
//arg: refModel - type of object which may on comments [string]
//arg: refUID - object which may own comments [string]
//opt: pageNo - result page to display, default is 1 (int) [string]
//opt: num - number of records per page [string]

function comments_list($args) {
	global $kapenta;
	global $kapenta;
	global $user;
	global $theme;
	global $kapenta;

	$pageNo = 1;				//%	page number to show [int]
	$num = 10;					//%	number of items per page [int]
	$start = 0;					//%	offset in database results [int]
	$html = '';					//%	return value [string]	

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no module)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no model)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no UID)'; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	if (true == array_key_exists('pageNo', $args)) {
		$pageNo = (int)$args['pageNo'];
		$start = (($pageNo - 1) * $num);
	}

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	$auth = false;
	// blanket permissions on comments
	if (true == $user->authHas('comments', 'comment_comment', 'show')) { $auth = true; }
	// permission granted by reference module
	if (true == $user->authHas($refModule, $refModel, 'comments-show', $refUID)) { $auth = true; }
	
	if (false == $auth) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	count comments attached to this item
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($args['refUID']) . "'";
	$conditions[] = "parent=''";

	$totalItems = $kapenta->db->countRange('comments_comment', $conditions);

	//----------------------------------------------------------------------------------------------
	//	load a page of comments from the database
	//----------------------------------------------------------------------------------------------

	$range = $kapenta->db->loadRange('comments_comment', '*', $conditions, 'createdOn DESC', $num, $start);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$blockFile = 'modules/comments/views/summary.block.php';

	if (count($range) > 0) {
		foreach ($range as $item) {
			//$model = new Comments_Comment();
			//$model->loadArray($row);
			//$html .= $theme->replaceLabels($model->extArray(), $theme->loadBlock($blockFile));

			$html .= "[[:comments::show::UID=" . $item['UID'] . ":]]";

		}  
	} else {
		$html .= ''
		 . "<div class='outline' style='color: #bbbbbb;'>"
		 . "<small>No comments yet.  Be the first :-)</small>"
		 . "</div>";
	}

	if (($start + $num) >= $totalItems) { $html .= "<!-- end of results -->"; }

	//----------------------------------------------------------------------------------------------
	//	set triggers
	//----------------------------------------------------------------------------------------------
	//$UID = $kapenta->createUID();
	//$rawBlock64 = base64_encode($args['rawblock']);
	//$html = "<div id='blockCommentsL" . $UID . "'>\n"
	//		. $html
	//		. "<!-- REGISTERBLOCK:blockCommentsL" . $UID . ":" . $rawBlock64 . " -->"
	//		. "</div>";
	//
	//	$channel = 'comment-' . $refModel . '-' . $refUID;
	//	$page->setTrigger('comments', $channel, $args['rawblock']);


	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
