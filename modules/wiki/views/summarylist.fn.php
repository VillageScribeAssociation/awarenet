<?

	require_once($kapenta->installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all wiki pages
//--------------------------------------------------------------------------------------------------

function wiki_summarylist($args) {
	global $kapenta;

	$sql = "select * from wiki_article order by refno DESC";
	$result = $kapenta->db->query($sql);
	$html = '';
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$html .= "[[:wiki::summary::raUID=" . $row['UID'] . ":]]";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
