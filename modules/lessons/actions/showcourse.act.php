<?phpLe

	require_once($kapenta->installPath . 'modules/lessons/models/collection.mod.php');
	require_once($kapenta->installPath . 'modules/lessons/models/stub.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a single course
//--------------------------------------------------------------------------------------------------

	if ('public' == $user->role) { $page->do302('Please log in to access this course.'); }

	$model = new 
	//TODO:

	$page->load('modules/lessons/actions/showcourse.act.php');
	$kapenta->page->blockArgs['UID'] = $req->ref;
	$page->render();

?>
