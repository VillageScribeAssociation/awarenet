<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/inc/wikicode.class.php');

//--------------------------------------------------------------------------------------------------
//|	menu for wiki, no arguments
//--------------------------------------------------------------------------------------------------
//opt: raUID - alias or UID of a Wiki_Aricle object, or 'no' if no edit link [string]
//opt: type - may be 'content' (default) or 'talk', requires raUID [string]

function wiki_menu($args) {
	global $theme, $user;	

	$type = 'content'; $raUID = 'no';
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('type', $args)) { $type = $args['type']; }

	$labels = array();
	$labels['newarticle'] = '';
	$labels['article'] = '';
	$labels['talkpage'] = '';
	$labels['editthis'] = '';
	$labels['history'] = '';

	$model = new Wiki_Article($args['raUID']);
	if (false == $model->loaded) { 
		$menu = "[[:theme::submenu::label=Front Page::link=/wiki/:]]\n"
			  . "[[:theme::submenu::label=All::link=/wiki/list/:]]\n"
			  . "[[:theme::submenu::label=Markup::link=/wiki/wikicode/:]]\n";

		if (true == $user->authHas('wiki', 'Wiki_Article', 'new', $model->UID)) {	
			$menu .= "[[:theme::submenu::label=New Article::link=/wiki/new/:]]\n";
		}
		return $menu;
	}

	if (true == $user->authHas('wiki', 'Wiki_Article', 'edit', $model->UID)) {
		$labels['newarticle'] = '[[:theme::submenu::label=New Article::link=/wiki/new/:]]';

		if ($raUID != 'no') {
			// we have an article, make some links for it
			$labels['article'] = "[[:theme::submenu::label=Article::"
								. "link=%%serverPath%%wiki/" . $model->alias . ":]]";

			$labels['talkpage'] = "[[:theme::submenu::label=Discussion::"
								. "link=%%serverPath%%wiki/talk/" . $model->alias . ":]]";

			$labels['history'] = "[[:theme::submenu::label=History::"
								. "link=%%serverPath%%wiki/history/" . $model->alias . ":]]";


			if ($type == 'content') { 
				$labels['editthis'] = "[[:theme::submenu::label=Edit This Page::"
								    . "link=%%serverPath%%wiki/edit/" . $model->alias . ":]]"; 
			}

			if ($type == 'talk') { 
				$labels['editthis'] = "[[:theme::submenu::label=Edit This Page::"
									. "link=%%serverPath%%wiki/edittalk/" . $model->alias . ":]]"; }
		}
	}
	
	$block = $theme->loadBlock('modules/wiki/views/menu.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
