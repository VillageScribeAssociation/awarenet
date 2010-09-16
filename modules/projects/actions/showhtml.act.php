<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//	show a project record (simple HTML, no page template)
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	TODO: check permissions here
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('Projects_Project');

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project($req->ref);

	echo "<html>"
		 . "<link href='" . $serverPath . "/themes/clockface/css/clockface.css' rel='stylesheet' type='text/css' />"
		 . "<title>" . $model->title . "</title><body>"
		 . $model->getSimpleHtml() . "</body></html>";

?>
