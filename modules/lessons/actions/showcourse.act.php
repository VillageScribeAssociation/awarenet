<?phpLe

	require_once($kapenta->installPath . 'modules/lessons/models/collection.mod.php');
	require_once($kapenta->installPath . 'modules/lessons/models/stub.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a single course
//--------------------------------------------------------------------------------------------------

	if ('public' == $kapenta->user->role) { $kapenta->page->do302('Please log in to access this course.'); }

	$model = new 
	//TODO:

	$kapenta->page->load('modules/lessons/actions/showcourse.act.php');
	$kapenta->page->blockArgs['UID'] = $kapenta->request->ref;
	$kapenta->page->render();

?>
