<?

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all wiki pages
//--------------------------------------------------------------------------------------------------

function wiki_summarylist($args) {
	$sql = "select * from wiki order by refno DESC";
	$result = dbQuery($sql);
	$html = '';
	while ($row = dbFetchAssoc($result)) {
		$html .= "[[:wiki::summary::raUID=" . $row['UID'] . ":]]";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
