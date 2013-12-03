<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for home module (static pages)
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Home_Static module
//--------------------------------------------------------------------------------------------------
//returns: html report, or false if not authorized [string][bool]

function home_install_module() {
	global $user;
	global $db;
	global $kapenta;
	global $kapenta;

	if ('admin' != $user->role) { return false; }	// only admins can do this

	$report = "<h3>Installing home_static Module</h3>\n";
	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create Home_Static table if it does not exist, upgrade it if it does
	//----------------------------------------------------------------------------------------------
	$model = new Home_Static();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);	

	//----------------------------------------------------------------------------------------------
	//	import any records from previous static table
	//----------------------------------------------------------------------------------------------
	//$rename = array('recordAlias' => 'alias');
	//$count = $dba->copyAll('static', $dbSchema, $rename); 
	//$report .= "<b>moved $count records from 'static' table.</b><br/>";

	//----------------------------------------------------------------------------------------------
	//	create and set a front page if one does not exist
	//----------------------------------------------------------------------------------------------
	$conditions = array("UID <> ''");
	$numPages = $db->countRange('home_static', $conditions);

	if (0 == $numPages) {
		$newPage = new Home_Static();
		$newPage->title = "awareNet";
		$newPage->template = "twocol-rightnav.template.php";
		$newPage->content = ''
		 . "[[:theme::navtitlebox::label=Home:]]\n"
		 . "<h1>Welcome</h1>\n"
		 . "<p>awareNet is social networking software for schools, for creating student "
		 . "communities in a safe, rich environment that spans the digital divide.&nbsp; It is "
		 . "free, open source software which anyone can use, change and redistribute.&nbsp; Its "
		 . "features follow our goals of education and learner collaboration, expanding young "
		 . "people's worlds beyond the confines of their local communities.&nbsp; These features "
		 . "are:<br></p><ul>"
		 . "<li>social network with profiles, status updates, notifications, etc</li>"
		 . "<li>collaborative projects to encourage teamwork</li>"
		 . "<li>discussion forums</li><li>blogging and blog aggregation</li>"
		 . "<li>personal and syndicated picture galleries</li>"
		 . "<li>instant messaging (chat)</li>"
		 . "<li>user messaging (mail)</li>"
		 . "<li>image and file management</li>"
		 . "<li>shared calendaring<br></li>"
		 . "</ul><p>The software is developed by eKhaya ICT in cooperation with the Village "
		 . "Scribe Association to enhance literacy and computer literacy in South Africa "
		 . "and to improve information flow and awareness nationally and internationally.<br></p>";

		$newPage->nav1 = "[[:theme::navtitlebox::label=Login:]]\n[[:users::loginform:]]";
		$check = $newPage->save();

		if ('' == $check) {
			$kapenta->registry->set('home.frontpage', $newPage->UID);
			$report .= "Home page set to " . $newPage->UID . ".<br/>";
		}
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	discover if this module is installed
//--------------------------------------------------------------------------------------------------
//:	if installed correctly report will contain HTML comment <!-- installed correctly -->
//returns: HTML installation status report [string]

function home_install_status_report() {
	global $user;
	global $kapenta;

	if ('admin' != $user->role) { return false; }

	$report = '';
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Static objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Home_Static();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	if (true == $installed) { $report .= '<!-- module installed correctly -->'; }
	return $report;
}

?>
