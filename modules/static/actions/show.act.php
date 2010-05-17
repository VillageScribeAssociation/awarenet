<?

//--------------------------------------------------------------------------------------------------------------
//	display a static page
//--------------------------------------------------------------------------------------------------------------

	//if (authHas('static', 'view', '') == false) { do403(); }

	if ($request['ref'] == '') { $request['ref'] == 'frontpage'; }

	raFindRedirect('static', 'show', 'static', $request['ref']);
	
	require_once($installPath . 'modules/static/models/static.mod.php');
	$model = new StaticPage($request['ref']);
	
	$page->data['template'] = 'twocol-rightnav.template.php';
	$page->data['content'] = $model->data['content'];
	$page->data['nav1'] = $model->data['nav1'];
	$page->data['nav2'] = $model->data['nav2'];
	$page->data['menu1'] = $model->data['menu1'];
	$page->data['menu2'] = $model->data['menu2'];
	$page->data['title'] = 'awareNet - ' . $model->data['title'];
	$page->data['script'] = $model->data['script'];
	
	if ($user->data['ofGroup'] == 'admin') { 
		$page->data['content'] .= "<br/><a href='/static/edit/" . $model->data['recordAlias']
					. "'>[edit static page]</a><br/>\n";
	}
	
	$page->blockArgs['staticTitle'] = $model->data['title'];
	$page->render();
	
?>
