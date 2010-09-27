<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/inc/images__widthx.inc.php');

//--------------------------------------------------------------------------------------------------
//|	make an image which acts as a button, swapping div contents
//--------------------------------------------------------------------------------------------------
//arg: raUID - record alias or UID [string]
//arg: from - id of a div [string]
//arg: to - id of a div [string]
//opt: imageUID - overrides raUID [string]
//opt: size - size do display, default is width300 [string]

function images_swapbutton($args) { 
	if (array_key_exists('from', $args) == false) { return ''; }
	if (array_key_exists('to', $args) == false) { return ''; }
	if (array_key_exists('size', $args) == false) { $args['size'] = 'width300'; }

	$args['link'] = 'no';

	// TODO: check for JS injection here
	$js = "onClick=\"divCopyInnerHtml('" . $args['from'] . "', '" . $args['to'] . "');\" ";
	$js .= "style='cursor: pointer;' ";
	$imgTag = images__widthx($args);
	$imgTag = str_replace('<img ', '<img ' . $js, $imgTag);

	return $imgTag; //For Learners
}

//--------------------------------------------------------------------------------------------------

?>

