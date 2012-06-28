<?

//--------------------------------------------------------------------------------------------------
//*	returns a navbox title graphic
//--------------------------------------------------------------------------------------------------
//opt: label - text displayed on the box [string]
//opt: width - width of the box - default=20 [string]
//opt: toggle - id of a div to toggle visibility [string]
//opt: hidden - set to yes if div is hidden by default, initial state of (+) button [string]

function theme_navtitlebox($args) {
	global $kapenta;
	global $theme;
	global $page;

	$s = $theme->style;
	$s['label'] = 'label';
	$s['rss'] = '';
	$s['link'] = '';
	
	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('label', $args)) { $s['label'] = $args['label']; }   
	if (true == array_key_exists('width', $args)) { $s['pxxNavBoxWidth'] = $args['width']; }

	//----------------------------------------------------------------------------------------------
	//	add toggle button 
	//----------------------------------------------------------------------------------------------	
	$toggle = '';
	$onClick = '';
	$eventJs = '';
	$style = '';
	$script = '';

	$UID = $kapenta->createUID();

	if (array_key_exists('toggle', $args) == true) {

		$icoHide = '%%serverPath%%themes/%%defaultTheme%%/images/icons/btn-minus.png';
		$icoShow = '%%serverPath%%themes/%%defaultTheme%%/images/icons/btn-plus.png';

		$icoFile = $icoHide;
		if ((true == array_key_exists('hidden', $args)) AND ('yes' == $args['hidden'])) {
			$icoFile = $icoShow;
		}

		$style = "style='cursor: pointer;'";

		$toggle = ''
		 . "<img"
		 . " id='ti" . $UID . "'"
		 . " class='navboxbtn'"
		 . " src='" . $icoFile . "'"
		 . " " . $style
		 . " width='16px'>";

		$script .= ''
		. "<script>\n"
		. "\t$('#td" . $UID . "').click(\n"
		. "\t\tfunction(event) {\n"
		. "\t\t\tkutils.toggleNTVisible('ti$UID', '" . $args['toggle'] . "');"
		. "\t\t}\n"
		. "\t);\n"
		. "</script>\n";

		/* -- moved to utils.js, delete once tested
		. "\t\t\tvar cbFn = function() { if (window.parent) { kutils.resizeIFrame(); } }\n"
		. "\t\t\tif ($('#" . $args['toggle'] . "').is(':hidden')) {\n"
		. "\t\t\t\t$('#" . $args['toggle'] . "').css('visibility', 'visible');\n"
		. "\t\t\t\t$('#ti$UID').attr('src', '$icoHide');\n"
		. "\t\t\t\t$('#" . $args['toggle'] . "').show('fast', cbFn);\n"
		. "\t\t\t} else {\n"
		. "\t\t\t\t$('#" . $args['toggle'] . "').hide('fast', cbFn);\n"
		. "\t\t\t\t$('#ti$UID').attr('src', '$icoShow');\n"
		. "\t\t\t}\n"
		*/

	}

	//----------------------------------------------------------------------------------------------
	//	make html (attache button event with javascript or it behaves strangely
	//----------------------------------------------------------------------------------------------	
	$html = ''
	 . "<div class='navbox' id='td" . $UID . "' $onClick $style>"
	 . $s['label'] . $toggle
	 . "</div>\n$script";

	//----------------------------------------------------------------------------------------------
	//	a rare concession to IE7 and it's box model, since navtitleboxes are used everywhere
	//----------------------------------------------------------------------------------------------	
	if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
		$html = str_replace('navboxbtn', 'navboxbtnie', $html);
	}

	return $html;
}

//	previous version of this, not using jQuery
/*
function theme_navtitlebox($args) {
	global $kapenta;
	global $theme;
	$s = $theme->style;
	$s['label'] = 'label';
	$s['rss'] = '';
	$s['link'] = '';
	
	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('label', $args)) { $s['label'] = $args['label']; }   
	if (true == array_key_exists('width', $args)) { $s['pxxNavBoxWidth'] = $args['width']; }

	//----------------------------------------------------------------------------------------------
	//	add toggle button 
	//----------------------------------------------------------------------------------------------	
	$toggle = '';
	$onClick = '';
	$eventJs = '';
	$style = '';

	if (array_key_exists('toggle', $args) == true) {
		$icoFile = '%%serverPath%%themes/clockface/icons/btn-minus.png';
		if ((array_key_exists('hidden', $args) == true) AND ('yes' == $args['hidden'])) 
			{ $icoFile = '%%serverPath%%themes/clockface/icons/btn-plus.png'; }

		$UID = $kapenta->createUID();
		$id = "id='ti" . $UID . "'";
		$style = "style='cursor: pointer;'";
		$onClick = "onClick=\"toggleVisible('ti" . $UID . "','" . $args['toggle'] . "');\"";
		$toggle = "<img $id class='navboxbtn' src='" . $icoFile . "' width='16px'>";
		$eventJs = "<script language='Javascript'>attachOnClick('ti" . $UID . "', "
		  	  	 . "\"kutils.toggleVisible('ti" . $UID . "','" . $args['toggle'] . "');\");</script>";
	}

	//----------------------------------------------------------------------------------------------
	//	make html (attache button event with javascript or it behaves strangely
	//----------------------------------------------------------------------------------------------	
	$html = "<div class='navbox' $onClick $style>" . $s['label'] . $toggle . "</div>\n$eventJs";

	//----------------------------------------------------------------------------------------------
	//	a rare concession to IE7 and it's box model, since navtitleboxes are used everywhere
	//----------------------------------------------------------------------------------------------	
	if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
		$html = str_replace('navboxbtn', 'navboxbtnie', $html);
	}

	return $html;
}

*/

?>
