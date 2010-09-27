<?

	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');
	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//*	this action will load all default permissions from module.xml.php files and apply them to roles
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check that the current user is an admin
	//----------------------------------------------------------------------------------------------
	//if ('admin' != $user->role) { $page->do403(); }

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
			if ('' == $report) { echo "created role $name... <br/>\n"; }
			else { echo "could not create role $name: <br/>\n$report <br/>\n"; }

		} else {
			echo "role exists: $name<br/>\n";	
		}
	}

	//----------------------------------------------------------------------------------------------
	//	load default permissions specified by modules
	//----------------------------------------------------------------------------------------------
	
	$modList = $kapenta->listModules();
	foreach($modList as $modName) {
		echo "<hr/>\n";
		$mod = new KModule($modName);
		if (false == $mod->loaded) { echo "Could not load module: $modName<br/>\n"; }
		else {

			//--------------------------------------------------------------------------------------		
			//	
			//--------------------------------------------------------------------------------------
			echo "module: $modName <br/>\n"
				. "description: " . $mod->description . "<br/>\n"
				. "Default permissions: " . count($mod->defaultpermissions) . "<br/>\n";

			

			foreach($mod->defaultpermissions as $defperm) { 
				$parts = explode(':', trim($defperm), 2);
				$args = explode('|', $parts[1] . '|||||||');
				$role = new Users_Role($parts[0], true);
				if (true == $role->loaded) { 
					$added = $role->addPermission($args[0], $args[1], $args[2], $args[3], $args[5]);
					if (true == $added) {echo "added permission: {$parts[1]} ({$parts[0]}) <br/>\n"; }
					else { echo "could not add default permission: $defperm<br/>\n"; }

				} else { echo "could not load role: " . $parts[0] . "<br/>\n"; }

			}

		}

	}

?>
