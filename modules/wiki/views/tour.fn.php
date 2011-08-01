<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//|	user tour function, uses a wiki article to introduce a website feature
//--------------------------------------------------------------------------------------------------
//arg: article - UID or alias of a wiki_article object [string]
//arg: setting - user setting to control visibility [string]

function wiki_tour($args) {
	global $user;
	global $theme;
	global $session;

	$html = '';					//% return value [string]
	$setting = '';				//% name of user setting, eg wiki.tour.moblog [string]
	$article = '';				//% UID or alias of a wiki article [string]

	//----------------------------------------------------------------------------------------------
	//	check if user has seen the tour
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return ''; }
	if (false == array_key_exists('article', $args)) { return '(no user setting given)'; }
	if (false == array_key_exists('setting', $args)) { return '(no user setting given)'; }

	$setting = $args['setting'];
	$article = $args['article'];

	if ('wiki.tour.' != substr($setting, 0, 10)) { return '(invalid wiki.tour setting)'; }
	if ('hide' == $user->getSetting($setting)) { return ''; }

	$model = new Wiki_Article($article);
	$model->expandWikiCode();

	if (false == $model->loaded) { 
		if ('admin' == $user->role) { 
			$link = "<a href='%%serverPath%%wiki/" . $article . "'>" . $article . "</a>";
			return "(tour article not found: $link)";
		}
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$abstractHtml = '';

	$sections = $model->wikicode->sections;
	foreach($sections as $section) {
		if ('' == $section['title']) { $abstractHtml .= $section['content'] . "<br/>"; }
	}

	$block = $theme->loadBlock('modules/wiki/views/tour.block.php');
	$labels = array(
		'setting' => $setting,
		'article' => $article,
		'abstractHtml' => $abstractHtml
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
