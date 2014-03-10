<?

//--------------------------------------------------------------------------------------------------
//*	remove extra notifications from images_added
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load all notifications from the database
	//----------------------------------------------------------------------------------------------
	$sql = "select * from notifications_notification where refModule='gallery'";
	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$model = new Notifications_Notification();
		$model->loadArray($row);

		//------------------------------------------------------------------------------------------
		//	group events
		//------------------------------------------------------------------------------------------
		if (('gallery' == $model->refModule) && ('images_added' == $model->refEvent)) {
			
			$conditions = array();
			$conditions[] = "refModule='gallery'";
			$conditions[] = "refEvent='" . $kapenta->db->addMarkup('images_added') . "'";
			$conditions[] = "createdBy='" . $kapenta->db->addMarkup($model->createdBy) . "'";
			$conditions[] = "UID != '" . $kapenta->db->addMarkup($model->UID) . "'";

			$range = $kapenta->db->loadRange('notifications_notification', '*', $conditions);
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
							$kapenta->db->delete($item['UID'], $model->dbSchema);
						}
					}

					$result = $kapenta->db->query($sql);

				}
			}

		}


	}


?>
