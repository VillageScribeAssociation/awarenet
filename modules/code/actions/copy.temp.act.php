<?php

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	temporary administrative action to copy code and files from backup table
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	$sql = "select UID, path from code_backup";
	$result = $kapenta->db->query($sql);

	$model = new Code_File();

	while($row = $kapenta->db->fetchAssoc($result)) {

		$conditions = array("UID='" . $kapenta->db->addMarkup($row['UID']) . "'");
		$range = $kapenta->db->loadRange('code_backup', '*', $conditions);

		$extant = $kapenta->db->loadRange('code_file', '*', $conditions);

		if (0 == count($extant)) {

			foreach($range as $item) {
				echo $item['UID'] . " - " . $item['path'] . "<br/>\n";
				$kapenta->db->save($item, $model->getDbSchema());
			}
		} else {
			echo $item['UID'] . " - " . $item['path'] . " (exists)<br/>\n";
		}
	}
	
	
