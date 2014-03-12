<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	make default home page (temporary development script)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	make the page and redirect to it
	//----------------------------------------------------------------------------------------------
	$model = new Home_Static();
	$model->template = 'twocol-rightnav.template.php';
	$model->title = 'frontpage';
	$model->content = "<h1>Welcome</h1><p>Thanks for installing Kapenta.</p>";
	$model->save();

	$kapenta->page->do302('home/' . $model->alias);

?>
