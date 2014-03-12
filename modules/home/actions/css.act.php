<?

//--------------------------------------------------------------------------------------------------
//*	dynamically reformatted and cached user CSS
//--------------------------------------------------------------------------------------------------
//TODO: consider moving defaults to the theme itself

	//----------------------------------------------------------------------------------------------
	//	check that a valid css file has been requested
	//----------------------------------------------------------------------------------------------
	$files = array('default.css', 'windows.css', 'iframe.css', 'mobile.css', 'tablet.css');
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	if (false == in_array($kapenta->request->ref, $files)) { $kapenta->page->do404("Unknown stylesheet."); }

	if (('default.css' == $kapenta->request->ref) && (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))) {
		$kapenta->request->ref = 'iesucks.css';
	}

	//----------------------------------------------------------------------------------------------
	//	check theme defaults
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
		'theme.i.logo' => 'themes/' . $kapenta->defaultTheme . '/images/awareNetLogo.png'
	);

	foreach($defaults as $key => $value) {
		$rVal = $kapenta->registry->get($key);
		if (('' == $rVal) && ('' != $value)) { $kapenta->registry->set($key, $value); }
		if ('' != $rVal) { $defaults[$key] = $rVal; }	// replaced with cusomized default theme
	}

	//----------------------------------------------------------------------------------------------
	//	system default colors and images
	//----------------------------------------------------------------------------------------------
	$colors = explode('|', $kapenta->registry->get('theme.colors'));
	$images = explode('|', $kapenta->registry->get('theme.images'));

	//----------------------------------------------------------------------------------------------
	//	try to load colors and images from user profile
	//----------------------------------------------------------------------------------------------
	foreach($defaults as $key => $value) {
		$check = $kapenta->user->get(str_replace('theme.', 'ut.', $key));
		if ('' != $check) { $defaults[$key] = $check; }
	}

	//----------------------------------------------------------------------------------------------
	//	load CSS file and perform replacements
	//----------------------------------------------------------------------------------------------
	$bgImg = $defaults['theme.c.background'];

	if ('' != $defaults['theme.i.background']) {
		$bgImg = 'url(' . $kapenta->serverPath . $defaults['theme.i.background'] . ') fixed';
	}

	$defaults['pageBackground'] = $bgImg;

	$fileName = 'themes/' . $kapenta->defaultTheme . '/css/' . $kapenta->request->ref;
	$raw = $kapenta->fs->get($fileName);

	$search = array();
	$replace = array();

	$defaults['serverPath'] = $kapenta->serverPath;
	$defaults['defaultTheme'] = $kapenta->defaultTheme;

	foreach($defaults as $key => $value) {
		$search[] = '%%' . $key . '%%';
		$replace[] = $value;
	}	

	$css = str_replace($search, $replace, $raw);

	header('Content-type: text/css');
	echo $css;

?>
