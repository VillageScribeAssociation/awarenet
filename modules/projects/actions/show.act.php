<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show a project
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	check references and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('Projects_Project');

	//if (false == $user->authHas('projects', 'Projects_Project', 'edit', $UID)) 
	//	{ $page->do403('You are not authorized to edit this project.'); }

	//----------------------------------------------------------------------------------------------
	//	load the model and determine if the current user can edit it
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project($req->ref);
	$members = $model->getMembers();

	$editUrl = '';
	$delUrl = '';

	// only members and admins can edit projects
	if (true == $model->hasEditAuth($user->UID)) 
		{ $editUrl = $serverPath . 'projects/editabstract/' . $model->alias; }

	// only admins can delete projects
	if ('admin' == $user->role) { 
		$ext = $model->extArray();
		$delUrl = $ext['delUrl'];
	}

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------	
	$page->load('modules/projects/actions/show.page.php');
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['projectTitle'] = $model->title;
	$page->blockArgs['projectRa'] = $model->alias;
	$page->blockArgs['editProjectUrl'] = $editUrl;	// TODO: clunky, fix this
	$page->blockArgs['delProjectUrl'] = $delUrl;
	$page->render();

?>
