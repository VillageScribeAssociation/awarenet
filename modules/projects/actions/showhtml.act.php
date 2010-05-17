<?

//--------------------------------------------------------------------------------------------------
//	show a project record (simple HTML, no page template)
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	TODO: check permissions here
	//----------------------------------------------------------------------------------------------
	if ($request['ref'] == '') { do404(); }
	raFindRedirect('projects', 'showsimplehtml', 'projects', $request['ref']);
	require_once($installPath . 'modules/projects/models/project.mod.php');

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------
	$model = new Project($request['ref']);

	echo "<html>"
		 . "<link href='" . $serverPath . "/themes/clockface/css/clockface.css' rel='stylesheet' type='text/css' />"
		 . "<title>" . $model->data['title'] . "</title><body>"
		 . $model->getSimpleHtml() . "</body></html>";

?>
