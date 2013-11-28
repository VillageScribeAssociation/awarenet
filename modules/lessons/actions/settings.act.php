<?

//--------------------------------------------------------------------------------------------------
//*	lesson module settings
//--------------------------------------------------------------------------------------------------
//+	Settings for KA Lite Integration.

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	check default presets
	//----------------------------------------------------------------------------------------------
	$defaults = array(
		'kalite.installation' => 'http://localhost:8008',
		'kalite.admin.user' => 'awarenet',
		'kalite.admin.pwd' => 'awarenet',
		'kalite.db.file' => '/www/ka-lite/kalite/database/data.sqlite',
	);

	foreach($defaults as $label => $value) {
		$key = $label;
		if ('' == $kapenta->registry->get($key)) { $kapenta->registry->set($key, $value);	}
	}

	//----------------------------------------------------------------------------------------------
	//	handle any POST vars
	//----------------------------------------------------------------------------------------------
	if ((true == array_key_exists('action', $_POST)) && ('addPreset' == $_POST['action'])) {

		foreach($_POST as $key => $value) {
			switch($key) {

				case 'kalite_installation':	
					$kapenta->registry->set('kalite.installation', $value);	
					break;	//..........................................................................

				case 'kalite_admin_user':	
					$kapenta->registry->set('kalite.admin.user', $value);	
					break;	//..........................................................................

				case 'kalite_admin_pwd':	
					$kapenta->registry->set('kalite.admin.pwd', $value);	
					break;	//..........................................................................

				case 'kalite_db_file':	
					$kapenta->registry->set('kalite.db.file', $value);	
					break;	//..........................................................................
			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/lessons/actions/settings.page.php');
	$page->render();

?>
