<?

	require_once($kapenta->installPath . 'modules/newsletter/models/adunit.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a Adunit object
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Newsletter_Adunit object [string]
//opt: adunitUID - UID of a Newsletter_Adunit object, overrides UID [string]

function newsletter_showadunit($args) {
	global $user;
	global $theme;
	global $kapenta;

	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	$raUID = '';
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('adunitUID', $args)) { $raUID = $args['adunitUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Newsletter_Adunit($raUID);	//% the object we're editing [object:Newsletter_Adunit]

	if (false == $model->loaded) { return ''; }

	//	LOL
	//if (false == $user->authHas('newsletter', 'newsletter_adunit', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	hacky way to get first image or video
	//----------------------------------------------------------------------------------------------
	$orderBy = 'weight';
	$adMedia = '';

	$imgBlock = ''
	 . '[[:images::default'
	 . '::size=widthnav'
	 . '::refModule=newsletter'
	 . '::refModel=newsletter_adunit'
	 . '::refUID=' . $model->UID
	 . ':]]';

	$adMedia = $theme->expandBlocks($imgBlock, 'nav');

	if (false !== strpos($adMedia, 'unavailable/')) { $adMedia = ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/newsletter/views/showadunit.block.php');
	$labels = $model->extArray();
	$labels['adMedia'] = $adMedia;
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
