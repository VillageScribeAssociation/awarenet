<?

//--------------------------------------------------------------------------------------------------
//	returns a navbox title graphic
//--------------------------------------------------------------------------------------------------
//opt: label - text displayed on the box
//opt: width - width of the box - default=20
//opt: toggle - id of a div to toggle visibility
//opt: hidden - set to yes if div id hidden by default

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
	if (array_key_exists('width', $args)) 	{ $s['pxxNavBoxWidth'] = $args['width']; } // ditto

	//----------------------------------------------------------------------------------------------
	//	add toggle button 
	//----------------------------------------------------------------------------------------------	
	$toggle = ''; $onClick = ''; $eventJs = '';
	if (array_key_exists('toggle', $args) == true) {
		$icoFile = '%%serverPath%%themes/clockface/icons/btn-minus.png';
		if ((array_key_exists('hidden', $args) == true) AND ($args['hidden'] == 'yes')) 
			{ $icoFile = '%%serverPath%%themes/clockface/icons/btn-plus.png'; }

		$UID = createUID();
		$id = "id='ti" . $UID . "'";
		$onClick = "onClick=\"toggleVisible('ti" . $UID . "','" . $args['toggle'] . "');\"";
		$toggle = "<img $id class='navboxbtn' src='" . $icoFile . "' width='16px'>";
		$eventJs = "<script language='Javascript'>attachOnClick('ti" . $UID . "', "
		  	  	 . "\"toggleVisible('ti" . $UID . "','" . $args['toggle'] . "');\");</script>";
	}

	//----------------------------------------------------------------------------------------------
	//	make html (attache button event with javascript or it behaves strangely
	//----------------------------------------------------------------------------------------------	
	$html = "<div class='navbox' $onClick>" . $s['label'] . $toggle . "</div>\n$eventJs";

	return $html;
}

?>
