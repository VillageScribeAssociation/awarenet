<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/inc/wikicode.class.php');

//--------------------------------------------------------------------------------------------------
//|	menu for wiki, no arguments
//--------------------------------------------------------------------------------------------------
//opt: raUID - recordAlias or UID of a Wiki_Revision object [string]
//opt: type - may be 'content' (default) or 'talk', requires raUID [string]

function wiki_revmenu($args) {
		global $theme;
		global $user;

	if (false == array_key_exists('raUID', $args)) { return ''; }

	$labels = array();
	$labels['prevrevision'] = '';
	$labels['nextrevision'] = '';
	$labels['allhistory'] = '';
	$labels['currentarticle'] = '';

	$model = new Wiki_Revision($args['raUID']);
	if (false == $model->loaded) { return ''; }

	if (true == $user->authHas('wiki', 'wiki_article', 'show', $model->articleUID)) {

		$labels['currentarticle'] = "[[:theme::submenu::label=Current Article::"
								. "link=%%serverPath%%wiki/" . $model->articleUID . ":]]";

		$labels['allhistory'] = "[[:theme::submenu::label=All::"
								. "link=%%serverPath%%wiki/history/" . $model->articleUID . ":]]";

		$prevUID = $model->getPreviousUID();
		if (false !== $prevUID) {
			$labels['prevrevision'] = '[[:theme::submenu::label=&lt;&lt; Previous Revision'
									 . '::link=/wiki/showrevision/' . $prevUID . ':]]';
		}

		$nextUID = $model->getNextUID();
		if (false !== $nextUID) {
			$labels['nextrevision'] = '[[:theme::submenu::label=Next Revision &gt;&gt;'
									 . '::link=/wiki/showrevision/' . $nextUID . ':]]';
		}


	}
	
	$block = $theme->loadBlock('modules/wiki/views/revmenu.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
