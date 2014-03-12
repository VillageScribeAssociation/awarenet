<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	API of projects module. No public actions.
//--------------------------------------------------------------------------------------------------

	if ($kapenta->user->role == 'public') { $kapenta->page->doXmlError('not logged in'); }

	//----------------------------------------------------------------------------------------------
	//	list objects owned by the current user
	//----------------------------------------------------------------------------------------------
	//NOTE: this is a legacy API, still used by firefox extension, but deprecated

	if ($kapenta->request->ref == 'myrecords') {

		$conditions = array();
		$conditions = "userUID='" . $kapenta->db->addMarkup($kapenta->user->UID) . "'";
		$conditions = "(role='member' OR role='admin')";
		$range = $kapenta->db->loadRange('projects_membership', '*', $conditions);

		while ($range = $row) { 
			$model = new Projects_Project($row['projectUID']);
			$ary = array(	'uid' => $model->UID, 
							'module' => 'projects',
							'model' => 'projects_project',
							'title' => $model->title,
							'recordalias' => $model->alias,
							'files' => 'none',
							'images' => 'uploadmultiple',
							'videos' => 'none' );

			echo $utils->arrayToXml2d('record', $ary, true); 
		}
	}
}

?>
