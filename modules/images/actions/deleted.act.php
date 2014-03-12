<?

//-------------------------------------------------------------------------------------------------
//*	show deleted images
//-------------------------------------------------------------------------------------------------
//DEPRECATED: image files are no longer kept after deletion TODO: remove

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	$sql = "select * from images_image";
	$result = $kapenta->db->query($sql);

	$tbl = array();

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$tbRow = array();
		$tbRow[] = $row['UID'];
		$tbRow[] = $row['refUID'];
		$tbRow[] = $row['refModule'];
		$tbRow[] = $row['title'];
		$tbRow[] = $row['fileName'];
		$tbRow[] = $row['editedOn'];
		$tbRow[] = $row['editedBy'];

		if (substr($row['refUID'], 0, 4) == 'del-') {
			$tbl[] = $tbRow;

			$row['refUID'] = str_replace('del-', '', $row['refUID']);
			$row['refModule'] = str_replace('del-', '', $row['refModule']);

			$sql = "update images_image set refUID='" . $kapenta->db->addMarkup($row['refUID']) . "' where UID='" . $row['UID'] . "'";
			$kapenta->db->query($sql);

			$sql = "update images_image set refModule='" . $kapenta->db->addMarkup($row['refModule']) . "' where UID='" . $row['UID'] . "'";
			$kapenta->db->query($sql);

		}

	}

	echo $theme->arrayToHtmlTable($tbl, false, false);

?>
