<?

//--------------------------------------------------------------------------------------------------------------
//	create a new static page
//--------------------------------------------------------------------------------------------------------------

	if ($user->authHas('home', 'Home_Static', 'create', 'TODO:UIDHERE') == false) { $kapenta->page->do403(); }
	
	require_once($kapenta->installPath . 'modules/static/models/static.mod.php');
	$model = new StaticPage();
	$model->title = 'New Page ' . $model->UID;
	$model->data['menu1'] = '[[:home::menu:]]';
	$model->save();
	
	$kapenta->page->do302('static/edit/' . $model->alias);

?>