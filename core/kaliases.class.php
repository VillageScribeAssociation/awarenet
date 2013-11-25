<? 

	require_once(dirname(__FILE__) . '/../modules/aliases/models/alias.mod.php');

//--------------------------------------------------------------------------------------------------
//*	aliases are SEO-friendly strings used to identify objects
//--------------------------------------------------------------------------------------------------
//+
//+	All objects are identified by their UID.  Additionally, objects may have multiple aliases.
//+
//+	http://mysite.org/widgets/234987023495 -(301)-> http://mysite.org/widgets/Cat-Polishing-Machine
//+
//+	All objects have a canonical alias stored in the 'alias' field on the object itself.  All
//+	other aliases recorded in the aliases_alias table should recirect to the canonical alias.
//+
//+	Objects which cannot use their preferred alias will be assigned another.  The default alias is 
//+	the one stored on the object itself. Alternate aliases 302 to the default alias.
//+
//+	The function findRedirect($model) returns the UID of an object, or redirects to the default
//+	alias if a non-default one is used.
//+
//+	Where Memcached is available aliases are opportunistically cached.  For canonical aliases:
//+	
//+		alias::model_name::lower-case-alias => UID of owner object
//+
//+	For alternate aliases:
//+
//+		aliasalt::model_name::lower-case-not-canonical => Canonical-Alias

class KAliases {

	//----------------------------------------------------------------------------------------------
	//.	member variables TODO: load these from some settings file
	//----------------------------------------------------------------------------------------------
	var $maxLen = 100;		// maximum length of an alias [int]

	//----------------------------------------------------------------------------------------------
	//.	constructor (nothing as yet)
	//----------------------------------------------------------------------------------------------
	
	function KAliases() {
		// nothing here yet
	}

	//----------------------------------------------------------------------------------------------
	//.	create an alias for a given object and identifying string
	//----------------------------------------------------------------------------------------------
	//arg: refModule - name of a module [string]
	//arg: refModel - class of object which will own the alias [string]
	//arg: refUID - UID of object which will own the alias [string]
	//arg: plainText - from which alias will be dericed [string]
	//returns: alias assigned to this object, empty string on failure [string]

	function create($refModule, $refModel, $refUID, $plainText) {
		global $kapenta;

		//------------------------------------------------------------------------------------------
		//	get the default alais for this plaintext, and lists of actions and modules
		//------------------------------------------------------------------------------------------
		$default = $this->stringToAlias($plainText);	//%	ideal alias [string]
		$modules = $kapenta->listModules();				//%	list of modules [array:string]
		$actions = $kapenta->listActions($refModule);	//%	list of actions [array:string]

		if ('' == $default) {		 						// no plainText
			$default = $refUID;								// no refUID
			if ('' == trim($default)) { return ''; } 	// no service
		}

		//------------------------------------------------------------------------------------------
		//	check for collisions with action or module names
		//------------------------------------------------------------------------------------------
		foreach($modules as $module) { 
			if (strtolower($default) == strtolower($module)) { $default .= '-'; }
		}

		foreach($actions as $action) { 
			if (strtolower($default) . '.act.php' == strtolower($action)) { $default .= '-'; }
		}
	
		//------------------------------------------------------------------------------------------
		//	check if object (#refUID) already owns its default alias - if so then we're done
		//------------------------------------------------------------------------------------------
		$defaultOwner = $this->getOwner($refModule, $refModel, $default);
	
		if ($defaultOwner == $refUID) { return $default; }
		if ($defaultOwner == false) {
			//--------------------------------------------------------------------------------------
			//	alias is not owned, it can be assigned to this object
			//--------------------------------------------------------------------------------------
			$this->saveAlias($refModule, $refModel, $refUID, $default);
			return $default;
		}

		//------------------------------------------------------------------------------------------
		//	the default alias is already owned by another object
		//------------------------------------------------------------------------------------------
		$currAliases = $this->getAll($refModule, $refModel, $refUID);

		if (0 == count($currAliases)) {
			//--------------------------------------------------------------------------------------
			//	this object has no aliases yet, find an unused object by appending a number
			//--------------------------------------------------------------------------------------
			$available = $this->findAvailable($refModule, $refModel, $default, 0);
			if ($available == false) { return ''; }
			$this->saveAlias($refModule, $refModel, $refUID, $available);
			return $available;			

		} else {
			//--------------------------------------------------------------------------------------
			//	the default is owned by another object, return the first alias the object registered
			//--------------------------------------------------------------------------------------
			foreach ($currAliases as $caUID => $caAlias) { return $caAlias; }

		}

		return ''; // just in case
	}

