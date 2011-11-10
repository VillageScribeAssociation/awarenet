<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');

//--------------------------------------------------------------------------------------------------
//*	scan through projects_revision table and convert to projects_change objects
//--------------------------------------------------------------------------------------------------

	$table = 'projects_revisionbakup';

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	$changes = array();

	//----------------------------------------------------------------------------------------------
	//	load projects one at a time and convert all revisions
	//----------------------------------------------------------------------------------------------
	$sql = "select * from projects_project";
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$project = new Projects_Project($row['UID']);

		$conditions = array("projectUID='" . $project->UID . "'");
		$range = $db->loadRange($table, '*', $conditions, 'createdOn ASC');
		echo "project: {$project->title} ({$project->UID}) has " . count($range) . " revisions.<br/>";

		$lastTitle = '';						//%	comparison
		$lastAbstract = '';						//%	comparison
		$lastSections = array();				//%	comparison

		//------------------------------------------------------------------------------------------
		//	check each revision against the last for specific changes
		//------------------------------------------------------------------------------------------
		foreach($range as $item) {
			$revision = new Projects_Revision();
			$revision->loadArray($item);

			//--------------------------------------------------------------------------------------
			//	find changes in project title and abstract
			//--------------------------------------------------------------------------------------
			if ($revision->title != $lastTitle) {
				$changes[] = array(
					'userUID' => $revision->createdBy,
					'projectUID' => $project->UID,
					'sectionUID' => '*',
					'changed' => 'p.title',
					'message' => 'Changed project title:',
					'value' => $revision->title
				);
			}


			if ($revision->abstract != $lastAbstract) {
				$changes[] = array(
					'userUID' => $revision->createdBy,
					'projectUID' => $project->UID,
					'sectionUID' => '*',
					'changed' => 'p.abstract',
					'message' => 'Changed project abstract:',
					'value' => $revision->abstract
				);
			}
		
			//--------------------------------------------------------------------------------------
			//	look for new sections
			//--------------------------------------------------------------------------------------
			foreach($revision->sections as $sUID => $section) {
				if (false == array_key_exists($sUID, $lastSections)) {
					$changes[] = array(
						'userUID' => $revision->createdBy,
						'projectUID' => $project->UID,
						'sectionUID' => $sUID,
						'changed' => 's.new',
						'message' => 'Added project section:',
						'value' => $sUID
					);

					$changes[] = array(
						'userUID' => $revision->createdBy,
						'projectUID' => $project->UID,
						'sectionUID' => $sUID,
						'changed' => 's.title',
						'message' => 'Changed section title:',
						'value' => $section['title']
					);

					$changes[] = array(
						'userUID' => $revision->createdBy,
						'projectUID' => $project->UID,
						'sectionUID' => $sUID,
						'changed' => 's.content',
						'message' => 'Changed section content:',
						'value' => $section['content']
					);
				}
			}

			//--------------------------------------------------------------------------------------
			//	look for sections being deleted
			//--------------------------------------------------------------------------------------
			foreach($lastSections as $sUID => $section) {
				if (false == array_key_exists($sUID, $revision->sections)) {
					$changes[] = array(
						'userUID' => $revision->createdBy,
						'projectUID' => $project->UID,
						'sectionUID' => $sUID,
						'changed' => 's.del',
						'message' => 'Removed section:',
						'value' => $sUID
					);
				}
			}

			//--------------------------------------------------------------------------------------
			//	look for changes to sections
			//--------------------------------------------------------------------------------------
			foreach($lastSections as $sUID => $section) {
				if (true == array_key_exists($sUID, $revision->sections)) {
				
					if ($section['title'] != $revision->sections[$sUID]['title']) {
						$changes[] = array(
							'userUID' => $revision->createdBy,
							'projectUID' => $project->UID,
							'sectionUID' => $sUID,
							'changed' => 's.title',
							'message' => 'Changed section title:',
							'value' => $revision->sections[$sUID]['title']
						);
					}

					if ($section['content'] != $revision->sections[$sUID]['content']) {
						$changes[] = array(
							'userUID' => $revision->createdBy,
							'projectUID' => $project->UID,
							'sectionUID' => $sUID,
							'changed' => 's.content',
							'message' => 'Changed section content:',
							'value' => $revision->sections[$sUID]['content']
						);
					}

				}
			}

			//--------------------------------------------------------------------------------------
			//	set comparison values for sext time
			//--------------------------------------------------------------------------------------
			$lastTitle = $revision->title;
			$lastAbstract = $revision->abstract;
			$lastSections = $revision->sections;

		}		

	}

	//----------------------------------------------------------------------------------------------
	//	display changes
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('user', 'project', 'section', 'changed', 'message', 'value');
	foreach($changes as $change) { $table[] = $change; }

	echo $theme->arrayToHtmlTable($table, true, true);

	//----------------------------------------------------------------------------------------------
	//	save changes
	//----------------------------------------------------------------------------------------------


	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]');

?>
