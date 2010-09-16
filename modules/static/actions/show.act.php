<?

	require_once($kapenta->installPath . 'modules/static/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a static page
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $req->ref == 'frontpage'; }
	$UID = $aliases->findRedirect('Home_Static');
	$model = new StaticPage($req->ref);
	//permissions check suspended, anyone can view static pages
	//if (false == $user->authHas('home', 'Home_Static', 'show', $UID)) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------	
	$page->template = 'twocol-rightnav.template.php';		//TODO: make this a setting
	$page->content = $model->content;
	$page->nav1 = $model->nav1;
	$page->nav2 = $model->nav2;
	$page->menu1 = $model->menu1;
	$page->menu2 = $model->menu2;
	$page->title = $model->title;
	$page->script = $model->script;
	
	if ('admin' == $user->role) { 
		$page->content .= "<br/><a href='/static/edit/" . $model->alias
					. "'>[edit static page]</a><br/>\n";
	}
	
	$page->blockArgs['staticTitle'] = $model->title;
	$page->render();
	
?>
