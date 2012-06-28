<?

//--------------------------------------------------------------------------------------------------
//*	display page for setting registry values on which the core depends
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	handle any posted values
	//----------------------------------------------------------------------------------------------

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'kapenta_installpath':		$registry->set('kapenta.installpath', $value);	break;
			case 'kapenta_serverpath':		$registry->set('kapenta.serverpath', $value);	break;
			case 'kapenta_sitename':		$registry->set('kapenta.sitename', $value);		break;
			case 'kapenta_alternate':		$registry->set('kapenta.alternate', $value);	break;
			case 'kapenta_snstart':			$registry->set('kapenta.snstart', $value);		break;
			case 'kapenta_snend':			$registry->set('kapenta.snend', $value);		break;

			case 'kapenta_db_host':
				$registry->set('kapenta.db.host', $value);
				$registry->set('db.host', $value);
				break;		//......................................................................

			case 'kapenta_db_name':
				$registry->set('kapenta.db.name', $value);
				$registry->set('db.name', $value);
				break;		//......................................................................

			case 'kapenta_db_user':
				$registry->set('kapenta.db.user', $value);
				$registry->set('db.user', $value);
				break;		//......................................................................

			case 'kapenta_db_password':
				$registry->set('kapenta.db.password', $value);
				$registry->set('db.password', $value);
				break;		//......................................................................

			case 'kapenta_db_persistent':
				$registry->set('kapenta.db.persistent', $value);
				$registry->set('db.persistent', $value);
				break;		//......................................................................


			case 'kapenta_themes_default':	
				//TODO: check this value
				$registry->set('kapenta.themes.default', $value);		
				break;	//..........................................................................

			case 'kapenta_modules_default':	
				//TODO: check this value
				$registry->set('kapenta.modules.default', $value);		
				break;	//..........................................................................

		}
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/admin/actions/coresetup.page.php');
	$page->render();

?>
