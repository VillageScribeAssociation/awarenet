<?

	require_once($kapenta->installPath . 'modules/notifications/models/notification.mod.php');

//--------------------------------------------------------------------------------------------------
//*	temporary administrative measure to cluster images in feed
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }


	//----------------------------------------------------------------------------------------------
	//	load all notifications from the database
	//----------------------------------------------------------------------------------------------
	$sql = "select * from notifications_notification";
	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$model = new Notifications_Notification();
		$model->loadArray($row);


		//------------------------------------------------------------------------------------------
		//	add event if not present
		//------------------------------------------------------------------------------------------
		switch($row['refModule']) {
			case 'gallery':	
				$model->content = notifications_removeDuplicateImages($model->content);
				$model->save();

				if ('' == $model->refEvent) { 
					$model->refEvent = 'images_added';
					$model->save();
					echo "added refEvent 'images_added'<br/>\n";
				}

		}

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

					if (false == strpos($model->content, "<!-- more images -->")) {
						$model->content = str_replace("</a>", "</a><!-- more images -->", $model->content);
					}

					foreach($items as $item) {
						$thumb = notifications_getImgBlock($item['content']);
						echo "thumbnail: " . htmlentities($thumb) . "<br/>\n";

						if (false == strpos($model->content, $thumb)) {
							$model->content = str_replace(
									"<!-- more images -->", 
									$thumb . "\n<!-- more images -->", 
									$model->content
							);								
						}

					}

					$model->content = str_replace("<br/>[ view image >> ]", "", $model->content);
					$model->content = str_replace("<br/>[ view image &gt;&gt; ]", "", $model->content);
					$model->content = str_replace("width300", "width100", $model->content);

					$model->content = notifications_removeDuplicateImages($model->content);

					$model->save();

					echo "<textarea rows='10' cols='100'>" . $model->content . "</textarea><br/><br/>\n";

				}
			}

		}


	}


//--------------------------------------------------------------------------------------------------
//	utility stuff
//--------------------------------------------------------------------------------------------------

function notifications_getImgBlock($content) {
	$startPos = strpos($content, "<a");
	if (false !== $startPos) { $content = substr($content, $startPos); }
	$endPos = strpos($content, "</a>");
	if (false !== $endPos) { $content = substr($content, 0, $endPos + 4); }

	$content = str_replace("<br/>[ view image >> ]", "", $content);
	$content = str_replace("<br/>[ view image &gt;&gt; ]", "", $content);
	$content = str_replace("width300", "width100", $content);

	return $content;
}

function notifications_removeDuplicateImages($content) {
	$content = str_replace("<a", "\n<a", $content);
	$content = str_replace("</a>", "</a>\n", $content);
	$lines = explode("\n", $content);
	$unique = array();
	foreach($lines as $line) {
		if ((false == in_array($line, $unique)) && ("" != $line)) {
			$unique[] = $line;
			echo "line: " . htmlentities($line) . "<br/>\n";
		}
	}

	$content = implode("\n", $unique);
	return $content;
}

?>
