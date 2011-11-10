<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//*	temporary administrative action to fix references to gallery images
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	change image comments to point to image and not gallery
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "refModule='gallery'";

	$range = $db->loadRange('comments_comment', '*', $conditions);

	echo "<h2>Searching for broken references to gallery images</h2>";

	foreach($range as $row) {
		if (true == $db->objectExists('images_image', $row['refUID'])) {
			$model = new Comments_Comment($row['UID']);
			$model->refModule = 'images';
			$model->refModel = 'images_image';
			$model->save();

			echo ""
			 . "<b>correcting comment " . $row['UID'] . " on images_image::"
			 . $row['refUID'] . "</b><br/>\n"
			 . "<p>" . $model->comment . "<br/><small>"
			 . $row['refModule'] . '::' . $row['refModel'] . '::' . $row['refUID']
			 . "</small></p><br/>\n";

		}
	}

?>
