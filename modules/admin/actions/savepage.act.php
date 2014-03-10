<?

//--------------------------------------------------------------------------------------------------
//*	save a page template and redirect to /admin/listpages/
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('savePage' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }

	if (false == array_key_exists('module', $_POST)) { $kapenta->page->do404('Module not specified.'); }
	if (false == array_key_exists('page', $_POST)) { $kapenta->page->do404('Module not specified.'); }
	$fileName = 'modules/' . $_POST['module'] . '/actions/' . $_POST['page'];

	if (false == $kapenta->fs->exists($fileName)) { $kapenta->page->do404('No such template.'); }

	$model = new KPage($fileName);
	if (false == $model->loaded) { $kapenta->page->do404('Could not load template.'); }

	//----------------------------------------------------------------------------------------------
	//	save the changes
	//----------------------------------------------------------------------------------------------

	foreach($_POST as $key => $val) { 
		switch($key) {
			case 'template':	$model->template = $val;		break;
			case 'title':		$model->title = $val;			break;
			case 'content':		$model->content = $val;			break;
			case 'nav1':		$model->nav1 = $val;			break;
			case 'nav2':		$model->nav2 = $val;			break;
			case 'script':		$model->script = $val;			break;
			case 'jsinit':		$model->jsinit = $val;			break;
			case 'banner':		$model->banner = $val;			break;
			case 'head':		$model->head = $val;			break;
			case 'menu1':		$model->menu1 = $val;			break;
			case 'menu2':		$model->menu2 = $val;			break;
			case 'section':		$model->section = $val;			break;
			case 'subsection':	$model->subsection = $val;		break;
			case 'breadcrumb':	$model->breadcrumb = $val;		break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	redirect back to list
	//----------------------------------------------------------------------------------------------
	
	$kapenta->page->do302('admin/listpages/');

?>
