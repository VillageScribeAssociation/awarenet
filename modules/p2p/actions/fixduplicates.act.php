<?

//--------------------------------------------------------------------------------------------------
//*	find and fix duplicate items in the gifts table
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	do it
	//----------------------------------------------------------------------------------------------
	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]', '');
	echo "<h1>Removing duplicate P2P_Gift items</h1>";

	$total = 0;
	$line = 100;
	$dbSchema = $db->getSchema('p2p_gift');

	$sql = "select * from p2p_gift";
	$result = $db->query($sql);
	while($row = $db->fetchAssoc($result)) {
		$item = $db->rmArray($row);

		echo ".";
		$line--;
		if (0 == $line) { $line = 100; echo "<br/>"; flush(); }

		$conditions = array();
		$conditions[] = "type='" . $db->addMarkup($item['type']) . "'";
		$conditions[] = "refModel='" . $db->addMarkup($item['refModel']) . "'";
		$conditions[] = "refUID='" . $db->addMarkup($item['refUID']) . "'";

		$range = $db->loadRange('p2p_gift', '*', $conditions);
		if (count($range) > 1) {
			$first = true;
			foreach($range as $row) {
				if (false == $first) {
					$db->delete($item['UID'], $dbSchema);
					echo "<br/>Removed duplicate: ". $item['refModel'] .'::'. $item['refUID'] ."<br/>";
					$total++;
				}
				$first = false;
			}
		}
	}

	echo "Total removed: $total<br/>";

	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', '');

?>
