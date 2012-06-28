<?

	require_once($kapenta->installPath . 'modules/images/models/images.set.php');

//--------------------------------------------------------------------------------------------------
//|	initialize a javascript slideshow on the page
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may own images [string]
//arg: refUID - UID of object which may own images [string]

function images_slideshowjs($args) {
	global $user;
	global $page;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(refModule not given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(refModel not given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(refUID not given)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	//TODO: checking and sanitization here
	$set = new Images_Images($refModule, $refModel, $refUID);

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$html .= ''
	 //. "[[:theme::navtitlebox::label=Slide Show:]]\n"
	 . "<div id='divSS"  . $refUID . "'></div>\n"
	 . "<script language='Javascript'>\n"
	 . "\tvar ss" . $refUID . " = new KSlideshow('divSS" . $refUID . "', 'ss" . $refUID . "');\n";

	foreach($set->members as $item) {
		$title = htmlentities($item['title']);
		$caption = htmlentities($item['caption']);
		$caption = str_replace("\n", '<br/>', $caption);
		$caption = str_replace("\r", '', $caption);
		$html .= "\tss" . $refUID . ".add('" . $item['UID'] . "', '$title', '$caption');\n";
	}

	$html .= "\tss" . $refUID . ".render();\n";
	$html .= "</script>\n";

	return $html;
}

?>
