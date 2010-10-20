<?

//--------------------------------------------------------------------------------------------------
//*	render and retun a block with at the current user's privilege
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments (permissions should be checked by blocks)
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('b', $_POST)) { $page->doXmlError('no request sent'); }
	$block = base64_decode($_POST['b']);
	//echo $block . "<br/>\n";
	$content = $theme->expandBlocks($block, '');
	$content = str_replace('%%serverPath%%', $kapenta->serverPath, $content);
	//TODO: more page/block args here

	echo $content;

?>
