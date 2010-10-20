<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//	save submitted changes, redirect to /home/Saved-Page
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('action not specified'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not given'); }
	if ('saveStaticPage' != $_POST['action']) { $page->do404('action not supported'); }

	$model = new Home_Static($_POST['UID']);

	if (false == $model->loaded) { $page->do404(); }
	if (false == $user->authHas('home', 'Home_Static', 'edit', $_POST['UID'])) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	save changes to static page
	//----------------------------------------------------------------------------------------------
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'title':		$model->title = $utils->cleanString($value); break;
			case 'template':	$model->template = $utils->cleanString($value); break;
			case 'content':		$model->content = $value; break;
			case 'nav1':		$model->nav1 = $value; break;
			case 'nav2':		$model->nav2 = $value; break;
			case 'script':		$model->script = $value; break;
			case 'jsinit':		$model->jsinit = $value; break;
			case 'banner':		$model->banner = $utils->cleanString($value); break;
			case 'menu1':		$model->menu1 = $value; break;
			case 'menu2':		$model->menu2 = $value; break;
			case 'breadcrumb':	$model->breadcrumb = $value; break;
			case 'section':		$model->section = $value; break;
			case 'subsection':	$model->subsection = $value; break;
		}
	}

	$report = $model->save();
	if ('' == $report) { $session->msg('Saved changes to ' . $model->alias . '<br/>', 'ok'); }
	else { $session->msg('Could not save Static:<br/>' . $report); }

	//----------------------------------------------------------------------------------------------
	//	redirect back to edited page
	//----------------------------------------------------------------------------------------------
	$page->do302('home/' . $model->alias);
		

?>
