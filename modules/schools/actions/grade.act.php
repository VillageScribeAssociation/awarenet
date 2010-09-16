<?

		require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show all users in a given year for a given school
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('grade', $req->args)) { $page->do404('grade not given'); }
	if ('' == $req->ref) { $page->do404(); }

	$model = new Schools_School($req->ref);
	//TODO: permission check here
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/schools/actions/grade.page.php');
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['schoolUID'] = $model->UID;
	$page->blockArgs['schoolName'] = $model->name;
	$page->blockArgs['schoolDescription'] = $model->description;
	$page->blockArgs['grade'] = base64_decode($req->args['grade']);

	//TODO: sanitize $req->args['grade']
	$page->blockArgs['gradeLink'] = "grade_" . $req->args['grade'] . '/' . $model->alias;
	$page->render();

?>
