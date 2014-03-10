<?

	require_once($kapenta->installPath . 'modules/code/models/bugs.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/codeproject.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/coderevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	show a templated page
//--------------------------------------------------------------------------------------------------
// * $args['docUID'] = recordAlias or UID or code entry, overrides raUID
// * $args['raUID'] = recordAlias or UID or code entry

function code_showpage($args) {
	global $kapenta;

	if (array_key_exists('docUID', $args)) { $args['raUID'] = $args['docUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	global $kapenta;
	$html = '';

	$c = new Code($args['raUID']);
	$ext = $c->extArray();

	$html .= "<h3>Page Components:</h3>";

	$tags = explode('|', 'template|content|title|script|nav1|nav2|banner|head|menu1|menu2|section');
	$raw = $kapenta->fileRemovePhpWrapper($ext['content']);
	$parts = array();
	$html .= "<table noborder class='wireframe'>\n";
	foreach($tags as $tag) {
		$parts[$tag] = sqlRemoveMarkup(xmlGetTag($raw, $tag));
		if (trim($parts[$tag]['value']) != '') {

			$clean = $parts[$tag]['value'];
			$clean = str_replace("<", "&lt;", $clean);
			$clean = str_replace(">", "&gt;", $clean);
			$clean = str_replace("%%", "%<!-- x -->%", $clean);
			$clean = str_replace("[[:", "[[%%delme%%:", $clean);
			$clean = str_replace("\n", "<br/>\n", $clean);

			$html .= "\t<tr>\n";
			$html .= "\t\t<td valign='top' class='title'>$tag</td>\n";
			$html .= "\t\t<td valign='top' class='wireframe'>" . $clean . "</td>\n";
			$html .= "\t</tr>\n";
		}
	}
	$html .= "</table><br/>\n";

	require_once($kapenta->installPath . 'modules/code/inc/format.inc.php');
	$htmlContent = formatDisplayHtml($ext['safeContent']);

	$html .= "<h3>Raw File In .page.php Format:</h3>";
	$html .= "<div class='inlinequote'><small>" . $htmlContent . "</small></div><br/>\n";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
