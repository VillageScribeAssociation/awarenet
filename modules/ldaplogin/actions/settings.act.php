<?

//--------------------------------------------------------------------------------------------------
//*	ldap login module settings
//--------------------------------------------------------------------------------------------------
//+	Settings for LDAP Login Integration.

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	check default presets
	//----------------------------------------------------------------------------------------------
	$defaults = array(
		'ldaplogin.server' => '',
		'ldaplogin.port' => '389',
		'ldaplogin.school' => '',
		'ldaplogin.schoolshortname' => '',
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

				case 'ldaplogin_server':	
					$kapenta->registry->set('ldaplogin.server', $value);	
					break;	//..........................................................................

				case 'ldaplogin_port':	
					$kapenta->registry->set('ldaplogin.port', $value);	
					break;	//..........................................................................

				case 'ldaplogin_school':	
					$kapenta->registry->set('ldaplogin.school', $value);	
					break;	//..........................................................................

				case 'ldaplogin_schoolshortname':	
					$kapenta->registry->set('ldaplogin.schoolshortname', $value);	
					break;	//..........................................................................
			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/ldaplogin/actions/settings.page.php');
	$kapenta->page->render();

?>
