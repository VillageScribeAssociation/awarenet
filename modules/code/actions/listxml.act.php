<?

//--------------------------------------------------------------------------------------------------
//*	list all public packages in XML format
//--------------------------------------------------------------------------------------------------

	header('Content-type: text/xml');

	$block = '[[:code::listpackagesxml:]]';
	$xml = $theme->expandBlocks($block, '');
	echo $xml;

?>
