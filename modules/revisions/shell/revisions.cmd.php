<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	commands for interacting with the revisions table
//--------------------------------------------------------------------------------------------------

function revisions_WebShell_revisions($args) {
	global $kapenta;
	global $user;
	global $db;
	global $shell;
	global $theme;
	global $kapenta;
	global $utils;

	$mode = 'list';							//%	operation [string]
	$html = '';								//%	return value [string]
	$ajw = "<span class='ajaxwarn'>";		//%	tidy [string]
	$max = 100;								//%	max results of a query [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists(0, $args)) {
		switch($args[0]) {
			case '-h':			$mode = 'help';		break;
			case '-l':			$mode = 'list';		break;
			case '-s':			$mode = 'show';		break;
			case '--help':		$mode = 'help';		break;
			case '--list':		$mode = 'list';		break;
			case '--show':		$mode = 'show';		break;
		}
	}

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {

		case 'help':
			//--------------------------------------------------------------------------------------
			//	display the manpage
			//--------------------------------------------------------------------------------------
			$html = revisions_WebShell_revisions_help();
			break;	//..............................................................................

		case 'list':
			//--------------------------------------------------------------------------------------
			//	check arguments / set up filter
			//--------------------------------------------------------------------------------------
			$byUser = '';
			$refModule = '';
			$refModel = '';
			$refUID = '';

			foreach($args as $arg) {
				$arg = strtolower($arg);
				if ('-u=' == substr($arg, 0, 3)) { $byUser = substr($arg, 3); }
				if ('--user=' == substr($arg, 0, 7)) { $byUser = substr($arg, 7); }
				if ('-m=' == substr($arg, 0, 3)) { $refModule = substr($arg, 3); }
				if ('--module=' == substr($arg, 0, 9)) { $refModule = substr($arg, 9); }
				if ('-t=' == substr($arg, 0, 3)) { $refModel = substr($arg, 3); }
				if ('--type=' == substr($arg, 0, 7)) { $refModel = substr($arg, 7); }
				if ('--model=' == substr($arg, 0, 8)) { $refModel = substr($arg, 8); }
				if ('-i=' == substr($arg, 0, 3)) { $refUID = substr($arg, 3); }
				if ('--uid=' == substr($arg, 0, 6)) { $refUID = substr($arg, 6); }
			}

			//echo "<pre>\n";
			//print_r($args);
			//echo "</pre>\n";
			
			$html .= ''
			 . "<small>"
			 . "-u filter by user: " . (($byUser != '') ? $byUser : 'all') . "<br/>\n"
			 . "-m filter by module: " . (($refModule != '') ? $refModule : 'all') . "<br/>\n"
			 . "-t filter by model: " . (($refModel != '') ? $refModel : 'all') . "<br/>\n"
			 . "-i filter by UID: " . (($refUID != '') ? $refUID : 'all') . "<br/>\n"
			 . "</small>";

			//--------------------------------------------------------------------------------------
			//	check arguments / filters
			//--------------------------------------------------------------------------------------

			if ('' != $byUser) { 
				if (false == $db->objectExists('users_user', $byUser)) {	//	find by UID
					$tempUser = new Users_User($byUser);					//	find by alias
					if (true == $tempUser->loaded) {
						$byUser = $tempUser->UID;
					} else {
						$tempUser = new Users_User($byUser, true);			//	find by username
						if (true == $tempUser->loaded) {
							$byUser = $tempUser->UID;
						} else {
							$html .= "<span style='ajaxwarn'>User not found.</span><br/>";
							$byUser = '';
						}
					}
				}
			}

			//--------------------------------------------------------------------------------------
			//	query the revisions table
			//--------------------------------------------------------------------------------------
			$conditions = array();

			if ('' != $byUser) { $conditions[] = "createdBy='" . $db->addMarkup($byUser) . "'"; }
			if ('' != $refModule) { $conditions[] = "refModule='" . $db->addMarkup($refModule) . "'"; }
			if ('' != $refModel) { $conditions[] = "refModel='" . $db->addMarkup($refModel) . "'"; }
			if ('' != $refUID) { $conditions[] = "refUID='" . $db->addMarkup($refUID) . "'"; }

			$range = $db->loadRange('revisions_revision', '*', $conditions, 'createdOn DESC', $max);

			//--------------------------------------------------------------------------------------
			//	display results
			//--------------------------------------------------------------------------------------
			$table = array();
			$table[] = array('UID', 'refModule', 'refModel', 'user', 'date');
			foreach($range as $item) {
				$table[] = array(
					"<a href=\""
					 . "javascript:kshellwindow.submit('revisions.revisions -s " . $item['UID'] . "')"
					 . "\">"
					 . $item['UID']
					 . "</a>",
					$item['refModule'],
					$item['refModel'],
					"[[:users::namelink::target=_parent::userUID=" . $item['createdBy'] . ":]]",
					$item['createdOn']
				);
			}

			if (0 == count($range)) {
				$html .= "No revisions match criteria.<br/>";
			} else {
				$html .= $theme->arrayToHtmlTable($table, true, true);
			}

			$html = $theme->expandBlocks($html, 'mobile');

			break;	//..............................................................................

		case 'noauth':
			//--------------------------------------------------------------------------------------
			//	user not authorized
			//--------------------------------------------------------------------------------------
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

		case 'show':
			//--------------------------------------------------------------------------------------
			//	display the contents of a revision
			//--------------------------------------------------------------------------------------
			if (false == array_key_exists(1, $args)) {
				return "<span class='ajaxwarn'>Revision UID not given.</span>";
			}

			$model = new Revisions_Revision($args[1]);
			if (false == $model->loaded) {
				return "<span class='ajaxwarn'>Unknown revision.</span>";
			}

			$block = "[[:revisions::showrevision::UID=" . $model->UID . ":]]";
			$html .= $theme->expandBlocks($block, 'nav1');			

			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the admin.registry command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function revisions_WebShell_revisions_help($short = false) {
	if (true == $short) { return "View revision history of objects."; }

	$html = "
	<b>usage: revisions.revisions [-l] </b><br/>
	<br/>
	<b>--list|-l</b> list revisions, starting with the most recent.  This list may be filtered by
	specifying any or all of:<br/>
	<ul>
		<li><b>--user|-u</b> user name, alias or UID</li>
		<li><b>--module|-m</b> module name</li>
		<li><b>--model|-t</b> model name / type of object</li>
		<li><b>--uid|-i</b> unique ID object</li>
	</ul>
	For example: <tt>revisions -l -u=admin -t=forums_thread</tt>
	<br/>
	";

	return $html;
}


?>
