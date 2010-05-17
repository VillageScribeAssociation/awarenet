<?

//--------------------------------------------------------------------------------------------------
//	show a group record
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	authentication (no public users)
	//----------------------------------------------------------------------------------------------
	if (($user->data['ofGroup'] == 'public') || ($user->data['ofGroup'] == 'banned')) { do403(); }

	//----------------------------------------------------------------------------------------------	
	//	check reference
	//----------------------------------------------------------------------------------------------
	if ($request['ref'] == '') { do404(); }
	raFindRedirect('groups', 'show', 'groups', $request['ref']);

	//----------------------------------------------------------------------------------------------	
	//	load the model
	//----------------------------------------------------------------------------------------------	
	require_once($installPath . 'modules/groups/models/group.mod.php');
	$model = new Group($request['ref']);

	if ($model->hasEditAuth($user->data['UID']) == true) 
		{ $editUrl = $serverPath . 'groups/edit/' . $model->data['recordAlias']; }

	//----------------------------------------------------------------------------------------------	
	//	render the page
	//----------------------------------------------------------------------------------------------	
	$page->load($installPath . 'modules/groups/actions/show.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['UID'] = $model->data['UID'];
	$page->blockArgs['groupName'] = $model->data['name'];
	$page->blockArgs['editGroupUrl'] = $editUrl;
	$page->render();

?>
