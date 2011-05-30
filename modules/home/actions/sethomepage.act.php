<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	set the home / default / front page
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403('Only admins can perform this action.'); }

	if (false == array_key_exists('homepage', $_POST)) { $page->do404('No homepage set.'); }

	//----------------------------------------------------------------------------------------------
	//	set registry value
	//----------------------------------------------------------------------------------------------

	$model = new Home_Static($_POST['homepage']);
	if (false == $model->loaded) { 
		$session->msg('Could not load / set new home page.', 'bad'); 
	} else { 
		$registry->set('home.frontpage', $model->UID);
		$session->msg('Homepage changed to: ' . $model->title);
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to admin console
	//----------------------------------------------------------------------------------------------

	$page->do302('admin/');

?>
