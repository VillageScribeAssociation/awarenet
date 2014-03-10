<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');

//-------------------------------------------------------------------------------------------------
//*	show all friendships in database
//-------------------------------------------------------------------------------------------------
//+	This is an administrative option - TODO: export as graphvis

	//---------------------------------------------------------------------------------------------
	//	authenticate
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	//---------------------------------------------------------------------------------------------
	//	load all friendships into array
	//---------------------------------------------------------------------------------------------

	$sql = "select * from User_Friendships";
	$result = $kapenta->db->query($sql);
	$ds = array();
	$report = '';

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$ds[] = array(
						'UID' => $row['UID'],
						'userUID' => $row['userUID'], 
						'friendUID' => $row['friendUID'], 
						'relationship' => $row['relationship'], 
						'status' => $row['status'],
						'check' => 'ok'
				);
	}

	//---------------------------------------------------------------------------------------------
	//	check reciprocity of all confirmed friendships
	//---------------------------------------------------------------------------------------------

	foreach($ds as $row) {
		if ('confirmed' == $row['status']) {
			if (false == hasFriend($ds, $row['friendUID'], $row['userUID'], 'confirmed')) {

				if (true == hasFriend($ds, $row['friendUID'], $row['userUID'], 'unconfirmed')) {
					$report .= "mismatched confirmation between " . $row['userUID'] . ' and ' . $row['friendUID'] . "<br/>\n";

					$recipUID = getFriendUID($ds, $row['friendUID'], $row['userUID'], 'unconfirmed');
					$recip = getDsByUID($ds, $recipUID);
					$report .= "reciprocal record ($recipUID) is " . $recip['status'] . " and should be 'confirmed'.<br/>\n";

					$model = new Users_Friendship($recipUID);
					$report .= "Loaded record " . $model->UID . " (status: " . $model->status . ")<br/>\n";					

					$model->status = 'confirmed';
					$model->save();

				} else {
					$report .= "unrequeited (confirmed) friendship between " . $row['userUID'] . ' and ' . $row['friendUID'] . "<br/>\n";
				}

			}
		}
	}

	//---------------------------------------------------------------------------------------------
	//	check for duplicates
	//---------------------------------------------------------------------------------------------

	foreach ($ds as $row) {
		$rCount = 0;
		
		foreach ($ds as $check) {
			if (($row['userUID'] == $check['userUID']) && ($row['friendUID'] == $check['friendUID'])) {
				$rCount += 1;
			}
		}
		if ($rCount > 1) {
			$report .= "duplicate friendship between " . $row['userUID'] . ' and ' . $row['friendUID'] . "<br/>";
		}

	}

	//---------------------------------------------------------------------------------------------
	//	check for reciprocal confirmations
	//---------------------------------------------------------------------------------------------

	foreach ($ds as $row) {
		if ($row['status'] == 'unconfirmed') {
			if (true == hasFriend($ds, $row['friendUID'], $row['userUID'], 'unconfirmed')) {
				$report .= "reciprocal unconfirmed friendship between " . $row['userUID'] . ' and ' . $row['friendUID'] . "<br/>\n";

				$firstUID = getFriendUID($ds, $row['userUID'], $row['friendUID'], 'unconfirmed');
				if (false != $firstUID) {
					$model = new Users_Friendship($firstUID);
					$report .= "record ($firstUID) should be 'confirmed'.<br/>\n";
					$report .= "Loaded record (1) " . $model->UID . " (status: " . $model->status . ")<br/>\n";					
					$model->status = 'confirmed';
					$model->save();

				}

				$secondUID = getFriendUID($ds, $row['friendUID'], $row['userUID'], 'unconfirmed');
				if (false != $secondUID) {
					$model = new Users_Friendship($secondUID);
					$report .= "record ($secondUID) should be 'confirmed'.<br/>\n";
					$report .= "Loaded record (2) " . $model->UID . " (status: " . $model->status . ")<br/>\n";					
					$model->status = 'confirmed';
					$model->save();
				}


			}
		}
	}

	//---------------------------------------------------------------------------------------------
	//	make array into table
	//---------------------------------------------------------------------------------------------

	$ftable = array();
	$ftable[] = array('From', 'To', 'relationship', 'status');

	foreach($ds as $row) {
		$from = '[[:users::namelink::userUID=' . $row['userUID'] . ':]]';
		$to = '[[:users::namelink::userUID=' . $row['friendUID'] . ':]]';
		$relationship = $row['relationship'];
		$status = "<span style='ajaxmsg'>" . $row['status'] . "</span>";
		if ('unconfirmed' == $row['status']) {
			$status = "<span style='ajaxerror'>" . $row['status'] . "</span>";
		}

		$ftable[] = array($from, $to, $relationship, $status);
	}

	$ftableHtml .= $theme->arrayToHtmlTable($ftable, true, true);
	$ftableHtml .= $report;

	//---------------------------------------------------------------------------------------------
	//	render the page
	//---------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/users/actions/allfriends.page.php');
	$kapenta->page->blockArgs['friendstable'] = $ftableHtml;
	$kapenta->page->render();

	//---------------------------------------------------------------------------------------------
	//	utility functions
	//---------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	discover if a record exists
	//---------------------------------------------------------------------------------------------
	
	function hasFriend($ds, $userUID, $friendUID, $status) {
		foreach($ds as $row) {
			if (($row['userUID'] == $userUID) && ($row['friendUID'] == $friendUID) && ($row['status'] == $status)) {
				return true;
			}
		}
		return false;
	}

	//---------------------------------------------------------------------------------------------
	//	get a friendship's recordUID
	//---------------------------------------------------------------------------------------------
	
	function getFriendUID($ds, $userUID, $friendUID, $status) {
		foreach($ds as $row) {
			if (($row['userUID'] == $userUID) && ($row['friendUID'] == $friendUID) && ($row['status'] == $status)) {
				return $row['UID'];
			}
		}
		return false;
	}

	//---------------------------------------------------------------------------------------------
	//	get a ds row given record UID
	//---------------------------------------------------------------------------------------------

	function getDsByUID($ds, $UID) {
		foreach($ds as $row) {
			if ($row['UID'] == $UID) { return $row; }
		}
		return false;
	}

?>
