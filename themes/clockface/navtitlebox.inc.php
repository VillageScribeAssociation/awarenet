<?

//--------------------------------------------------------------------------------------------------
//	returns a navbox title graphic
//--------------------------------------------------------------------------------------------------
//	arguments:
//		link:	URL - what happens when you click on this - default=none
//		rss:	rss feed for this section - default=none
//		style:	box|arrowleft|arrowright|rounded|blunt
//		width:	width of the box - default=20
//		label:	text displayed on the box

function theme_navtitlebox($args) {
	global $installPath;
	global $serverPath;
	global $defaultTheme;
	global $page;
	$s = $page->style;
	
	//----------------------------------------------------------------------------------------------
	//	read arguments
	//----------------------------------------------------------------------------------------------
	$s['label'] = 'label'; $s['rss'] = ''; $s['link'] = '';

	if (array_key_exists('label', $args)) 	{ $s['label'] = $args['label']; }   
	if (array_key_exists('rss', $args)) 	{ $s['rss'] = $args['rss']; }   // unused in awareNet
	if (array_key_exists('link', $args)) 	{ $s['link'] = $args['link']; } // unused in awareNet
	if (array_key_exists('width', $args)) 	{ $s['pxxNavBoxWidth'] = $args['width']; } // ditto

	$html = "<div class='navbox'>" . $s['label'] . "</div>\n";

	return $html;
}

?>
