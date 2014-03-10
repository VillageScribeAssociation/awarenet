<?php

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	temporary administrative action to copy code and files from backup table
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$sql = "select UID, path from code_backup";
	$result = $db->query($sql);

	$model = new Code_File();

	while($row = $db->fetchAssoc($result)) {

		$conditions = array("UID='" . $db->addMarkup($row['UID']) . "'");
		$range = $db->loadRange('code_backup', '*', $conditions);

		$extant = $db->loadRange('code_file', '*', $conditions);

		if (0 == count($extant)) {

			foreach($range as $item) {
				echo $item['UID'] . " - " . $item['path'] . "<br/>\n";
				$db->save($item, $model->getDbSchema());
			}
		} else {
			echo $item['UID'] . " - " . $item['path'] . " (exists)<br/>\n";
		}
	}
	
	
