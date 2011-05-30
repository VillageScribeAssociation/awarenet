<?

//--------------------------------------------------------------------------------------------------
//*	remove extra notifications from images_added
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load all notifications from the database
	//----------------------------------------------------------------------------------------------
	$sql = "select * from notifications_notification where refModule='gallery'";
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$model = new Notifications_Notification();
		$model->loadArray($row);

		//------------------------------------------------------------------------------------------
		//	group events
		//------------------------------------------------------------------------------------------
		if (('gallery' == $model->refModule) && ('images_added' == $model->refEvent)) {
			
			$conditions = array();
			$conditions[] = "refModule='gallery'";
			$conditions[] = "refEvent='" . $db->addMarkup('images_added') . "'";
			$conditions[] = "createdBy='" . $db->addMarkup($model->createdBy) . "'";
			$conditions[] = "UID != '" . $db->addMarkup($model->UID) . "'";

			$range = $db->loadRange('notifications_notification', '*', $conditions);
			$dates = array();

			$mdate = substr($model->createdOn, 0, 10);

			foreach($range as $item) {
				$date = substr($item['createdOn'], 0, 10);
				if ($mdate == $date) {
					if (false == array_key_exists($date, $dates)) {	$dates[$date] = array(); }
					$dates[$date][] = $item;
				}
			}

			foreach($dates as $date => $items) {
				if (count($items) > 1) {
					echo "date: $date items: " . count($items) . "<br/>\n";

					$longest = 0;
					$longestUID = '';

					foreach($items as $item) {
						if (strlen($item['content']) > $longest) { 
							$longest = strlen($item['content']);
							$longestUID = $item['UID'];
						}
					}

					echo "longestUID: $longestUID longest: $longest <br/>\n";
					foreach($items as $item) {
						if ($item['UID'] != $longestUID) {
							echo "delete: " . $item['UID'] . "<br/>\n";
							$db->delete($item['UID'], $model->dbSchema);
						}
					}

					$result = $db->query($sql);

				}
			}

		}


	}


?>
