<?

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display something from the code table (folder, file, etc)
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ($kapenta->request->ref == '') { $page->do404(); }
	
	$model = new Code_File($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Unkown item.'); }
	//TODO: permission check here

	//----------------------------------------------------------------------------------------------
	//	decide which type of object it is
	//----------------------------------------------------------------------------------------------

	$pageName = 'showphp.page.php';

	switch ($model->type) {
		case 'folder':				$pageName = 'showfolder.page.php';		break;
		case 'kapenta/action':		$pageName = 'showphp.page.php';			break;
		case 'kapenta/include':		$pageName = 'showphp.page.php';			break;
		case 'kapenta/template':	$pageName = 'showphp.page.php';			break;
		case 'page':				$pageName = 'showpage.page.php';		break;
		case 'block':				$pageName = 'showblock.page.php';		break;
		case 'txt':					$pageName = 'showtxt.page.php';			break;
		case 'text/php':			$pageName = 'showphp.page.php';			break;
		case 'xml':					$pageName = 'showtxt.page.php';			break;
		case 'template':			$pageName = 'showtemplate.page.php';	break;
		case 'jpeg':				$pageName = 'showtxt.page.php';			break;
		case 'png':					$pageName = 'showtxt.page.php';			break;
		case 'gif':					$pageName = 'showtxt.page.php';			break;
		case 'ttf':					$pageName = 'showtxt.page.php';			break;

		default: $page->do404('Unkown object type: ' . $model->type); 	break;

	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/code/actions/' . $pageName);
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->render();

?>
