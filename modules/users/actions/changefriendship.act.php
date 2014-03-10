<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');

//-------------------------------------------------------------------------------------------------
//*	change 'relationship' field of a friendship, eg: boyfriend -> spouse
//-------------------------------------------------------------------------------------------------
//TODO: consider moving this functionality to friendships.set.php

	//---------------------------------------------------------------------------------------------
	//	load the record and make sure the correct user is changing it
	//---------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('Friendship UID not given.'); }
	if (false == array_key_exists('relationship', $_POST)) { $kapenta->page->do404('no relationship'); }

	$model = new Users_Friendship($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Relationship not found.'); }
	if ($model->userUID != $user->UID) { $kapenta->page->do403(); }

	$relationship = $utils->cleanTitle($_POST['relationship']);

	//---------------------------------------------------------------------------------------------
	//	change it
	//---------------------------------------------------------------------------------------------

	$model->relationship = $relationship;
	$report = $model->save();

	if ('' == $report) { $session->msg('Relationship changed to: ' . $relationship, 'ok'); }
	else { $session->msg('Could not change relationship:<br/>' . $report, 'bad'); }

	//---------------------------------------------------------------------------------------------
	//	notify the other party
	//---------------------------------------------------------------------------------------------
	$ext = $model->extArray();

	$friendNameBlock = '[[:users::name::userUID=' . $model->friendUID . ':]]';
	$friendName = $theme->expandBlocks($friendNameBlock, '');

	$listUrl = '%%serverPath%%users/friends/' . $user->alias;
	$listLink = "<a href='$listUrl'>friends list</a>";

	$title = $user->getName() . " now lists $friendName as their " . $model->relationship . ".";
	$content = $user->getNameLink() . " changed a relationship on their $listLink.";
	$url = '%%serverPath%%users/friends/' . $user->alias;

	$nUID = $notifications->create(
		'users', 'users_user', $user->UID, 'friendship_changed', 
		$title, $content, $url
	);

	$notifications->addUser($nUID, $user->UID);
	$notifications->addFriends($nUID, $user->UID);
	$notifications->addUser($nUID, $model->friendUID);
	$notifications->addFriends($nUID, $user->UID);

	//---------------------------------------------------------------------------------------------
	//	redirect back to friends list
	//---------------------------------------------------------------------------------------------	
	$kapenta->page->do302('users/friends/' . $user->alias);

?>
