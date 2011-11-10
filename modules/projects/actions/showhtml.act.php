<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//	show a project record (simple HTML, no page template)
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	TODO: check permissions here
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('projects_project');

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project($req->ref);

	$cssUrl = $kapenta->serverPath . "/themes/clockface/css/default.css";

	echo "<html>"
		 . "<link href='" . $cssUrl . "' rel='stylesheet' type='text/css' />"
		 . "<title>" . $model->title . "</title><body>"
		 . $model->getSimpleHtml() . "</body></html>";

?>
