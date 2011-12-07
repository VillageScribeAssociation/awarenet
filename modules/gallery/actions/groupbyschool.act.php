<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');
	
//--------------------------------------------------------------------------------------------------
//*	temporary development / admin / upgrade action to reset the schoolUID field on all galleries
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------

	$sql = "select * from gallery_gallery";
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$item = $db->rmArray($row);		
		$creator = $db->getObject('users_user', $item['createdBy']);

		if ($creator['school'] != $item['schoolUID']) {
			$model = new Gallery_Gallery($item['UID']);
			$model->schoolUID = $creator['school'];
			$check = $model->save();
			if (false == $check) {
				$session->msg("Reset school for gallery: " . $item['title'], 'ok');
			} else {
				$session->msg("Could not set school for gallery: " . $item['title'], 'bad');
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to admin console
	//----------------------------------------------------------------------------------------------
	$page->do302('admin/');

?>
