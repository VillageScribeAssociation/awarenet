<?php

//--------------------------------------------------------------------------------------------------\
//*	Apply simple code substitutions to module code
//--------------------------------------------------------------------------------------------------

	$subs = array(

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
		'$kapenta->fileSize(' => '$kapenta->fs->size(',

        'global $db;' => 'global $kapenta;',
        '$db->loadRange(' => '$kapenta->db->loadRange(',
        '$db->load(' => '$kapenta->db->load(',
        '$db->save(' => '$kapenta->db->save(',
        '$db->addmarkup(' => '$kapenta->db->addMarkup(',
        '$db->addMarkup(' => '$kapenta->db->addMarkup(',
        '$db->rmArray(' => '$kapenta->db->rmArray(',
        '$db->query(' => '$kapenta->db->query(',
        '$db->delete(' => '$kapenta->db->delete(',
        '$db->validate(' => '$kapenta->db->validate(',
        '$db->fetchAssoc(' => '$kapenta->db->fetchAssoc(',
        '$db->objectExists(' => '$kapenta->db->objectExists(',
        '$db->tableExists(' => '$kapenta->db->tableExists(',
        '$db->loadTables(' => '$kapenta->db->loadTables(',
        '$db->listTables(' => '$kapenta->db->listTables(',
        '$db->getSchema(' => '$kapenta->db->getSchema(',
        '$db->checkSchema(' => '$kapenta->db->checkSchema(',
        '$db->countRange(' => '$kapenta->db->countRange(',
        '$db->loadAlias(' => '$kapenta->db->loadAlias(',
        '$db->makeBlank(' => '$kapenta->db->makeBlank(',
        '$db->datetime(' => '$kapenta->db->datetime(',
        '$db->numRows(' => '$kapenta->db->numRows(',
        '$db->getObject(' => '$kapenta->db->getObject(',
        '$db->isShared(' => '$kapenta->db->isShared(',
        '$db->updateQuiet(' => '$kapenta->db->updateQuiet(',
        '$db->removeMarkup(' => '$kapenta->db->removeMarkup(',
        '$db->storeObjectXml(' => '$kapenta->db->storeObjectXml(',
        '$db->getObjectXml(' => '$kapenta->db->getObjectXml(',
        '$db->lasterr' => '$kapenta->db->lasterr',
        '$db->type' => '$kapenta->db->type',
        '$db->objectXmlToArray(' => '$kapenta->db->objectXmlToArray(',
        '$db->serialize(' => '$kapenta->db->serialize(',
        '$db->quoteType(' => '$kapenta->db->quoteType(',
        '$db->createTable(' => '$kapenta->db->createTable(',
        '$db->count' => '$kapenta->db->count',
        '$db->time' => '$kapenta->db->time',

        'global $page;' => 'global $kapenta;',
		'$page->blockArgs[' => '$kapenta->page->blockArgs[',
		'$page->load(' => '$kapenta->page->load(',
		'$page->render(' => '$kapenta->page->render(',

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
