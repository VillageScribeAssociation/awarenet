<?

	require_once($kapenta->installPath . 'modules/docgen/inc/tokenizer.inc.php');
	require_once($kapenta->installPath . 'modules/docgen/inc/lexer.inc.php');
	//require_once($kapenta->installPath . 'modules/docgen/models/source.class.php');

//--------------------------------------------------------------------------------------------------
//*	initialize
//--------------------------------------------------------------------------------------------------

	//$testFile = $kapenta->installPath. 'modules/docgen/GlobalFunctions.txt';
	$testFile = $kapenta->installPath. 'modules/docgen/test.txt';
	$source = implode(file($testFile));

	$cells = dgTokenizeSource($source);

	echo count($cells) . " cells<br/>\n";
	$cells = dgSetSourceColor($cells);
	$html = dgCellsToHtml($cells);

	echo $html;

	$model = new SourceFile($cells);
	dgTokenizeCells($model);

?>
