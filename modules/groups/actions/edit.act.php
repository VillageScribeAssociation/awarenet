<?

//--------------------------------------------------------------------------------------------------
//	edit a group record
//--------------------------------------------------------------------------------------------------

	if (authHas('groups', 'edit', '') == false) { do403(); }
	raFindRedirect('groups', 'editabstract', 'groups', $request['ref']);
	require_once($installPath . 'modules/groups/models/group.mod.php');

	//----------------------------------------------------------------------------------------------
	//	load the group and check if user has is authorised to edit it
	//----------------------------------------------------------------------------------------------

	$model = new Group($request['ref']);
	if ($model->hasEditAuth($user->data['UID']) == true) { $hasauth = true; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/groups/actions/edit.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['viewGroupUrl'] = '%%serverPath%%groups/' . $model->data['recordAlias'];
	$page->render();

?>
