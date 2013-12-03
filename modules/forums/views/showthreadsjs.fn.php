<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list threads in a forum
//--------------------------------------------------------------------------------------------------
//arg: forumUID - UID of a forum [string]
//opt: num - number of threads per page (default is 20) [string]

function forums_showthreadsjs($args) {
	global $db, $page, $theme;
	$pageno = 1; 		//%	current page number [int]
	$num = 20; 			//%	number of threads per page [int]
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('forumUID', $args)) { return '(forum not specified)'; }
	//if (true == array_key_exists('pageno', $args)) { $pageno = (int)$args['pageno']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }		

	$model = new Forums_Board($args['forumUID']);
	if (false == $model->loaded) { return '(forum not found)'; }

	//----------------------------------------------------------------------------------------------
	//	count all threads on this forum
	//----------------------------------------------------------------------------------------------
	$conditions = array("board='" . $db->addMarkup($model->UID) . "'");
	$totalItems = $db->countRange('forums_thread', $conditions);
	$totalPages = ceil($totalItems / $num);

	//----------------------------------------------------------------------------------------------
	//	set up live registration and page switcher
	//----------------------------------------------------------------------------------------------
	$block = "[[:forums::showthreads::forumUID=" . $model->UID . "::num=$num::pageno=%%page%%::pagination=no:]]";
	$blockPg1 = str_replace('%%page%%', '1', $block);

	$divId = "divForum" . $model->UID;
	$pageLinks = '';

	for ($i = 1; $i <= $totalPages; $i++) {
		$blockPgX = str_replace('%%page%%', $i, $block);
		$blockPgX = str_replace('[[:', '[[%%delme%%:', $blockPgX);
		$onClick = "klive.setDivContent('$divId', '$blockPgX');";
		$pageLinks .= "<a href='#' onClick=\"$onClick\" class='black'>[$i]</a> \n";
	}

	$pagination = ''
		. "<table noborder width='100%'>"
		. "<tr><td bgcolor='#dddddd'>\n" . $pageLinks . "<br/></td></tr>"
		. "</table>\n";	

	$listDiv = ''
		. "<div id='$divId'>"
		. $blockPg1
		. "</div>"
		. "<!-- REGISTERBLOCK:$divId:" . base64_encode($blockPg1) . " -->";

	$html = $pagination . $listDiv . $pagination;

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
