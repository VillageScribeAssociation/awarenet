<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/models/images.set.php');

//--------------------------------------------------------------------------------------------------
//*	sets a specified image as the current user's profile picture
//--------------------------------------------------------------------------------------------------
//ref: alias or UID of an Images_Image object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }
	if ('' == $kapenta->request->ref) { $page->do404('Image not specified.'); }

	$model = new Images_image($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Unkown image.'); }

	if (false == $kapenta->fs->exists($model->fileName)) {
		$msg = 'Image not available on this node, you can not set it as a profile picture.';
		$session->msg($msg, 'bad');
		$page->do302('users/editprofile/' . $user->alias);
	}

	//----------------------------------------------------------------------------------------------
	//	copy the file and make a new Images_Image object belonging to the user
	//----------------------------------------------------------------------------------------------
	$UID = $kapenta->createUID();
	$model->UID = $UID;
	$model->refModule = 'users';
	$model->refModel = 'users_user';
	$model->refUID = $user->UID;
	
	$newFile = ''
	 . 'data/images/'
	 . substr($UID, 0, 1) . '/' . substr($UID, 1, 1) . '/' . substr($UID, 2, 1) . '/'
	 . $UID . '.jpg';

	$kapenta->fs->put($newFile, '');

	$check = copy($kapenta->installPath . $model->fileName, $kapenta->installPath . $newFile);

	if (false == $check) {
		$msg = 'Unable to copy image to your profile.';
		$session->msg($msg, 'bad');
		$page->do302('users/editprofile/' . $user->alias);
	}

	$model->fileName = $newFile;
	$report = $model->save();

	if ('' == $report) {
		$session->msg('Added new image to your profile pictures: ' . $model->title, 'ok');
	} else {
		$session->msg('Error while copying: ' . $report, 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	set image weights such that the image is the default
	//----------------------------------------------------------------------------------------------

	$imgset = new Images_Images('users', 'users_user', $user->UID);
	$check = $imgset->setDefault($model->UID);

	if (true == $check) { $session->msg('Set as current profile picture.', 'ok'); }
	else { $session->msg('Could not set as current profile picture.', 'bad'); }

	//----------------------------------------------------------------------------------------------
	//	redirect to edit profile form
	//----------------------------------------------------------------------------------------------
	$page->do302('users/editprofile/' . $user->alias);

?>
