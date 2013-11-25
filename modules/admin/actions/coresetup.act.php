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
			case 'kapenta_installpath':		$kapenta->registry->set('kapenta.installpath', $value);	break;
			case 'kapenta_serverpath':		$kapenta->registry->set('kapenta.serverpath', $value);	break;
			case 'kapenta_sitename':		$kapenta->registry->set('kapenta.sitename', $value);		break;
			case 'kapenta_alternate':		$kapenta->registry->set('kapenta.alternate', $value);	break;
			case 'kapenta_snstart':			$kapenta->registry->set('kapenta.snstart', $value);		break;
			case 'kapenta_snend':			$kapenta->registry->set('kapenta.snend', $value);		break;

			case 'kapenta_db_host':
				$kapenta->registry->set('kapenta.db.host', $value);
				$kapenta->registry->set('db.host', $value);
				break;		//......................................................................

			case 'kapenta_db_name':
				$kapenta->registry->set('kapenta.db.name', $value);
				$kapenta->registry->set('db.name', $value);
				break;		//......................................................................

			case 'kapenta_db_user':
				$kapenta->registry->set('kapenta.db.user', $value);
				$kapenta->registry->set('db.user', $value);
				break;		//......................................................................

			case 'kapenta_db_password':
				$kapenta->registry->set('kapenta.db.password', $value);
				$kapenta->registry->set('db.password', $value);
				break;		//......................................................................

			case 'kapenta_db_persistent':
				$kapenta->registry->set('kapenta.db.persistent', $value);
				$kapenta->registry->set('db.persistent', $value);
				break;		//......................................................................


			case 'kapenta_themes_default':	
				//TODO: check this value
				$kapenta->registry->set('kapenta.themes.default', $value);		
				break;	//..........................................................................

			case 'kapenta_modules_default':	
				//TODO: check this value
				$kapenta->registry->set('kapenta.modules.default', $value);		
				break;	//..........................................................................

		}
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/admin/actions/coresetup.page.php');
	$kapenta->page->render();

?>
