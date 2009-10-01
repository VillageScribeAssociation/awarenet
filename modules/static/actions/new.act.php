<?

//--------------------------------------------------------------------------------------------------------------
//	create a new static page
//--------------------------------------------------------------------------------------------------------------

	if (authHas('static', 'create', '') == false) { do403(); }
	
	require_once($installPath . 'modules/static/models/static.mod.php');
	$model = new StaticPage();
	$model->data['title'] = 'New Page ' . $model->data['UID'];
	$model->data['menu1'] = '[[:home::menu:]]';
	$model->save();
	
	do302('static/edit/' . $model->data['recordAlias']);

?>