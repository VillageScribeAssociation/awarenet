<?

		require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show all users in a given year for a given school
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('grade', $kapenta->request->args)) { $page->do404('grade not given'); }
	if ('' == $kapenta->request->ref) { $page->do404(); }

	$model = new Schools_School($kapenta->request->ref);
	//TODO: permission check here
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/schools/actions/grade.page.php');
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['schoolUID'] = $model->UID;
	$kapenta->page->blockArgs['schoolRa'] = $model->alias;
	$kapenta->page->blockArgs['schoolName'] = $model->name;
	$kapenta->page->blockArgs['schoolDescription'] = $model->description;
	$kapenta->page->blockArgs['grade'] = base64_decode($kapenta->request->args['grade']);

	//TODO: sanitize $kapenta->request->args['grade']
	$kapenta->page->blockArgs['gradeLink'] = "grade_" . $kapenta->request->args['grade'] . '/' . $model->alias;
	$kapenta->page->render();

?>
