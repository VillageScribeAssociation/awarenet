<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');
	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	move users from one school to another
//--------------------------------------------------------------------------------------------------

function schools_WebShell_migrate($args) {
	global $kapenta;
	global $user;
	global $shell;
	global $theme;
	global $utils;

	$mode = 'migrate';						//%	operation [string]
	$ajw = "<span class='ajaxwarn'>";		//%	tidy [string]
	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'migrate':
			//--------------------------------------------------------------------------------------
			//	move one or all users from one school to another
			//--------------------------------------------------------------------------------------
			if (false == array_key_exists(0, $args)) { return $ajw . "User not given.</span>"; }
			if (false == array_key_exists(1, $args)) { return $ajw . "fromSchool not given.</span>"; }
			if (false == array_key_exists(2, $args)) { return $ajw . "toSchool not given.</span>"; }

			$userRa = $args[0];
			$fromSchoolRa = $args[1];
			$toSchoolRa = $args[2];

			$fromSchool = new Schools_School($fromSchoolRa);
			if (false == $fromSchool->loaded) { return $ajw . "fromSchool unknown.</span>"; }

			$toSchool = new Schools_School($toSchoolRa);
			if (false == $toSchool->loaded) { return $ajw . "fromSchool unknown.</span>"; }

			if ('*' == $userRa) {
				//----------------------------------------------------------------------------------
				//	move all users
				//----------------------------------------------------------------------------------
				$conditions = array("school='" . $kapenta->db->addMarkup($fromSchool->UID) . "'");
				$range = $kapenta->db->loadRange('users_user', '*', $conditions);
				$success = "<span class='ajaxmsg'>Moved to " . $toSchool->name . ".</span>";

				if (0 == count($range)) { return $ajw . 'No users attend this school.</span>'; }

				foreach($range as $item) {
					$mvUser = new Users_User($item['UID']);
					if (true == $mvUser->loaded) {
						$mvUser->school = $toSchool->UID;
						$report = $mvUser->save();
						if ('' == $report) {
							$html .= $mvUser->getNameLink() . " $success<br/>";
						} else { 
							$html .= "Could not move user: $report<br/>";
						}
					}
				}

			} else {
				//----------------------------------------------------------------------------------
				//	move a single user
				//----------------------------------------------------------------------------------
				$mvUser = new Users_User($userRa);
				if (false == $mvUser->loaded) { return $ajw . "user not found.</span>"; }
				if ($mvUser->school == $toSchool->UID) { return $ajw . "no change.</span>"; }
				if ($mvUser->school != $fromSchool->UID) { return $ajw . "not at fromSchool.</span>"; }

				$mvUser->school = $toSchool->UID;
				$report = $mvUser->save();

				if ('' == $report) {
					$html .= $mvUser->getNameLink() . "<span class='ajaxmsg'>Moved</span><br/>";
				} else {
					$html .= "<span class='ajaxerror'>Could not move: $report</span>";
				}
			}

			break;	//..............................................................................

		case 'noauth':
			//--------------------------------------------------------------------------------------
			//	user not authorized
			//--------------------------------------------------------------------------------------
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the admin.describe command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function schools_WebShell_migrate_help($short = false) {
	if (true == $short) { return "Move users between schools."; }

	$html = "
	<b>usage: schools.migrate <i>user|* fromSchool toSchool</i></b><br/>
	<br/>
	Change <i>user</i>'s school, or all users at <i>fromSchool</i> if wildcard is given.
	<br/>
	";

	return $html;
}


?>
