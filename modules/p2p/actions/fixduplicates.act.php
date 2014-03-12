<?

//--------------------------------------------------------------------------------------------------
//*	find and fix duplicate items in the gifts table
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	do it
	//----------------------------------------------------------------------------------------------
	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]', '');
	echo "<h1>Removing duplicate P2P_Gift items</h1>";

	$total = 0;
	$line = 100;
	$dbSchema = $kapenta->db->getSchema('p2p_gift');

	$sql = "select * from p2p_gift";
	$result = $kapenta->db->query($sql);
	while($row = $kapenta->db->fetchAssoc($result)) {
		$item = $kapenta->db->rmArray($row);

		echo ".";
		$line--;
		if (0 == $line) { $line = 100; echo "<br/>"; flush(); }

		$conditions = array();
		$conditions[] = "type='" . $kapenta->db->addMarkup($item['type']) . "'";
		$conditions[] = "refModel='" . $kapenta->db->addMarkup($item['refModel']) . "'";
		$conditions[] = "refUID='" . $kapenta->db->addMarkup($item['refUID']) . "'";

		$range = $kapenta->db->loadRange('p2p_gift', '*', $conditions);
		if (count($range) > 1) {
			$first = true;
			foreach($range as $row) {
				if (false == $first) {
					$kapenta->db->delete($item['UID'], $dbSchema);
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
