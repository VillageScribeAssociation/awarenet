<?

//-------------------------------------------------------------------------------------------------
//|	summarize a module for the nav
//-------------------------------------------------------------------------------------------------
//arg: module - name of a module [string]

function docgen_modulesummarynav($args) {
	if (array_key_exists('module', $args) == false) { return ''; }

	$docUrl = '%%serverPath%%docgen/describemodule/' . $args['module'];

	$imgUrl = "%%serverPath%%themes/%%defaultTheme%%/images/icons/folder.png";
	$img = "<img src='$imgUrl' border='0' />";

	$desc = "<b><a href='$docUrl'>" . $args['module'] . "</a></b><br/>";
	$desc .= "<small>";

	$actions = listActions($args['module']);
	$desc .= count($actions) . " actions, ";

	$pages = listPages($args['module']);
	$desc .= count($actions) . " pages, ";

	$models = listModels($args['module']);
	$desc .= count($models) . " models, ";

	$views = listViews($args['module']);
	$desc .= count($views) . " views, ";

	$blocks = listBlocks($args['module']);
	$desc .= count($blocks) . " blocks, ";

	$incs = listIncs($args['module']);
	$desc .= count($incs) . " includes.";

	$desc .= "</small><br/>\n";

	$html = "<table noborder>
	<tr>
		<td valign='top'><a href='$docUrl'>$img</a></td>
		<td valign='top'>$desc</td>
	</tr>
	</table>\n";

	return $html;
}


?>
