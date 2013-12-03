<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a static page, given its UID or an alias as reference
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check references and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->request->ref = 'frontpage'; }
	$UID = $aliases->findRedirect('home_static');
	$model = new Home_Static($UID);
	if (false == $model->loaded) {
		if ('frontpage' == $kapenta->request->ref) { $page->do404('no front page'); }
		else { $page->do404('page not found'); }
	}

	if (false == $user->authHas('home', 'home_static', 'show', $UID)) { $page->do403();}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->template = 'twocol-rightnav.template.php';
	$page->content = $model->content;
	$page->nav1 = $model->nav1;
	$page->nav2 = $model->nav2;
	$page->menu1 = $model->menu1;
	$page->menu2 = $model->menu2;
	$page->title = $model->title;
	$page->script = $model->script;
	$page->breadcrumb = $model->breadcrumb;

	if ('admin' == $user->role) { 
		$page->content .= "<br/><a href='/home/edit/" . $model->alias
					. "'>[edit static page]</a><br/>\n";
	}
	
	$kapenta->page->blockArgs['staticTitle'] = $model->title;
	$kapenta->page->render();
	
?>
