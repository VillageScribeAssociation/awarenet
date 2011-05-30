<?

	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//*	object for working with module data
//--------------------------------------------------------------------------------------------------

class KModule {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $fileName = '';			//_	location of module.xml.php file if loaded [string]
	var $loaded = false;		//_	set to true when a module.xml.php gile is loaded [bool]
	
	var $modulename = '';		//_	name of this module [string]
	var $version = '';			//_	version number, for comapatability checks [string]
	var $revision = '';			//_	revision number, for compatability checks [string]
	var $description = '';		//_	description of this module [string]
	var $core = '';				//_	no longer used, TODO: remove [string]
	var $installed = '';		//_	no longer used, TODO: remove [string]
	var $enabled = '';			//_	reserved [bool]
	var $search = '';			//_	not used in awareNet [bool]

	var $defaultpermissions;	//_	default permission set for this module [array]
	
	var $models;				//_	array of models (serialized to array) [array]
	var $dependencies;			//_	reserved [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: moduleName - name of a module [string]

	function KModule($moduleName = '') {
		$this->models = array();
		$this->dependencies = array();
		$this->defaultpermissions = array();
		if ($moduleName != '') { $this->load($moduleName); }
	}	

	//----------------------------------------------------------------------------------------------
	//.	load a module.xml.php file
	//----------------------------------------------------------------------------------------------
	//arg: moduleName - name of a module [string]

	function load($moduleName) {
		global $kapenta;

		$fileName = 'modules/' . $moduleName . '/module.xml.php';
		if (false == $kapenta->fileExists($fileName)) { "echo NSF $fileName"; return false; }

		$this->modulename = $moduleName;
		$this->fileName = $fileName;
		$this->models = array();

		//------------------------------------------------------------------------------------------
		//	read xml
		//------------------------------------------------------------------------------------------
		$doc = new KXmlDocument($fileName, true);			// parse XML document
		$root = $doc->getEntity(1);							// try get root entity
		if (false == $root) { return false; }				// check that we did		

		//------------------------------------------------------------------------------------------
		//	get basic module fields
		//------------------------------------------------------------------------------------------

		foreach($root['children'] as $idx => $childId) {
			$child = $doc->getEntity($childId);
		    switch ($child['type']) {
				case 'modulename':	$this->modulename = trim($child['value']); 	break;
				case 'version':		$this->version = $child['value']; 			break;
				case 'revision':	$this->revision = $child['value']; 			break;
				case 'description':	$this->description = $child['value'];		break;
				case 'enabled':		$this->enabled = $child['value']; 			break;
				case 'search':		$this->search = $child['value'];			break;

				case 'defaultpermissions':	
					$this->defaultpermissions = array();
					$lines = explode("\n", $child['value']);		
					foreach($lines as $line) 
						{ if ('' != trim($line)) { $this->defaultpermissions[] = $line; } }

					break;	//......................................................................

				case 'models':		
					$child = $doc->getEntity($childId);
					foreach($child['children'] as $modelId) {
						$modelXml = $doc->getInnerXml($modelId, true);
						$model = new KModel($modelXml);
						$this->models[$model->name] = $model->toArray();
						//TODO: checks here
					}
					break;	//......................................................................

				case 'dependencies':	
					$this->dependencies = array();
					$child = $doc->getEntity($childId);
					foreach($child['children'] as $depId) {
						$depName = $doc->getInnerXml($depId);
						$this->dependencies[] = $depName;
					}
					break;

			}
		}

		//------------------------------------------------------------------------------------------
		//	done
		//------------------------------------------------------------------------------------------
		$this->loaded = true;
		return true;		
	}

	//----------------------------------------------------------------------------------------------
	//.	save a module.xml.php file // TODO: improve this, add checks, add bool return value
	//----------------------------------------------------------------------------------------------

