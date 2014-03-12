<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list most popular posts on local node formatted for the nav
//--------------------------------------------------------------------------------------------------
//opt: ladder - ladder to display, default is images.all [string]
//opt: num - number of images to show (default is 25) [string]
//opt: size - image size to user [string]

function images_popular($args) {
	global $theme;
	global $kapenta;
	global $session;

	$size = 'thumbsm';			//%	image size [string]
	$num = 10;					//%	number of items to show [int]
	$ladder = 'images.all';		//%	name of popularity ladder to user [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->authHas('images', 'images_image', 'show')) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('ladder', $args)) { $ladder = $args['ladder']; }
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }

	//----------------------------------------------------------------------------------------------
	//	get ladder from 'popular' module
	//----------------------------------------------------------------------------------------------
	$block = '[[:popular::ladder::name=' . $ladder . '::num=' . $num . ':]]';
	$raw = $theme->expandBlocks($block, '');
	$items = explode("\n", $raw);

	//----------------------------------------------------------------------------------------------
	//	make the block and return
	//----------------------------------------------------------------------------------------------
	foreach($items as $item) {
		if ('' != trim($item)) {
			$model = new Images_Image($item);				
			if (true == $model->loaded) {
				$ext = $model->extArray();
				$html .= ''
				 . "<a href='%%serverPath%%images/show/" . $ext['alias'] . "'>"
				 . "<img"
				 . " src='%%serverPath%%images/" . $size . '/' . $ext['alias'] . "'"
				 . " title='" . $ext['title'] . "'"
				 . " border='0' vspace='5px' hspace='5px' "
				 . "/></a>";
			}
		}
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
