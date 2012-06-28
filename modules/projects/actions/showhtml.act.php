<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	development/debugging method show a project (simple HTML, no page template)
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('projects_project');

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project($req->ref);

	$cssUrl = $kapenta->serverPath . "/home/css/default.css";

	echo "<html>"
		 . "<link href='" . $cssUrl . "' rel='stylesheet' type='text/css' />"
		 . "<title>" . $model->title . "</title><body>"
		 . $model->getSimpleHtml() . "</body></html>";

?>
