<?php

//--------------------------------------------------------------------------------------------------\
//*	Apply simple code substitutions to module code
//--------------------------------------------------------------------------------------------------

	$subs = array(
		'$page->blockArgs[' => '$kapenta->page->blockArgs[',
		'$page->load(' => '$kapenta->page->load(',
		'$page->render(' => '$kapenta->page->render(',
		'$req->' => '$kapenta->request->',
		'$kapenta->fileExists(' => '$kapenta->fs->exists(',
		'$kapenta->filePutContents(' => '$kapenta->fs->put(',
		'$kapenta->fileGetContents(' => '$kapenta->fs->get(',
		'$registry->get(' => '$kapenta->registry->get(',
		'$registry->set(' => '$kapenta->registry->set(',
		'$registry->has(' => '$kapenta->registry->has(',
		'$registry->delete(' => '$kapenta->registry->delete(',
		'$registry->load(' => '$kapenta->registry->load(',
		'$registry->save(' => '$kapenta->registry->save(',
		'$registry->search(' => '$kapenta->registry->search(',
		'$registry->getPrefix(' => '$kapenta->registry->getPrefix(',
		'global $registry' => 'global $kapenta',
		'$kapenta->fileSize(' => '$kapenta->fs->size('
	);

	//----------------------------------------------------------------------------------------------
	//	find all files of interest, except this one
	//----------------------------------------------------------------------------------------------

	$files = $kapenta->fs->search('modules/', '.php');

	echo $kapenta->theme->expandBlocks('[[:theme::ifscrollheader:]]');
	echo "<h2>Searching for v2 calls in module code:</h2>\n";

	foreach($files as $file) {
		if (false == strpos($file, 'migratev3')) {
			echo "<div class='chatmmessageblack'><b>file:</b> $file</div>\n";
	
			$raw = $kapenta->fs->get($file);
			$new = $raw . '';
			foreach($subs as $find => $replace) {
				if (false !== strpos($raw, $find)) {
					echo "<div class='chatmessagered'><b>match:</b> $find</div>\n";
					$new = str_replace($find, $replace, $new);
				}
			}

			if (($raw !== $new) && (false == strpos($file, 'migrate'))) {
				$kapenta->fs->put($file, $new);
			}
		}
	}


	echo $kapenta->theme->expandBlocks('[[:theme::ifscrollfooter:]]');
?>
