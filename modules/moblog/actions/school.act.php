<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display all blog posts from a particular school
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	which school blog to show?
	//----------------------------------------------------------------------------------------------

	$model = new Schools_School();
	if (false == $user->authHas('moblog', 'moblog_post', 'show')) { $page->do403(); }

	if ('' != $req->ref) {
		$model->load($req->ref);
		if (false  == $model->loaded) { $page->do404(); }

	} else { $model->load($user->school); }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/moblog/actions/school.page.php');
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['schoolUID'] = $model->UID;
	$page->blockArgs['schoolName'] = $model->name;
	$page->blockArgs['schoolRa'] = $model->alias;
	$page->allowBlockArgs('page,tag');
	$page->render();

?>