	//----------------------------------------------------------------------------------------------
	//	checks that the alias supplied is the (object) default, redirects to the default if not
	//----------------------------------------------------------------------------------------------
	//arg: model - type of object which owns this alias [string]
	//returns: UID of object owning the alias given, 301 to correct alias, or it 404s [string]

	function findRedirect($model) {
		global $kapenta;
		global $req;
		global $page;
		global $db;
		global $session;

		$model = strtolower($model);
		$safeAlias = strtolower($db->addMarkup($req->ref));

		//------------------------------------------------------------------------------------------
		//	look for this object in memcache
		//------------------------------------------------------------------------------------------
		if (true == $kapenta->mcEnabled) {
			//	try as canonical alias
			$aliasKey = 'alias::' . $model . '::' . strtolower($req->ref);
			
			if (true == $kapenta->cacheHas($aliasKey)) {
				$cachedUID = $kapenta->cacheGet($aliasKey);
				if ('s:' === substr($cachedUID, 0, 2)) { $cachedUID = unserialize($cachedUID); }
				//echo "Cached: " . $cachedUID . "<br/>\n";
				return $cachedUID;		//......................................
			}

			//	try as alternate alias
			$redirectKey = 'aliasalt::' . $model . '::' . strtolower($req->ref);
			if (true == $kapenta->cacheHas($redirectKey)) {
				//TODO: use $request object to reconstruct URI, include args
				$URI = $req->module . '/' . $req->action . '/' . $default;
				$URI = str_replace('//', '/', $URI);
				$page->do301($URI);

				return '';		//..................................................................
			}
		}

		//------------------------------------------------------------------------------------------
		//	look for this object in the aliases_alias table
		//------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "aliaslc='" . $safeAlias . "'";
		$conditions[] = "refModel='" . $db->addMarkup($model) . "'";
		$range = $db->loadRange('aliases_alias', '*', $conditions, 'createdOn', 1);

		foreach($range as $item) {
			//--------------------------------------------------------------------------------------
			//	we have an alias object, find canonical and compare
			//--------------------------------------------------------------------------------------
			$canonical = $this->getDefault($model, $item['refUID']);
			if ($canonical == '') { $page->do404(); }							//	no such object
	
			//--------------------------------------------------------------------------------------
			//	cache for next time
			//--------------------------------------------------------------------------------------
			if (true == $kapenta->mcEnabled) {
				$aliasKey = 'alias::' . $model . '::' . strtolower($canonical);
				$kapenta->cacheSet($aliasKey, $item['refUID']);					//	canonical alias

				if (strtolower($req->ref) != strtolower($canonical)) {
					$redirectKey = 'aliasalt::' . $model . '::' . strtolower($item['alias']);
					$kapenta->cacheSet($redirectKey, $canonical);				//	alternate alias
				}
			}

			if (strtolower($req->ref) == strtolower($canonical)) {
				//----------------------------------------------------------------------------------
				//	default alias was used, return the UID and we're done
				//----------------------------------------------------------------------------------
				return $item['refUID'];

			} else {
				//----------------------------------------------------------------------------------
				//	alias used is not default or wrong case (mr-smith -(301)-> Mr-Smith)
				//----------------------------------------------------------------------------------
				//TODO: use $request object to reconstruct URI, include args
				$URI = $req->module . '/' . $req->action . '/' . $canonical;
				$URI = str_replace('//', '/', $URI);
				$page->do301($URI);

			}
	
		} // end foreach aliases_alias object

		//------------------------------------------------------------------------------------------
		//	no matches found, perhaps this is a valid UID
		//------------------------------------------------------------------------------------------
		if (true == $db->objectExists($model, $req->ref)) { return $req->ref;}

		//------------------------------------------------------------------------------------------
		//	not a UID, search directly
		//------------------------------------------------------------------------------------------
		if ('' != trim($req->ref)) {
			$conditions = array("alias='" . $db->addMarkup(trim($req->ref)) . "'");
			$range = $db->loadRange($model, '*', $conditions);
			if (count($range) > 0) {
				$item = array_pop($range);
				return $item['UID'];
			}
		}

		$page->do404('Could not find aliased item');
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//.	create new Alias object
	//----------------------------------------------------------------------------------------------
	//arg: refModule - name of a module [string]
	//arg: refModel - class of object which will own the alias [string]
	//arg: refUID - UID of object which will own the alias [string]
	//arg: alias - to be created [string]
	//returns: UID of new alias, or false on failure [string][bool]	

	function saveAlias($refModule, $refModel, $refUID, $alias) {
		global $kapenta;
		global $session;

		$model = new Aliases_Alias();
		$model->refModule = $refModule;
		$model->refModel = $refModel;
		$model->refUID = $refUID;
		$model->alias = $alias;
		$model->alias = strtolower($alias);
		$report = $model->save();

		if ('' != $report) {
			$session->msgAdmin('Error: could not create alias:<br/>' . $report, 'bad');
			return '';
		}

		return $model->UID;
	}

	//----------------------------------------------------------------------------------------------
	//.	get dbSchema for alias table
	//----------------------------------------------------------------------------------------------
	//returns: dbSchema [array]

	function getDbSchema() {
		$model = new Aliases_Alias();
		return $model->getDbSchema();
	}

	//----------------------------------------------------------------------------------------------
	//.	make alias froma given string
	//----------------------------------------------------------------------------------------------
	//arg: plainText - preferably in latin script [string]
	//returns: format suitable for including in URLs [string]

	function stringToAlias($plainText) {
		$alias = '';

		//------------------------------------------------------------------------------------------
		//	remove any chars which are non-alphanumeric, space, minus or '.'
		//------------------------------------------------------------------------------------------
		$plainText = str_replace("&amp;", "-and-", $plainText);

		$numChars = strlen($plainText);
		for($i = 0; $i < $numChars; $i++) {
			$currChar = substr($plainText, $i, 1);
		
			switch($currChar) {
				case "\r":	$alias .= '-';		break;		// replace these chars
				case "\n":	$alias .= '-';		break;		// ...
				case "\t":	$alias .= '-';		break;
				case ' ':	$alias .= '-';		break;
				case '_':	$alias .= '-';		break;
				case '/':	$alias .= '-fs-';	break;
				case '&':	$alias .= '-and-';	break;
				case ':':	$alias .= '-c-';	break;
				case ';':	$alias .= '-sc-';	break;
				case '#':	$alias .= 'No.';	break;
				case '?':	$alias .= '-q-';	break;

				case '-':	$alias .= '-';		break;		// allow these chars
				case '.':	$alias .= '.';		break;		// ...

				default:
					$oCC = ord($currChar);
					if (($oCC >= 48) AND ($oCC <= 57)) { $alias .= $currChar; } 	// 0-9
					if (($oCC >= 97) AND ($oCC <= 122)) { $alias .= $currChar; } 	// a-z
					if (($oCC >= 65) AND ($oCC <= 90)) { $alias .= $currChar; } 	// A-Z
					if ($oCC == 45) { $alias .= '-'; } 								// minus
					if ($oCC == 46) { $alias .= '.'; } 								// allow period
					break;
			}		
		}

		$alias = substr($alias, 0, 100);	// make sure it's less than 100 chars long

		//------------------------------------------------------------------------------------------
		//	tidy output, and we're done
		//------------------------------------------------------------------------------------------
		$alias = str_replace("-and-amp-sc-", "-and-", $alias);
		$alias = str_replace("---", "-", $alias);
		$alias = str_replace("---", "-", $alias);
		$alais = str_replace("---", "-", $alias);
		$alias = str_replace("--", "-", $alias);

		return $alias;	
	}

	//----------------------------------------------------------------------------------------------
	//.	delete all aliases for an object and remove from memcache
	//----------------------------------------------------------------------------------------------
	//arg: refModule - module to which owner belongs [string]
	//arg: refModel - type of object this is [string]
	//arg: refUID - UID of object which owns aliases [string]
	//returns: number of aliases deleted [int]

	function deleteAll($refModule, $refModel, $refUID) {
		global $db;
		$delCount = 0;

		$conditions = array();
		$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
		$conditions[] = "refModel='" . $db->addMarkup($refModel) . "'";
		$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";
		$range = $db->loadRange('aliases_alias', '*', $conditions);

		foreach($range as $row) {	
			$model = new Aliases_Alias();
			$model->loadArray($row);
			$model->delete();
			$delCount++;
		}

		return $delCount;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the default alias of an object (ie, the one recorded by the object itsef)
	//----------------------------------------------------------------------------------------------
	//arg: model - object type / db table name [string]
	//arg: UID - unique identifier of object [string]
	//returns: default alias of object, empty string error [string]

	function getDefault($model, $UID) {
		global $db;
		global $session;

		//------------------------------------------------------------------------------------------
		//	check that model is a valid table
		//------------------------------------------------------------------------------------------
		if (false == $db->tableExists($model)) { 
			$session->msgAdmin('No such table:' . $model, 'bad');
			return false;
		}

		//------------------------------------------------------------------------------------------
		//	try load the object from the database
		//------------------------------------------------------------------------------------------
		$objAry = $db->getObject($model, $UID);
		if (0 == count($objAry)) { return ''; }

		//------------------------------------------------------------------------------------------
		//	return the alias, if it has one
		//------------------------------------------------------------------------------------------
		if (false == array_key_exists('alias', $objAry)) { return ''; } 	// no alias field
		if ('' == trim($objAry['alias'])) { return ''; }					// alias field blank 

		return trim($objAry['alias']);										// OK.
	}

	//--------------------------------------------------------------------------------------------------
	//.	find out which object an alias belongs to, provided we know the module and type
	//--------------------------------------------------------------------------------------------------
	//arg: refModule - module name [string]
	//arg: refModel - model type [string]
	//arg: alias - the alias whose owner we'd like to know [string]

	function getOwner($refModule, $refModel, $alias) {
		global $kapenta, $db;

		$alias = strtolower(trim($alias));

		$conditions = array();
		$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
		$conditions[] = "refModel='" . $db->addMarkup($refModel) . "'";
		$conditions[] = "aliaslc='" . $db->addMarkup($alias) . "'";
		$range = $db->loadRange('aliases_alias', '*', $conditions);

		if (count($range) > 0) { 
			//--------------------------------------------------------------------------------------
			//	alias is owned by some object, return its UID
			//--------------------------------------------------------------------------------------
			foreach($range as $record) { return $record['refUID']; }
	
		} else {
			//--------------------------------------------------------------------------------------
			//	apparently not owned by an object, check if it's an action on this module
			//--------------------------------------------------------------------------------------
			//	this prevents action names being claimed by objects, causing ambiguity in URI
			$actList = $kapenta->listActions($refModule);
			foreach($actList as $action) 
				{ if (strtolower($action) == $alias) { return '(action)'; } }

			// nope, nothing claims this name
			return false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	get all aliases associated with a particular record
	//----------------------------------------------------------------------------------------------
	//arg: refModule - module name [string]
	//arg: refModel - model name [string]
	//arg: refUID - UID of the object which owns aliases [string]
	//returns: array of aliases owned by this object, key is UID, value is alias [array]

	function getAll($refModule, $refModel, $refUID) {
		global $db;
		$als = array();

		$conditions = array();
		$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
		$conditions[] = "refModel='" . $db->addMarkup($refModel) . "'";
		$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";
		$range = $db->loadRange('aliases_alias', '*', $conditions);

		foreach($range as $record) { $als[$record['UID']] = $record['alias']; }
		return $als;
	}

	//----------------------------------------------------------------------------------------------
	//.	find an unused alias which begins with the one we'd like
	//----------------------------------------------------------------------------------------------
	//arg: refModule - module name [string]
	//arg: refModel - object type [string]
	//arg: default - alias we'd like best [string]
	//opt: depth - to prevent infinite recursion [int]
	//;	$depth is to prevent infinite recusion if table is full
	//;	used to resolve collisions (eg, two Mr-Smith records)

	function findAvailable($refModule, $refModel, $default, $depth = 0) {
		global $kapenta;

		if ($depth > 50) { return false; }
		$extended = $default . '-' . substr($kapenta->createUID(), 0, 5);
		if (false == $this->getOwner($refModule, $refModel, $extended)) { return $extended; }
		return $this->findAvailable($refModule, $refTable, $default, $depth + 1);
	}

}

?>