	function save() {
		global $kapenta;
		$xml = $this->toXml();

		//echo "fileName: " . $this->fileName . "<br/>\n";
		//echo "<textarea rows='30' cols='50'>" . $xml . "</textarea><br/>\n";
		
		//filePutContents takes a filename relative to $kapenta->installPath

		$kapenta->filePutContents($fileName, $xml, false, true);
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize to xml
	//----------------------------------------------------------------------------------------------
	//returns: XML serialization of this module [string]

	function toXml() {
		$xml = '';				//%	return value [string]
		$modelsXml = '';		//% collects model descriptions [string]
		$defaultPerms = '';		//%	default permission set [string]
		$dependencies = '';		//%	module dependencies [string]

		foreach($this->models as $modelName => $modelAry) {
			$model = new KModel();
			$model->loadArray($modelAry);
			if (true == $model->loaded) { $modelsXml .= $model->toXml('        ', '    '); }
		}

		foreach($this->defaultpermissions as $defaultperm) 
			{ $defaultPerms .= '        ' . trim($defaultperm) . "\n"; }

		foreach($this->dependencies as $dependency) 
			{ $dependencies .= '        <module>' . trim($dependency) . "</module>\n";	}

		$xml = "<module>\n"
		     . "    <modulename>" . $this->modulename . "</modulename>\n"
		     . "    <version>" . $this->version . "</version>\n"
		     . "    <revision>" . $this->revision . "</revision>\n"
		     . "    <description>" . $this->description . "</description>\n"
		     . "    <enabled>" . $this->enabled . "</enabled>\n"
		     . "    <search>" . $this->search . "</search>\n"
		     . "    <models>\n"
			 . $modelsXml
		     . "    </models>\n"
		     . "    <dependencies>\n"
			 . $dependencies
			 . "    </dependencies>\n"
			 . "    <defaultpermissions>\n"
			 . $defaultPerms
			 . "    </defaultpermissions>\n"
		     . "</module>\n";

		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize to array
	//----------------------------------------------------------------------------------------------
	//returns: array of module data [array]

	function toArray() {
		$ary = array(
			'modulename' => $this->modulename,
			'version' => $this->version,
			'revision' => $this->revision,
			'description' => $this->description,
			'core' => $this->core,
			'models' => $this->models,
			'installed' => $this->installed,
			'enabled' => $this->enabled,
			'search' => $this->search
		);
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $user;
		$ext = $this->toArray();

		$ext['editUrl'] = '';		$ext['editLink'] = '';
		$ext['installUrl'] = '';	$ext['installLink'] = '';

		if ('admin' == $user->role) {
			$ext['editUrl'] = '%%serverPath%%admin/module/' . $this->modulename;	
			$ext['installUrl'] = '%%serverPath%%admin/install/' . $this->modulename;
			//TODO: moar
		}
		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the current module's installation status
	//----------------------------------------------------------------------------------------------
	//returns: installation status message [string]
	//, this function depends upon a standard install.inc.php

	function getInstallStatusReport() {
		global $kapenta;

		$incFile = 'modules/' . $this->modulename . '/inc/install.inc.php';
		if (false == $kapenta->fileExists($incFile)) { return 'no install script'; }

		require_once($kapenta->installPath . $incFile);

		$statusFn = $this->modulename . '_install_status_report';
		if (false == function_exists($statusFn)) { return 'no status function'; }

		return $statusFn();
	}

	//----------------------------------------------------------------------------------------------
	//.	get the current module's installation status
	//----------------------------------------------------------------------------------------------
	//returns: installation status message [string]
	//, this function depends upon a standard install.inc.php

	function install() {
		global $kapenta;

		$incFile = 'modules/' . $this->modulename . '/inc/install.inc.php';
		if (false == $kapenta->fileExists($incFile)) { return 'no install script'; }

		require_once($kapenta->installPath . $incFile);

		$statusFn = $this->modulename . '_install_module';
		if (false == function_exists($statusFn)) { return 'no install_module function'; }

		return $statusFn();
	}

}

?>
