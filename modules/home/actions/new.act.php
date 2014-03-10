<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------------------
//*	create a new static page
//--------------------------------------------------------------------------------------------------------------

	if (false == $user->authHas('home', 'home_static', 'new')) { $kapenta->page->do403(); }
	
	$model = new Home_Static();
	$model->menu1 = '[[:home::menu:]]';
	$model->save();
	
	$kapenta->page->do302('home/edit/' . $model->alias);

?>
