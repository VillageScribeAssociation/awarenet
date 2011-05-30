<?

	require_once($kapenta->installPath . 'modules/docgen/inc/readcomments.inc.php');

//-------------------------------------------------------------------------------------------------
//*	test readingcomments.inc.php
//-------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$fileName = $kapenta->installPath . 'core/mysql.inc.php';
	$dc = docRead($fileName);

	$dg = '';
	$dg .= "file summary: " . implode("<br/>\n", $dc['summary']) . "<br/>\n";
	$dg .= "file description: " . implode("<br/>\n", $dc['desc']) . "<br/>\n";

	foreach($dc['functions'] as $fn) {
		$dg .=  "<h2>" . $fn['name'] . " - " . implode(" ", $fn['summary']) . "</h2>";
		$dg .=  "notes: " . implode("<br/>\n", $fn['desc']) . "<br/>\n";
		$dg .=  docMakeArgTable($fn) . "<br/>\n";
	}

	$page->load('modules/docgen/actions/testreadcomments.page.php');
	$page->blockArgs['docgentest'] = $dg;
	$page->render();

?>
