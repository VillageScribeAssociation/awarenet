<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a group record
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	$UID = $aliases->findRedirect('Groups_Group');
	if (false == $user->authHas('groups', 'Groups_Group', 'edit', $UID)) { $page->do403(); }

	$model = new Groups_Group($UID);
	if (true == $model->hasEditAuth($user->UID)) { $hasauth = true; }
	if (false == $hasauth) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/groups/actions/edit.page.php');
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['viewGroupUrl'] = '%%serverPath%%groups/' . $model->alias;
	$page->render();

?>
