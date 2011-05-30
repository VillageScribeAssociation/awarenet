<?

	require_once($kapenta->installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	make a select element listing all wiki articles (?)
//--------------------------------------------------------------------------------------------------
//opt: varname - name of HTML form variable [string]
//opt: default - current value [string]

function wiki_select($args) {
	global $db;

	$varname = 'person';
	$default = '';
	if (array_key_exists('varname', $args)) { $varname = $args['varname']; }
	if (array_key_exists('default', $args)) { $default = $args['default']; }
	$html = '';
	
	$sql = "select * from wiki_article order by name";
	$result = $db->query($sql);

	$html .= "<select name='" . $varname . "'>\n";
	while ($row = $db->fetchAssoc($result)) {
		if ($row['UID'] == $default) {
			$html .= "<option value='" . $row['UID'] . "' CHECKED='CHECKED'>" 
			      . $row['name'] . "</option>\n";
		} else {
			$html .= "<option value='" . $row['UID'] . "'>" . $row['name'] . "</option>\n";
		}
	}
	$html .= "</select>\n";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
