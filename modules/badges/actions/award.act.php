<?

	require_once($kapenta->installPath . 'modules/badges/models/badge.mod.php');

//--------------------------------------------------------------------------------------------------
//*	associate a user with a badge
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST vars
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }		// only admins can do this at present

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified'); }
	if ('awardBadge' != $_POST['action']) { $page->do404('Action not supported.'); }

	if (false == array_key_exists('badgeUID', $_POST)) { $page->do404('badgeUID not supplied.'); }
	if (false == array_key_exists('userUID', $_POST)) { $page->do404('userUID not supplied.'); }

	$model = new Badges_Badge($_POST['badgeUID']);
	if (false == $model->loaded) { $page->do404('Badge not found.'); }

	//----------------------------------------------------------------------------------------------
	//	make the award and redirect back to user profile
	//----------------------------------------------------------------------------------------------

	$report = $model->awardTo($_POST['userUID']);
	if ('' == $report) { 
		$session->msg('Awarded badge.', 'ok'); 
		$page->do302('users/profile/' . $_POST['userUID']);
	} else { 
		$session->msg($report, 'bad'); 
		$page->do302('users/profile/' . $_POST['userUID']);
	}

?>
