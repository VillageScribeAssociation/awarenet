<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a static page, given its UID or an alias as reference
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check references and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->request->ref = 'frontpage'; }
	$UID = $kapenta->aliases->findRedirect('home_static');
	$model = new Home_Static($UID);
	if (false == $model->loaded) {
		if ('frontpage' == $kapenta->request->ref) { $kapenta->page->do404('no front page'); }
		else { $kapenta->page->do404('page not found'); }
	}

	if (false == $kapenta->user->authHas('home', 'home_static', 'show', $UID)) { $kapenta->page->do403();}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->template = 'twocol-rightnav.template.php';
	$kapenta->page->content = $model->content;
	$kapenta->page->nav1 = $model->nav1;
	$kapenta->page->nav2 = $model->nav2;
	$kapenta->page->menu1 = $model->menu1;
	$kapenta->page->menu2 = $model->menu2;
	$kapenta->page->title = $model->title;
	$kapenta->page->script = $model->script;
	$kapenta->page->breadcrumb = $model->breadcrumb;

	if ('admin' == $kapenta->user->role) { 
		$kapenta->page->content .= "<br/><a href='/home/edit/" . $model->alias
					. "'>[edit static page]</a><br/>\n";
	}
	
	$kapenta->page->blockArgs['staticTitle'] = $model->title;
	$kapenta->page->render();
	
?>
