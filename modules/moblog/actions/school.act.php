<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display all blog posts from a particular school
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	which school blog to show?
	//----------------------------------------------------------------------------------------------

	$model = new Schools_School();
	if (false == $kapenta->user->authHas('moblog', 'moblog_post', 'show')) { $kapenta->page->do403(); }

	if ('' != $kapenta->request->ref) {
		$model->load($kapenta->request->ref);
		if (false  == $model->loaded) { $kapenta->page->do404(); }

	} else { $model->load($kapenta->user->school); }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/moblog/actions/school.page.php');
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['schoolUID'] = $model->UID;
	$kapenta->page->blockArgs['schoolName'] = $model->name;
	$kapenta->page->blockArgs['schoolRa'] = $model->alias;
	$kapenta->page->allowBlockArgs('page,tag');
	$kapenta->page->render();

?>
