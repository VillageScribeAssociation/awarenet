<?php

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//-------------------------------------------------------------------------------------------------
//*	temporary / development action to clear spurious error messages
//-------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$sql = "select * from messages_message where title='Kapenta Error Message'";
	$result = $kapenta->db->query($sql);

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$item = $kapenta->db->rmArray($row);
		$model = new Messages_Message($item['UID']);
		$model->delete();
		echo "Deleting " . $model->UID . "...<br/>\n";
		flush();
	}

?>
