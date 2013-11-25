<?

//--------------------------------------------------------------------------------------------------
//|	assembles HTML header for scrolling, progressively updated iframe
//--------------------------------------------------------------------------------------------------
//opt: title - page title, default is website name [string]

function theme_ifscrollheader($args) {
	global $kapenta;
	global $user;

	$title = $kapenta->websiteName;				//%	page title [string]
	$html = '';									//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('title', $args)) { $title = $args['title']; }

	//----------------------------------------------------------------------------------------------
	//	create css
	//----------------------------------------------------------------------------------------------

	$defaults = array(
		'theme.colors' => 'darkest|dark|medium|light|lighter|lightest|text|background|link|action',
		'theme.c.darkest' => '#000',
		'theme.c.dark' => '#444',
		'theme.c.medium' => '#666',
		'theme.c.light' => '#aaa',
		'theme.c.lighter' => '#eee',
		'theme.c.lightest' => '#fff',
		'theme.c.text' => '#333',
		'theme.c.link' => '#365a10',
		'theme.c.action' => '#799f3e',
		'theme.c.background' => '#aaa',
		'theme.images' => 'logo|background',
		'theme.i.background' => '',
		'theme.i.logo' => 'themes/smallfry/images/awareNetLogo.png'
	);

	foreach($defaults as $key => $value) {
		$rVal = $kapenta->registry->get($key);
		if (('' == $rVal) && ('' != $value)) { $kapennta->registry->set($key, $value); }
		if ('' != $rVal) { $defaults[$key] = $rVal; }	// replaced with cusomized default theme
	}

	//	system default colors and images
	$colors = explode('|', $kapenta->registry->get('theme.colors'));
	$images = explode('|', $kapenta->registry->get('theme.images'));

	//	try to load colors and images from user profile
	foreach($defaults as $key => $value) {
		$check = $user->get(str_replace('theme.', 'ut.', $key));
		if ('' != $check) { $defaults[$key] = $check; }
	}

	//	load CSS file and perform replacements
	$bgImg = $defaults['theme.c.background'];

	if ('' != $defaults['theme.i.background']) {
		$bgImg = 'url(' . $kapenta->serverPath . $defaults['theme.i.background'] . ') fixed';
	}

	$defaults['pageBackground'] = $bgImg;

	$fileName = 'themes/' . $kapenta->defaultTheme . '/css/windows.css';
	$raw = $kapenta->fs->get($fileName);

	$search = array();
	$replace = array();
	foreach($defaults as $key => $value) {
		$search[] = '%%' . $key . '%%';
		$replace[] = $value;
	}	

	$css = str_replace($search, $replace, $raw);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $kapenta->theme->loadBlock('themes/smallfry/views/ifscrollheader.block.php');

	$labels = array(
		'serverPath' => $kapenta->serverPath,
		'defaultTheme' => $kapenta->defaultTheme,
		'pageTitle' => $title,
		'css' => $css
	);

	$html = $kapenta->theme->replaceLabels($labels, $block);

	return $html;
}

?>
