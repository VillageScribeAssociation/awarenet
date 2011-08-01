<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list most popular posts on local node formatted for the nav
//--------------------------------------------------------------------------------------------------
//opt: ladder - ladder to display, default is moblog.all [string]
//opt: num - max number of posts to show (default is 10) [string]

function moblog_listpopularnav($args) {
	global $theme;
	global $user;
	global $session;

	$num = 10;					//%	number of items to show [int]
	$ladder = 'moblog.all';		//%	name of popularity ladder to user [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('moblog', 'moblog_post', 'show')) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('ladder', $args)) { $ladder = $args['ladder']; }

	//----------------------------------------------------------------------------------------------
	//	get ladder from 'popular' module
	//----------------------------------------------------------------------------------------------
	$block = '[[:popular::ladder::name=' . $ladder . '::num=' . $num . ':]]';
	$raw = $theme->expandBlocks($block, '');
	$items = explode("\n", $raw);

	//echo "listing popular nav, args ok, ladder: $ladder<br/>" . str_replace("\n", "<br/>\n", $raw) . "<br/>";

	//----------------------------------------------------------------------------------------------
	//	make the block and return
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/moblog/views/summarynav.block.php');
	foreach($items as $item) {
		if ('' != trim($item)) {
			$model = new Moblog_Post($item);				
			if (true == $model->loaded) {
				$ext = $model->extArray();
				$html .= $theme->replaceLabels($ext, $block);
			}
		}
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
