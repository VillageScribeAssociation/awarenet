<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	
//--------------------------------------------------------------------------------------------------
//*	development scratch space / test the projects model
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	administrators only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	load test data
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project($req->ref);
	//if (false == $model->selectionsLoaded) { $page->do404('could not load project sections', true); }

	foreach($model->sections as $idx => $section) {
		echo $idx . " -> " . $section['title'] . "(UID: " . $section['UID'] . " weight: " . $section['weight'] . ")<br/>\n";
		echo "<textarea rows='10' cols='80'>" . $section['content'] . "</textarea><br/><br/>\n";
	}

	$conditions = array("UID='" . $model->UID . "'");
	$range = $db->loadRange('projects', '*', $conditions);	
	foreach($range as $row) {
		echo "<textarea rows='40' cols='80'>" . $row['content'] . "</textarea>\n";
	}

?>
