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

	return $html;
}

?>
