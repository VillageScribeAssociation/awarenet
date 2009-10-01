<?

//--------------------------------------------------------------------------------------------------
//	object for working with module data
//--------------------------------------------------------------------------------------------------

class KModule {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $fileName = '';

	var $modulename = '';
	var $version = '';
	var $revision = '';
	var $description = '';
	var $core = '';
	var $installed = '';
	var $enabled = '';
	var $search = '';
	
	var $permissions;
	var $dependancy;
	var $blocks;

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function KModule($modulename = '') {
		$this->permissions = array();
		$this->dependency = array();
		$this->blocks = array();

		if ($modulename != '') { $this->load($modulename); }
	}	

	//----------------------------------------------------------------------------------------------
	//	load a module.xml.php file
	//----------------------------------------------------------------------------------------------

	function load($moduleName) {
		global $installPath;
		$fileName = $installPath . 'modules/' . $moduleName . '/module.xml.php';
		if (file_exists($fileName) == false) { return false; }
		$this->name = $moduleName;
		$this->fileName = $fileName;

		//------------------------------------------------------------------------------------------
		//	read xml
		//------------------------------------------------------------------------------------------
		$xe = xmlLoad($fileName);

		//------------------------------------------------------------------------------------------
		//	get basic module fields
		//------------------------------------------------------------------------------------------

		foreach($xe->children as $index => $child) {
		    switch ($child->type) {
				case 'modulename':	$this->modulename = $child->toTxt(); 	break;
				case 'version':		$this->version = $child->toTxt(); 		break;
				case 'revision':	$this->revision = $child->toTxt(); 		break;
				case 'description':	$this->description = $child->toTxt();	break;
				case 'core':		$this->core = $child->toTxt(); 			break;
				case 'installed':	$this->installed = $child->toTxt();		break;
				case 'enabled':		$this->enabled = $child->toTxt(); 		break;
				case 'search':		$this->search = $child->toTxt();		break;

			}
		}

		//------------------------------------------------------------------------------------------
		//	get dependancies
		//------------------------------------------------------------------------------------------

		$this->dependancy = array();
		$depXe = $xe->getTypeArray('depend');
		foreach ($depXe as $dIdx => $dep) { $this->dependancy[] = $dep->toTxt(); } 

		//------------------------------------------------------------------------------------------
		//	get permissions
		//------------------------------------------------------------------------------------------
		
		$this->permissions = array();
		$permXe = $xe->getTypeArray('perm');
		foreach($permXe as $pIdx => $perm) {
			$permissionTxt = $perm->toTxt();
			$pipePos = strpos($permissionTxt, '|');
			if ($pipePos > 0) {
				$permName = substr($permissionTxt, 0, $pipePos);
				$permVal = substr($permissionTxt, ($pipePos + 1));

				if (array_key_exists($permName, $this->permissions) == false) {
					$this->permissions[$permName] = array();
					$this->permissions[$permName][] = $permVal;
				} else {
					$this->permissions[$permName][] = $permVal;
				}
			}
		} 

		//------------------------------------------------------------------------------------------
		//	done
		//------------------------------------------------------------------------------------------
		return true;		
	}

	//----------------------------------------------------------------------------------------------
	//	save a module.xml.php file // TODO: use XmlEntity for this
	//----------------------------------------------------------------------------------------------

	function save() {
		$deps = '';
		$perms = '';

		//foreach($this->dependancy as $dep) { $deps .= "        <depend>$dep</depend>\n"; }
		foreach($this->permissions as $permName => $grant) { 
			foreach($grant as $condition) {
				if (strlen(trim($condition)) > 1) { // non-empty
					$perms .= "        <perm>$permName|" . $condition . "</perm>\n"; 
				}
			}
		}

		$xml = "<module>\n"
		     . "    <modulename>" . $this->modulename . "</modulename>\n"
		     . "    <version>" . $this->version . "</version>\n"
		     . "    <revision>" . $this->description . "</revision>\n"
		     . "    <description>" . $this->description . "</description>\n"
		     . "    <core>" . $this->core . "</core>\n"
		     . "    <installed>" . $this->installed . "</installed>\n"
		     . "    <enabled>" . $this->enabled . "</enabled>\n"
		     . "    <dbschema>" . $this->dbschema . "</dbschema>\n"
		     . "    <search>" . $this->search . "</search>\n"
		     . "    <dependancy>\n" . $deps . "    </dependancy>\n"
		     . "    <permissions>\n" . $perms . "    </permissions>\n"
		     . "    <blocks>\n"
		     . "    </blocks>\n"
		     . "</module>\n";

		$xml = '<' . '? /' . '*' . "\n" . $xml . '*' . '/ ?' . '>';

		//echo "fileName: " . $this->fileName . "<br/>\n";
		//echo "<textarea rows='30' cols='50'>" . $xml . "</textarea><br/>\n";

		$fH = fopen($this->fileName, 'w+');
		fwrite($fH, $xml);
		fclose($fH);

	}

	//----------------------------------------------------------------------------------------------
	//	to array
	//----------------------------------------------------------------------------------------------

	function toArray() {
		$ary = array();
		$ary['modulename'] = $this->modulename;
		$ary['version'] = $this->version;
		$ary['revision'] = $this->revision;
		$ary['description'] = $this->description;
		$ary['core'] = $this->core;
		$ary['installed'] = $this->installed;
		$ary['enabled'] = $this->enabled;
		$ary['dbschema'] = $this->dbschema;
		$ary['search'] = $this->search;
		return $ary;
	}

}

?>
