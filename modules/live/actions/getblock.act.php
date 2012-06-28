<?

//--------------------------------------------------------------------------------------------------
//*	render and retun a block with at the current user's privilege
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments (permissions should be checked by blocks)
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('b', $_POST)) { $page->doXmlError('no request sent'); }
	$block = base64_decode($_POST['b']);
	$area = 'content';
	//echo $block . "<br/>\n";

	if (true == array_key_exists('area', $req->args)) { $area = $req->args['area']; }
	if (true == array_key_exists('area', $_POST)) { $area = $_POST['area']; }
	if (true == array_key_exists('a', $_POST)) { $area = $_POST['a']; }    

	$content = $theme->expandBlocks($block, $area);

	$content = str_replace('%%serverPath%%', $kapenta->serverPath, $content);
	$content = str_replace('%%defaultTheme%%', $kapenta->defaultTheme, $content);

	echo $content;

?>
