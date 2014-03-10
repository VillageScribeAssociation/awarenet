<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show all users in the year and grade matching those of a given user
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->request->ref = $user->UID; }
	
	$model = new Users_User($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404(); }
	//TODO: permissions check here (same as profile?)

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/users/actions/grade.page.php');
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['schoolUID'] = $model->school;
	$kapenta->page->blockArgs['schoolName'] = '[[:schools::name::schoolUID=' . $model->school . ':]]';
	$kapenta->page->blockArgs['grade'] = $model->grade;
	$kapenta->page->blockArgs['userRa'] = $model->alias;
	$kapenta->page->blockArgs['userUID'] = $model->UID;
	$kapenta->page->blockArgs['userName'] = $model->getName();

	$kapenta->page->menu2 = "[[:users::menu::userUID=" . $model->UID . ":]]";
	$kapenta->page->title = 'awareNet - ' . $model->getName() . ' (classmates )';

	$kapenta->page->render();

?>
