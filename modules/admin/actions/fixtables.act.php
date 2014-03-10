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

	//if ('admin' != $user->role) { $kapenta->page->do403(); }

	//---------------------------------------------------------------------------------------------
	//	specify which tables not to add fields to
	//---------------------------------------------------------------------------------------------

	$html = '';

	/*
	if (true == $kapenta->db->tableExists('aliases_alias')) { $kapenta->db->query("drop table Aliases_Alias"); }
	$html .= aliases_install_module();
	$html .= "<br/>\n";

	if (true == $kapenta->db->tableExists('announcements_announcement')) { $kapenta->db->query("drop table Announcements_Announcement"); }
	$html .= announcements_install_module();
	$html .= "<br/>\n";

	if (true == $kapenta->db->tableExists('calendar_entry')) { $kapenta->db->query("drop table Calendar_Entry"); }
	$html .= calendar_install_module();
	$html .= "<br/>\n";

	if (true == $kapenta->db->tableExists('comments_comment')) { $kapenta->db->query("drop table Comments_Comment"); }
	$html .= comments_install_module();
	$html .= "<br/>\n";

	if (true == $kapenta->db->tableExists('files_file')) { $kapenta->db->query("drop table Files_File"); }
	if (true == $kapenta->db->tableExists('files_folder')) { $kapenta->db->query("drop table Files_Folder"); }
	$html .= files_install_module();
	$html .= "<br/>\n";

	if (true == $kapenta->db->tableExists('forums_board')) { $kapenta->db->query("drop table Forums_Board"); }
	if (true == $kapenta->db->tableExists('forums_thread')) { $kapenta->db->query("drop table Forums_Thread"); }
	if (true == $kapenta->db->tableExists('forums_reply')) { $kapenta->db->query("drop table Forums_Reply"); }
	$html .= forums_install_module();
	$html .= "<br/>\n";

	if (true == $kapenta->db->tableExists('groups_group')) { $kapenta->db->query("drop table Groups_Group"); }
	if (true == $kapenta->db->tableExists('groups_membership')) { $kapenta->db->query("drop table Groups_Membership"); }
	$html .= groups_install_module();
	$html .= "<br/>\n";

	if (true == $kapenta->db->tableExists('home_static')) { $kapenta->db->query("drop table Home_Static"); }
	$html .= home_install_module();
	$html .= "<br/>\n";

	if (true == $kapenta->db->tableExists('images_image')) { $kapenta->db->query("drop table Images_Image"); }
	$html .= images_install_module();
	$html .= "<br/>\n";

	if (true == $kapenta->db->tableExists('messages_message')) { $kapenta->db->query("drop table Messages_Message"); }
	$html .= messages_install_module();
	$html .= "<br/>\n";

	if (true == $kapenta->db->tableExists('moblog_post')) { $kapenta->db->query("drop table Moblog_Post"); }
	$html .= moblog_install_module();
	$html .= "<br/>\n";

	if (true == $kapenta->db->tableExists('projects_project')) { $kapenta->db->query("drop table Projects_Project"); }
	if (true == $kapenta->db->tableExists('projects_membership')) { $kapenta->db->query("drop table Projects_Membership"); }
	if (true == $kapenta->db->tableExists('projects_revision')) { $kapenta->db->query("drop table Projects_Revision"); }
	$html .= projects_install_module();
	$html .= "<br/>\n";

	if (true == $kapenta->db->tableExists('schools_school')) { $kapenta->db->query("drop table Schools_School"); }
	$html .= schools_install_module();
	$html .= "<br/>\n";

	if (true == $kapenta->db->tableExists('sync_server')) { $kapenta->db->query("drop table Sync_Server"); }
	if (true == $kapenta->db->tableExists('sync_notice')) { $kapenta->db->query("drop table Sync_Notice"); }
	if (true == $kapenta->db->tableExists('sync_download')) { $kapenta->db->query("drop table Sync_Download"); }
	$html .= sync_install_module();
	$html .= "<br/>\n";
	
	if (true == $kapenta->db->tableExists('users_user')) { $kapenta->db->query("drop table Users_User"); }
	if (true == $kapenta->db->tableExists('users_role')) { $kapenta->db->query("drop table Users_Role"); }
	if (true == $kapenta->db->tableExists('users_friendship')) { $kapenta->db->query("drop table Users_Friendship"); }
	$html .= users_install_module();
	$html .= "<br/>\n";
	*/

	//----------------------------------------------------------------------------------------------
	//	patch up 
	//----------------------------------------------------------------------------------------------
	$rename = array(
		'announcements' => 'announcements_announcement',
		'calendar' => 'calendar_entry',
		'comments' => 'comments_comment',
		'downloads' => 'sync_download',
		'files' => 'files_file',
		'folders' => 'files_folder',
		'forums' => 'forums_board',
		'forumreplies' => 'forums_reply',
		'forumthreads' => 'forums_thread',
		'friendships' => 'users_friendship',
		'gallery' => 'gallery_gallery',
		'groupmembers' => 'groups_membership',
		'groups' => 'groups_group',
		'images' => 'images_image',
		'messages' => 'messages_message',
		'moblog' => 'moblog_post',
		'projectmembers' => 'projects_membership',
		'projectrevisions' => 'projects_revision',
		'projects' => 'projects_project',
		'recordalias' => 'aliases_alias',
		'schools' => 'schools_school',
		'servers' => 'sync_server',
		'sync' => 'sync_notice',
		'users' => 'users_user',
		'wiki' => 'wiki_article',
		'wikirevisions' => 'wiki_revision'
	);

	$dba = new KDBAdminDriver();

	foreach($rename as $oldname => $newname) {
		if (true == $kapenta->db->tableExists($newname)) {

			$dbSchema = $kapenta->db->getSchema($newname);
		
			$sql = "select * from $newname";
			$result = $kapenta->db->query($sql);
			while ($row = $kapenta->db->fetchAssoc($result)) {
				$dirty = false;
				$row = $kapenta->db->rmArray($row);

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

				if (true == $dirty) { $kapenta->db->save($row, $dbSchema); }

			} // end foreach record

		} // end if table exists
	} // end foreach

	//---------------------------------------------------------------------------------------------
	//	render the page
	//---------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/admin/actions/fixtables.page.php');
	$kapenta->page->blockArgs['tablesdisplay'] = $html;
	//$page->data['template'] = str_replace('%%tablesdisplay%%', $html, $page->data['template']);
	$kapenta->page->render();

?>
