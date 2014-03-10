<?

	require_once($kapenta->installPath . 'modules/static/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a static page
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->request->ref == 'frontpage'; }
	$UID = $aliases->findRedirect('Home_Static');
	$model = new StaticPage($kapenta->request->ref);
	//permissions check suspended, anyone can view static pages
	//if (false == $user->authHas('home', 'Home_Static', 'show', $UID)) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------	
	$page->template = 'twocol-rightnav.template.php';		//TODO: make this a setting
	$kapenta->page->content = $model->content;
	$page->nav1 = $model->nav1;
	$page->nav2 = $model->nav2;
	$page->menu1 = $model->menu1;
	$page->menu2 = $model->menu2;
	$kapenta->page->title = $model->title;
	$page->script = $model->script;
	
	if ('admin' == $user->role) { 
		$kapenta->page->content .= "<br/><a href='/static/edit/" . $model->alias
					. "'>[edit static page]</a><br/>\n";
	}
	
	$kapenta->page->blockArgs['staticTitle'] = $model->title;
	$kapenta->page->render();
	
?>
