<?

//-------------------------------------------------------------------------------------------------
//	show deleted images
//-------------------------------------------------------------------------------------------------

	$sql = "select * from Images_Image";
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

			$sql = "update Images_Image set refUID='" . $db->addMarkup($row['refUID']) . "' where UID='" . $row['UID'] . "'";
			$db->query($sql);

			$sql = "update Images_Image set refModule='" . $db->addMarkup($row['refModule']) . "' where UID='" . $row['UID'] . "'";
			$db->query($sql);

		}

	}

	echo $theme->arrayToHtmlTable($tbl, false, false);

?>
