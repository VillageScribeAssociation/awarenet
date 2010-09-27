<?

//-------------------------------------------------------------------------------------------------
//	change 'relationship' field of a friendship, eg: boyfriend -> spouse
//-------------------------------------------------------------------------------------------------

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');

	//---------------------------------------------------------------------------------------------
	//	load the record and make sure the correct user is changing it
	//---------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $_POST)) { $page->do404(); }
	if (false == $db->objectExists('friendships', $_POST['UID'])) { $page->do404(); }

	$model = new Users_Friendship($_POST['UID']);
	if ($model->userUID != $user->UID) { $page->do403(); }

	//---------------------------------------------------------------------------------------------
	//	change it
	//---------------------------------------------------------------------------------------------
	if (false == array_key_exists('relationship', $_POST)) { $page->doXmlError('no relationship'); }
	$model->relationship = $_POST['relationship'];
	$model->save();

	//---------------------------------------------------------------------------------------------
	//	notify the other party
	//---------------------------------------------------------------------------------------------
	$title = $user->getName() . " now lists you as their " . $model->relationship . ".";
	$content = $user->getName() . " changed your relationship on their friends list.";
	$url = '/users/friends/' . $user->alias;
	$fromUrl = '/users/profile/' . $user->UID;

	$imgRow = imgGetDefault('users', $user->UID);
	$imgUID = '';
	if (false != $imgRow) { $imgUID = $imgRow['UID']; }

	//TODO: replace this notification function
	notifyUser(	$model->friendUID, $kapenta->createUID(), $user->getName(), 
				$fromUrl, $title, $content, $url, $imgUID );

	//---------------------------------------------------------------------------------------------
	//	redirect back to friends list
	//---------------------------------------------------------------------------------------------
	
	$page->do302('users/friends/' . $user->alias);

?>
