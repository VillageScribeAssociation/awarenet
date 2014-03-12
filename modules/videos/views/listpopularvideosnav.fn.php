<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list most popular videos on local node formatted for the nav
//--------------------------------------------------------------------------------------------------
//opt: ladder - ladder to display, default is videos.all [string]
//opt: num - max number of posts to show (default is 10) [string]

function videos_listpopularvideosnav($args) {
	global $theme;
	global $kapenta;
	global $session;

	$num = 10;					//%	number of items to show [int]
	$ladder = 'videos.all';		//%	name of popularity ladder to user [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->authHas('videos', 'videos_video', 'show')) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('ladder', $args)) { $ladder = $args['ladder']; }

	//----------------------------------------------------------------------------------------------
	//	get ladder from 'popular' module
	//----------------------------------------------------------------------------------------------
	$block = '[[:popular::ladder::name=' . $ladder . '::num=' . $num . ':]]';
	$raw = $theme->expandBlocks($block, '');
	$items = explode("\n", $raw);

	//----------------------------------------------------------------------------------------------
	//	make the block and return
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/videos/views/videosummarynav.block.php');
	foreach($items as $item) {
		if ('' != trim($item)) {
			$model = new Videos_Video($item);				
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
