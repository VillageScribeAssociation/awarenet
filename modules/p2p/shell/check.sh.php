<?php

	require_once('../../../shinit.php');

//--------------------------------------------------------------------------------------------------
//	check/maintain gifts table
//--------------------------------------------------------------------------------------------------

	$range = $db->loadRange('p2p_peer', '*');
	$peers = array();
	$count_deleted = 0;

	$errorCount = 0;
	$fixCount = 0;


	$kill_on_sight = array(								//	items which should never have been 
		'live_trigger',									//	placed in the gifts table.
		'live_mailbox'
	);

	foreach($range as $item) {
		echo "peer: " . $item['name'] . ' ' . $item['url'] . ' (' . $item['UID'] . ')' . "\n";
		$peers[] = $item['UID'];
	}

	$sql = "select * from p2p_gift";
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$item = $db->rmArray($row);

		echo "p2p_gift::" . $item['UID'] . " peer=" . $item['peer'] . "\n";

		if (false == in_array($item['peer'], $peers)) {
			$report .= "ERROR: No such peer.\n";
			$report .= "Deleting...\n";

			delete_gift($item['UID']);
			echo $report;
		}

		//------------------------------------------------------------------------------------------
		//	check for invalid tables
		//------------------------------------------------------------------------------------------
		if (
			(true == in_array($item['refModel'], $kill_on_sight)) ||
			('tmp_' == substr($item['refModel'], 0, 4))
		) {
			echo ''
			 . 'p2p_gift::' . $item['UID'] . ' for'
			 . ' ' . $item['refModel'] . '::' . $item['refUID']
			 . ' Invalid object type: ' . $item['refModel'] . "\n";
			$errorCount++;
			$fixCount++;

			delete_gift($item['UID']);
		}

		//------------------------------------------------------------------------------------------
		//	check references to files, delete offer if bad
		//------------------------------------------------------------------------------------------
		if ('file' == $item['type']) {
			if (false == $kapenta->fileExists($item['fileName'])) { 
				echo ''
				 . 'p2p_gift::' . $item['UID'] . ' for'
				 . ' ' . $item['refModel'] . '::' . $item['refUID']
				 . ' Missing file: ' . $item['fileName'] . "\n";
				$errorCount++;
				$fixCount++;

				delete_gift($item['UID']);
			}
		}

		//------------------------------------------------------------------------------------------
		//	check references to objects, delete offer if bad
		//------------------------------------------------------------------------------------------
		if ('object' == $item['type']) {
			if (false == $db->objectExists($item['refModel'], $item['refUID'])) { 
				echo ''
				 . 'p2p_gift::' . $item['UID'] . ' for '
				 . $item['refModel'] . '::' . $item['refUID']
				 . ' Missing object: ' . $item['refModel'] . '::' . $item['refUID'] . "\n";

				delete_gift($item['UID']);
				$errorCount++;
				$fixCount++;

			} else {
				if (false == $db->isShared($item['refModel'], $item['refUID'])) {
					echo ''
					 . 'p2p_gift::' . $item['UID'] . ' for '
					 . $item['refModel'] . '::' . $item['refUID']
					 . ' Object not shared: ' . $item['refModel'] . '::' . $item['refUID'] . "\n";
					
					delete_gift($item['UID']);
					$errorCount++;
					$fixCount++;
				}
			}
		}

		//------------------------------------------------------------------------------------------
		//	check for duplicates
		//------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "peer='" . $db->addMarkup($item['peer']) . "'";		
		$conditions[] = "type='" . $db->addMarkup($item['type']) . "'";		
		$conditions[] = "refModel='" . $db->addMarkup($item['refModel']) . "'";
		$conditions[] = "refUID='" . $db->addMarkup($item['refUID']) . "'";
		$range = $db->loadRange('p2p_gift', '*', $conditions);

		if (count($range) > 1) {
			$first = true;
			foreach($range as $dup) {
				if (true == $first) {
					//	leave
				} else {
					echo ''
					 . "deleting duplicate gift " . $dup['UID'] . " for "
					 . $item['refModel'] . '::' .$item['UID'] . "\n";
					delete_gift($item['UID']);
				}
			}
		} 

	}
	
	echo "\nDeleted: $count_deleted invalid items.\n";
	echo "\nError count: $errorCount\n";
	echo "\nFix count: $fixCount\n";


	function delete_gift($UID) {
		global $db;
		global $count_deleted;
		$sql = "delete from p2p_gift where UID='" . $UID . "'";
		$db->query($sql);
		$count_deleted++;
	}

?>
