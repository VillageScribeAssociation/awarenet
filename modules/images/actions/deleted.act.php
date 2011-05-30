<?

//-------------------------------------------------------------------------------------------------
//	show deleted images
//-------------------------------------------------------------------------------------------------

	$sql = "select * from images_image";
	$result = $db->query($sql);

	$tbl = array();

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
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

			$sql = "update images_image set refUID='" . $db->addMarkup($row['refUID']) . "' where UID='" . $row['UID'] . "'";
			$db->query($sql);

			$sql = "update images_image set refModule='" . $db->addMarkup($row['refModule']) . "' where UID='" . $row['UID'] . "'";
			$db->query($sql);

		}

	}

	echo $theme->arrayToHtmlTable($tbl, false, false);

?>
