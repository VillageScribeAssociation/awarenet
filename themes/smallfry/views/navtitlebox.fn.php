<?

//--------------------------------------------------------------------------------------------------
//*	returns a nav block title div
//--------------------------------------------------------------------------------------------------
//opt: label - text displayed on the box [string]
//opt: width - width of the box - default=20 [string]
//opt: toggle - id of a div to toggle visibility [string]
//opt: divid - id of title bar div [string]
//opt: hidden - set to yes if div is hidden by default, initial state of (+) button [string]

function theme_navtitlebox($args) {
	global $kapenta;
	global $theme;

	$s = $theme->style;
	$s['label'] = 'label';
	$s['rss'] = '';
	$s['link'] = '';
	
	$divId = '';	
	
	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('label', $args)) { $s['label'] = $args['label']; }   
	if (true == array_key_exists('width', $args)) { $s['pxxNavBoxWidth'] = $args['width']; }

	if (true == array_key_exists('divid', $args)) {
		$divId = $args['divid'];
	} else {
		if (true == array_key_exists('toggle', $args)) {
			$divId = $args['toggle'] . 'ntb';
		} else {
			$divId = 'tb' . $kapenta->createUID();
		}
	}

	//----------------------------------------------------------------------------------------------
	//	add toggle button 
	//----------------------------------------------------------------------------------------------	
	$toggle = '';
	$onClick = '';
	$eventJs = '';
	$style = '';

	if (array_key_exists('toggle', $args) == true) {
		$icoFile = '%%serverPath%%themes/%%defaultTheme%%/images/icons/btn-minus.png';
		if ((array_key_exists('hidden', $args) == true) AND ('yes' == $args['hidden'])) 
			{ $icoFile = '%%serverPath%%themes/%%defaultTheme%%/images/icons/btn-plus.png'; }

		$UID = $kapenta->createUID();
		$id = "id='ti" . $UID . "'";
		$style = "style='cursor: pointer;'";
		$onClick = "onClick=\"toggleVisible('ti" . $UID . "','" . $args['toggle'] . "');\"";
		$toggle = "<img $id class='navboxbtn' src='" . $icoFile . "' width='16px'>";
		$eventJs = "<script language='Javascript'>attachOnClick('ti" . $UID . "', "
		  	  	 . "\"kutils.toggleVisible('ti" . $UID . "','" . $args['toggle'] . "');\");</script>";
	}

	//----------------------------------------------------------------------------------------------
	//	make html (attach button event with javascript or it behaves strangely
	//----------------------------------------------------------------------------------------------	
	$html = "<div id='$divId' class='navbox' $onClick $style>" . $s['label'] . $toggle . "</div>\n$eventJs";

	//----------------------------------------------------------------------------------------------
	//	a rare concession to IE7 and it's box model, since navtitleboxes are used everywhere
	//----------------------------------------------------------------------------------------------	
	if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
		$html = str_replace('navboxbtn', 'navboxbtnie', $html);
	}

	return $html;
}

?>
