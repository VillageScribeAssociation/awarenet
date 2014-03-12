<?php

	require_once($kapenta->installPath . 'modules/lessons/inc/coursexml.inc.php');
	require_once($kapenta->installPath . 'modules/lessons/models/stub.mod.php');
	require_once($kapenta->installPath . 'modules/lessons/models/collection.mod.php');	

//--------------------------------------------------------------------------------------------------
//*	import all course objects into the database
//--------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');

	$importer = new Lessons_CourseXml();
	
	$list = $importer->listAll();

	foreach($list as $item) {
		echo "Found: " . $item . "<br/>\n"; flush();
		$importer->loaded = false;
		$importer->load($item);

		if (true == $importer->loaded) {

			echo $importer->saveDb(); flush();
			echo $importer->makeStubs(); flush();
			echo "<hr/>\n";

		}	
	}

	//$report = $set->importAll();
	//echo $report;

	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]');

?>
