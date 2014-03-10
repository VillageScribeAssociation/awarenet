<?

	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');
	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//*	this action will load all default permissions from module.xml.php files and apply them to roles
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check that the current user is an admin
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	ensure default roles exists and clear existing permissions
	//----------------------------------------------------------------------------------------------
	$names = array('public', 'student', 'teacher', 'admin', 'banned');

	foreach($names as $name) {
		$model = new Users_Role();
		$ok = $model->loadByName($name);
		if (false == $ok) {
			$model->name = $name;
			$report = $model->save();
			if ('' == $report) { $session->msg("Created role $name... <br/>\n", 'ok'); }
			else { $session->msg("Could not create role $name: <br/>\n$report <br/>\n", 'bad'); }

		} else {
			//echo "role exists: $name<br/>\n";	
		}
	}

	//----------------------------------------------------------------------------------------------
	//	load default permissions specified by modules
	//----------------------------------------------------------------------------------------------
	
	$modList = $kapenta->listModules();
	foreach($modList as $modName) {
		$mod = new KModule($modName);
		if (false == $mod->loaded) {
			$session->msg("Could not load module: $modName<br/>\n", 'bad');
		} else {
			//--------------------------------------------------------------------------------------		
			//	
			//--------------------------------------------------------------------------------------
			$msg = "module: $modName <br/>\n"
				. "description: " . $mod->description . "<br/>\n"
				. "Default permissions: " . count($mod->defaultpermissions) . "<br/>\n";

			$session->msg($msg);

			foreach($mod->defaultpermissions as $defperm) { 
				$parts = explode(':', trim($defperm), 2);
				$args = explode('|', $parts[1] . '|||||||');
				$role = new Users_Role($parts[0], true);
				if (true == $role->loaded) { 
					$added = $role->permissions->add(
						$args[1], $args[2], $args[3], $args[5]
					);
					if (true == $added) {
						$session->msg("added permission: {$parts[1]} ({$parts[0]}) <br/>\n", 'ok'); 
					} else { 
						$session->msg("could not add default permission: $defperm<br/>\n", 'bad');
					}

					$report = $role->save();
					if ('' != $report) { $session->msg('Could not save role.', 'bad'); }

				} else { $session->msg("Could not load role: " . $parts[0] . "<br/>\n", 'bad'); }

			}

		}

	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to permissions page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('admin/permissions/');


?>
