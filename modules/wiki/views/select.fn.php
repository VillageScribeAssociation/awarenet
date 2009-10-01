<?

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	make a select element listing all wiki articles (?)
//--------------------------------------------------------------------------------------------------
// * $args['varname'] = name of variable
// * $args['default'] = current value

function wiki_select($args) {
	$varname = 'person';
	$default = '';
	if (array_key_exists('varname', $args)) { $varname = $args['varname']; }
	if (array_key_exists('default', $args)) { $default = $args['default']; }
	$html = '';
	
	$sql = "select * from wiki order by name";
	$result = dbQuery($sql);
	$html .= "<select name='" . $varname . "'>\n";
	while ($row = dbFetchAssoc($result)) {
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