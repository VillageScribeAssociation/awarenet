<?

	require_once($kapenta->installPath . 'modules/aliases/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/announcements/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/calendar/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/comments/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/files/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/forums/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/groups/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/home/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/images/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/messages/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/moblog/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/projects/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/schools/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/sync/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/users/inc/install.inc.php');

//-------------------------------------------------------------------------------------------------
//	temporary script to reinstall tables
//-------------------------------------------------------------------------------------------------

	//if ('admin' != $user->role) { $page->do403(); }

	//---------------------------------------------------------------------------------------------
	//	specify which tables not to add fields to
	//---------------------------------------------------------------------------------------------

	$html = '';

	/*
	if (true == $db->tableExists('Aliases_Alias')) { $db->query("drop table Aliases_Alias"); }
	$html .= aliases_install_module();
	$html .= "<br/>\n";

	if (true == $db->tableExists('Announcements_Announcement')) { $db->query("drop table Announcements_Announcement"); }
	$html .= announcements_install_module();
	$html .= "<br/>\n";

	if (true == $db->tableExists('Calendar_Entry')) { $db->query("drop table Calendar_Entry"); }
	$html .= calendar_install_module();
	$html .= "<br/>\n";

	if (true == $db->tableExists('Comments_Comment')) { $db->query("drop table Comments_Comment"); }
	$html .= comments_install_module();
	$html .= "<br/>\n";

	if (true == $db->tableExists('Files_File')) { $db->query("drop table Files_File"); }
	if (true == $db->tableExists('Files_Folder')) { $db->query("drop table Files_Folder"); }
	$html .= files_install_module();
	$html .= "<br/>\n";

	if (true == $db->tableExists('Forums_Board')) { $db->query("drop table Forums_Board"); }
	if (true == $db->tableExists('Forums_Thread')) { $db->query("drop table Forums_Thread"); }
	if (true == $db->tableExists('Forums_Reply')) { $db->query("drop table Forums_Reply"); }
	$html .= forums_install_module();
	$html .= "<br/>\n";

	if (true == $db->tableExists('Groups_Group')) { $db->query("drop table Groups_Group"); }
	if (true == $db->tableExists('Groups_Membership')) { $db->query("drop table Groups_Membership"); }
	$html .= groups_install_module();
	$html .= "<br/>\n";

	if (true == $db->tableExists('Home_Static')) { $db->query("drop table Home_Static"); }
	$html .= home_install_module();
	$html .= "<br/>\n";

	if (true == $db->tableExists('Images_Image')) { $db->query("drop table Images_Image"); }
	$html .= images_install_module();
	$html .= "<br/>\n";

	if (true == $db->tableExists('Messages_Message')) { $db->query("drop table Messages_Message"); }
	$html .= messages_install_module();
	$html .= "<br/>\n";

	if (true == $db->tableExists('Moblog_Post')) { $db->query("drop table Moblog_Post"); }
	$html .= moblog_install_module();
	$html .= "<br/>\n";

	if (true == $db->tableExists('Projects_Project')) { $db->query("drop table Projects_Project"); }
	if (true == $db->tableExists('Projects_Membership')) { $db->query("drop table Projects_Membership"); }
	if (true == $db->tableExists('Projects_Revision')) { $db->query("drop table Projects_Revision"); }
	$html .= projects_install_module();
	$html .= "<br/>\n";

	if (true == $db->tableExists('Schools_School')) { $db->query("drop table Schools_School"); }
	$html .= schools_install_module();
	$html .= "<br/>\n";

	if (true == $db->tableExists('Sync_Server')) { $db->query("drop table Sync_Server"); }
	if (true == $db->tableExists('Sync_Notice')) { $db->query("drop table Sync_Notice"); }
	if (true == $db->tableExists('Sync_Download')) { $db->query("drop table Sync_Download"); }
	$html .= sync_install_module();
	$html .= "<br/>\n";
	
	if (true == $db->tableExists('Users_User')) { $db->query("drop table Users_User"); }
	if (true == $db->tableExists('Users_Role')) { $db->query("drop table Users_Role"); }
	if (true == $db->tableExists('Users_Friendship')) { $db->query("drop table Users_Friendship"); }
	$html .= users_install_module();
	$html .= "<br/>\n";
	*/

	//----------------------------------------------------------------------------------------------
	//	patch up 
	//----------------------------------------------------------------------------------------------
	$rename = array(
		'announcements' => 'Announcements_Announcement',
		'calendar' => 'Calendar_Entry',
		'comments' => 'Comments_Comment',
		'downloads' => 'Sync_Download',
		'files' => 'Files_File',
		'folders' => 'Files_Folder',
		'forums' => 'Forums_Board',
		'forumreplies' => 'Forums_Reply',
		'forumthreads' => 'Forums_Thread',
		'friendships' => 'Users_Friendship',
		'gallery' => 'Gallery_Gallery',
		'groupmembers' => 'Groups_Membership',
		'groups' => 'Groups_Group',
		'images' => 'Images_Image',
		'messages' => 'Messages_Message',
		'moblog' => 'Moblog_Post',
		'projectmembers' => 'Projects_Membership',
		'projectrevisions' => 'Projects_Revision',
		'projects' => 'Projects_Project',
		'recordalias' => 'Aliases_Alias',
		'schools' => 'Schools_School',
		'servers' => 'Sync_Server',
		'sync' => 'Sync_Notice',
		'users' => 'Users_User',
		'wiki' => 'Wiki_Article',
		'wikirevisions' => 'Wiki_Revision'
	);

	$dba = new KDBAdminDriver();

	foreach($rename as $oldname => $newname) {
		if (true == $db->tableExists($newname)) {

			$dbSchema = $db->getSchema($newname);
		
			$sql = "select * from $newname";
			$result = $db->query($sql);
			while ($row = $db->fetchAssoc($result)) {
				$dirty = false;
				$row = $db->rmArray($row);

				if (true == array_key_exists('refModule', $row)) {

					if ('static' == $row['refModule']) {
						$html .= "changing refModule 'static' to 'home' in $newname." . $row['UID'] . "<br/>\n";
						$row['refModule'] = 'home';
						$dirty = true;
					}

					if ('forumthreads' == $row['refModule']) {
						$html .= "changing refModule 'forumthreads' to 'forums' in $newname." . $row['UID'] . "<br/>\n";
						$row['refModule'] = 'forums';
						$dirty = true;
					}

					if ((true == array_key_exists('refModel', $row)) && ('' == $row['refModel'])) {
						if (true == array_key_exists($row['refModule'], $rename)) {
							$newModel = $rename[$row['refModule']];							
							$html .= "setting refModel to $newModel in $newname." . $row['UID'] . "<br/>\n";
							$row['refModel'] = $newModel;
							$dirty = true;

						} else {
							$html .= "unknown refModule '" . $row['refModule'] . "' in $newname." . $row['UID'] . "<br/>\n";
						}
					}

				}

				if (true == $dirty) { $db->save($row, $dbSchema); }

			} // end foreach record

		} // end if table exists
	} // end foreach

	//---------------------------------------------------------------------------------------------
	//	render the page
	//---------------------------------------------------------------------------------------------

	$page->load('modules/admin/actions/fixtables.page.php');
	$page->blockArgs['tablesdisplay'] = $html;
	//$page->data['template'] = str_replace('%%tablesdisplay%%', $html, $page->data['template']);
	$page->render();

?>
