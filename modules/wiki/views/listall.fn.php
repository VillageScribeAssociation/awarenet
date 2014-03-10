<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
//	require_once($kapenta->installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list wiki articles
//--------------------------------------------------------------------------------------------------
//opt: page - page number (default is 1) [string]
//opt: num - number of entries to show per page (default is 30) [string]
//opt: namespace - namespace to list from, default is 'article' [string]
//TODO: this could stand some TLC

function wiki_listall($args) {
		global $kapenta;
		global $user;
		global $theme;


	$pageno = 1;
	$num = 30;
	$total = 0;
	$numPages = 0;
	$start = 0;

	$namespace = 'article';
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('pageno', $args)) { $pageno = (int)$args['pageno']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	//TODO: permissions check, other namespaces, argument checks

	//----------------------------------------------------------------------------------------------
	//	count all wiki articles
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "namespace='" . $namespace . "'";

	$totalItems = $kapenta->db->countRange('wiki_article', $conditions);
	$totalPages = ceil($totalItems / $num);

	//----------------------------------------------------------------------------------------------
	//	load a page worth of articles
	//----------------------------------------------------------------------------------------------
	$start = (($pageno - 1) * $num);
	$range = $kapenta->db->loadRange('wiki_article', '*', $conditions, 'title', $num, $start);

	//----------------------------------------------------------------------------------------------
	//	make table
	//----------------------------------------------------------------------------------------------

	$table = array();
	$table[] = array('Article', '[x]', '[x]' );	// 'Content', 'Talk', 'Hitcount'

	foreach ($range as $row) {
		$ra = $row['alias'];
		$alink = "<a href='%%serverPath%%wiki/". $ra ."'>". $row['title'] ."</a>";
		$talk = "<a href='%%serverPath%%wiki/talk/". $ra ."'>[discuss]</a>";
		$hist = "<a href='%%serverPath%%wiki/history/". $ra ."'>[history]</a>";

		$table[] = array($alink, $talk, $hist);
	}

	//TODO: stabilize talk
	//		. "\t\t<td><small>" . strlen($row['content']) . " bytes</small></td>\n"
	//		. "\t\t<td><small>" . strlen($row['talk']) . " bytes</small></td>\n"
	//		. "\t\t<td>" . $row['viewcount'] . "</td>"


	//----------------------------------------------------------------------------------------------
	//	add pagination
	//----------------------------------------------------------------------------------------------

	$plink = "%%serverPath%%wiki/list/";
	$pagination = "[[:theme::pagination::page=$pageno::total=$totalPages::link=$plink:]]\n";
	$html = $pagination . $theme->arrayToHtmlTable($table, true, true) . $pagination;

	return $html;
}
?>
