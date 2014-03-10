<?

	require_once($kapenta->installPath . 'modules/notifications/models/notification.mod.php');	
	require_once($kapenta->installPath . 'modules/notifications/models/userindex.mod.php');

//--------------------------------------------------------------------------------------------------
//	import user notifications from pervious version of module (XML)
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	$conditions = array("1=1");
	//$conditions[] = "user='admin'";
	$range = $kapenta->db->loadRange('notices', '*', $conditions);


	foreach($range as $row) {
		echo "<h1>User: " . $row['user'] . "</h1>";
		echo "<textarea rows='10' cols='80'>" . $row['notices'] . "</textarea><br/>\n";

		$doc = new KXmlDocument($row['notices']);

		$notices = $doc->getChildren(1);
		foreach($notices as $noticeId) {
			$notice = $doc->getEntity($noticeId);
			$fields = $doc->getChildren($noticeId);
	
			$model = new Notifications_Notification();
			$model->refModule = 'users';
			$model->refModel = 'users_user';
			$model->refUID = $row['user'];
			$model->createdOn = '2010-08-23 17:19:11';
			$model->createdBy = $row['user'];
			$model->editedOn = '2010-08-23 17:19:11';
			$model->editedBy = $row['user'];

			foreach($fields as $fieldId) {
				$field = $doc->getEntity($fieldId);
				$value = base64_decode($field['value']);
				switch($field['type']) {
					case 'title':		$model->title = strip_tags($value);		break;
					case 'content':		$model->content = strip_tags($value, '<b><i><a><div><p><small>');	break;
					case 'url':			$model->refUrl = $value;	break;
					case 'UID':			$model->UID = $value;		break;
					case 'timestamp':	
							$model->createdOn = $kapenta->db->datetime($value);
							$model->editedOn = $kapenta->db->datetime($value);		
							break;

				}

				echo "<b>" . $field['type'] . ":</b> " . htmlentities(base64_decode($field['value'])) . "<br/>\n";
			}

			$result = $model->save();
			if ('' == $result) {
				echo "...saved (" . $model->UID . ")<br/>\n";
				$notifications->addUser($model->UID, $model->refUID);

			} else { echo "...NOT saved (" . $model->UID . ")"; }


			echo "<br/><hr/>\n";
		}

	}

?>
