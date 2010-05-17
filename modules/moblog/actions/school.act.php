<?

//--------------------------------------------------------------------------------------------------
//	display all blog posts from a particular school
//--------------------------------------------------------------------------------------------------

	if (authHas('moblog', 'view', '') == false) { do403(); }

	//----------------------------------------------------------------------------------------------
	//	which school blog to show?
	//----------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/schools/models/school.mod.php');
	$model = new School();

	if ($request['ref'] != '') {
		if ($model->load($request['ref']) == false) { do404(); }
	} else {
		$model->load($user->data['school']);
	}

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/moblog/actions/school.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['schoolUID'] = $model->data['UID'];
	$page->blockArgs['schoolName'] = $model->data['name'];
	$page->blockArgs['schoolRa'] = $model->data['recordAlias'];
	$page->allowBlockArgs('page,tag');
	$page->render();

?>
