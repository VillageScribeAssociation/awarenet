<?

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	list wiki articles
//--------------------------------------------------------------------------------------------------
// * $args['page'] = UID of wiki entry
// * $args['num'] = number of entries to show per page

function wiki_listall($args) {
	$pageno = 1; $num = 30; $total = 0; $numPages = 0; $start = 0;
	if (array_key_exists('pageno', $args) == true) { $pageno = $args['pageno']; }
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	count all wiki articles
	//----------------------------------------------------------------------------------------------
	$sql = "select count(UID) as numarticles from wiki";
	$result = dbQuery($sql);
	$row = dbFetchAssoc($result);
	$total = $row['numarticles'];
	$numPages = ceil($total / $num);

	//----------------------------------------------------------------------------------------------
	//	load records
	//----------------------------------------------------------------------------------------------
	$start = (($pageno - 1) * $num);
	$sql = "select * from wiki order by title ASC limit $start, $num";
	$result = dbQuery($sql);

	//----------------------------------------------------------------------------------------------
	//	make table
	//----------------------------------------------------------------------------------------------
	$table = "<table class='scaffold'>";
	$table .= "\t<tr>\n"
			. "\t\t<td class='title'>Article</td>\n"
			. "\t\t<td class='title'>[x]</td>\n"
			. "\t\t<td class='title'>[x]</td>\n"
			. "\t\t<td class='title'>Content</td>\n" 
			. "\t\t<td class='title'>Talk</td>" 
			. "\t\t<td class='title'>Hitcount</td>\n" 
			. "\t</tr>";

	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$ra = $row['recordAlias'];

		$alink = "<a href='%%serverPath%%wiki/". $ra ."'>". $row['title'] ."</a>";
		$talk = "<a href='%%serverPath%%wiki/talk/". $ra ."'>[discuss]</a>";
		$hist = "<a href='%%serverPath%%wiki/history/". $ra ."'>[history]</a>";

		$table .= "\t<tr>"
				. "\t\t<td>" . $alink . "</td>\n"
				. "\t\t<td><small>" . $talk . "</small></td>\n"
				. "\t\t<td><small>" . $hist . "</small></td>\n"
				. "\t\t<td><small>" . strlen($row['content']) . " bytes</small></td>\n"
				. "\t\t<td><small>" . strlen($row['talk']) . " bytes</small></td>\n"
				. "\t\t<td>" . $row['hitcount'] . "</td>"
				. "\t</tr>";
	}
	$table .= "</table>\n";

	//----------------------------------------------------------------------------------------------
	//	add pagination
	//----------------------------------------------------------------------------------------------

	$plink = "%%serverPath%%wiki/list/";
	$html = "[[:theme::pagination::page=". $page ."::total=". $numPages ."::link=". $plink .":]]\n";
	$html = $html . $table . $html;

	return $html;
}


?>