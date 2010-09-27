<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show all users in the year and grade matching those of a given user
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $req->ref = $user->UID; }
	
	$model = new Users_User($req->ref);
	if (false == $model->loaded) { $page->do404(); }
	//TODO: permissions check here (same as profile?)

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/users/actions/grade.page.php');
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['schoolUID'] = $model->school;
	$page->blockArgs['schoolName'] = '[[:schools::name::schoolUID=' . $model->school . ':]]';
	$page->blockArgs['grade'] = $model->grade;
	$page->blockArgs['userRa'] = $model->alias;
	$page->blockArgs['userUID'] = $model->UID;
	$page->blockArgs['userName'] = $model->getName();

	$page->menu2 = "[[:users::menu::userUID=" . $model->UID . ":]]";
	$page->title = 'awareNet - ' . $model->getName() . ' (classmates )';

	$page->render();

?>
