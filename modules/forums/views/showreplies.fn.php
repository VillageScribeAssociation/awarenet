<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a thread and paginated replies
//--------------------------------------------------------------------------------------------------
//arg: threadUID - UID of a forum thread [string]
//opt: pageNo - page to display, default is 1 (int) [string]
//opt: num - number of replies per page, default is 5 (int) [string]
//opt: pagination - make / display pagination bar, default is yes (yes|no) [string]

function forums_showreplies($args) {
	global $kapenta;
	global $kapenta;
	global $theme;
	global $kapenta;

	$pageNo = 1; 					//% results page to display [int]
	$num = 5; 						//%	default number of items per page [int]
	$start = 0;						//%	offset in database results [int]
	$pagination = 'yes';			//%	show pagination bar [string]
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------	
	if (false == array_key_exists('threadUID', $args)) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }		
	if (true == array_key_exists('pageNo', $args)) { 
		$pageNo = (int)$args['pageNo'];
		$start = (($pageNo - 1) * $num);
	}

	$model = new Forums_Thread($args['threadUID']);
	if (false == $model->loaded) { return '(thread not found)'; }
	//TODO: check permission here

	//----------------------------------------------------------------------------------------------
	//	count all replies in this thread
	//----------------------------------------------------------------------------------------------
	$conditions = array("thread='" . $model->UID . "'");
	$totalItems = $kapenta->db->countRange('forums_reply', $conditions);
	$totalPages = ceil($totalItems / $num);

	//----------------------------------------------------------------------------------------------
	//	load a page of results from the database
	//----------------------------------------------------------------------------------------------
	$range = $kapenta->db->loadRange('forums_reply', '*', $conditions, 'createdOn ASC', $num, $start);

	//----------------------------------------------------------------------------------------------
	//	show the current page
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/forums/views/reply.block.php');

	foreach ($range as $item) {
		$reply = new Forums_Reply($item['UID']);
		$ext = $reply->extArray();
		$ext['threadTitle'] = $model->title;
		$ext['editLinkJs'] = '';

		//	force images smaller (TODO: something more elegant)
		//$ext['contentHtml'] = str_replace('widthcontent', 'width300', $ext['contentHtml']);
		//$ext['contentHtml'] = str_replace('width570', 'width300', $ext['contentHtml']);

		if (($ext['createdBy'] == $kapenta->user->UID) || ('admin' == $kapenta->user->role)) {
			$UID = $ext['UID'];
			$editBlock = '[[:forums::editreplyif::replyUID=' . $UID . ':]]';
			$editBlock64 = base64_encode($editBlock);
			$onClick = "klive.bindDivToBlock('divEditReply" . $UID . "', '$editBlock64', true);";
			$ext['editLinkJs'] = "<a href='#reply" . $UID . "' onClick=\"$onClick\">[edit]</a>";
		}

		$ext['editNotice'] = '';
		if ($ext['createdOn'] != $ext['editedOn']) {
			$ebBlock = '[[:users::namelink::userUID=' . $ext['editedBy'] . ':]]';
			$ext['editNotice'] = ''
			 . '<small>Edited on ' . $ext['editedOn']
			 . ' by ' . $ebBlock . '</small>';
		}

		$html .= $theme->replaceLabels($ext, $block);
	}

	$link = '%%serverPath%%forums/showthread/' . $model->alias . '/';

	$pagination = "[[:theme::pagination::page=" . (int)$pageNo 
				. "::total=" . $totalPages . "::link=" . $link . ":]]\n";

	if (0 == $totalItems) { $html = "<div class='inlinequote'>No replies.</div>"; }
	if (($start + $num) >= $totalItems) { $html .= "<!-- end of results -->"; }

	if ('yes' == $pagination) { $html = $pagination . $html . $pagination; }

	//	correct image sizes
	$html = $theme->expandBlocks($html);

	$html = str_replace('widthcontent', 'widthindent', $html);
	$html = str_replace('widtheditor', 'widthindent', $html);
	$html = str_replace('width570', 'widthindent', $html);
	$html = str_replace('s_slide', 's_slideindent', $html);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
