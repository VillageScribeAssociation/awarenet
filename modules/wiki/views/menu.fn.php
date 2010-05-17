<?

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu for wiki, no arguments
//--------------------------------------------------------------------------------------------------
//opt: raUID - recordAlias or UID or wiki entry, or 'no' if no edit link [string]
//opt: type - may be 'content' (default) or 'talk', requires raUID [string]

function wiki_menu($args) {
	$type = 'content'; $raUID = 'no';
	if (array_key_exists('raUID', $args) == true) { $raUID = $args['raUID']; }
	if (array_key_exists('type', $args) == true) { $type = $args['type']; }

	$labels = array();
	$labels['newarticle'] = '';
	$labels['article'] = '';
	$labels['talkpage'] = '';
	$labels['editthis'] = '';
	$labels['history'] = '';

	if (authHas('wiki', 'edit', '') == true) {
		$labels['newarticle'] = '[[:theme::submenu::label=New Article::link=/wiki/new/:]]';

		if ($raUID != 'no') {
			// we have an article, make some links for it
			$labels['article'] = "[[:theme::submenu::label=Article::"
								. "link=%%serverPath%%wiki/" . $raUID . ":]]";

			$labels['talkpage'] = "[[:theme::submenu::label=Discussion::"
								. "link=%%serverPath%%wiki/talk/" . $raUID . ":]]";

			$labels['history'] = "[[:theme::submenu::label=History::"
								. "link=%%serverPath%%wiki/history/" . $raUID . ":]]";


			if ($type == 'content') { 
				$labels['editthis'] = "[[:theme::submenu::label=Edit This Page::"
								    . "link=%%serverPath%%wiki/edit/" . $raUID . ":]]"; 
			}

			if ($type == 'talk') { 
				$labels['editthis'] = "[[:theme::submenu::label=Edit This Page::"
									. "link=%%serverPath%%wiki/edittalk/" . $raUID . ":]]"; }
		}
	} 
	
	$html = replaceLabels($labels, loadBlock('modules/wiki/views/menu.block.php'));
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
