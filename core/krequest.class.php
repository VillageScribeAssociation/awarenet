<?

//--------------------------------------------------------------------------------------------------
//	this object represents the HTTP request made of the web server
//--------------------------------------------------------------------------------------------------

class KRequest { 

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $raw;		// the REQUEST_URI as reported by Apache [string]
	var $parts;		// separated by forwardslash [array]
	var $module;	// module requested [string]
	var $TOaction;	// action to take on this module [string]
	var $ref;		// object on which the action is taken [string]
	var $args;		// arguments [array]
	var $mvc;		// module, model, action parts [array]

	//----------------------------------------------------------------------------------------------
	//	constructor (breaks up request)
	//----------------------------------------------------------------------------------------------

	function KRequest($raw) {
		global $registry;

		$this->raw = $raw;											// store for future reference
		$this->module = $registry->get('kapenta.modules.default');	// see setup.inc.php
		$this->action = 'default';									// they should all have one
		$this->ref = '';

		if ('/' == substr($raw, 0, 1)) { $raw = substr($raw, 1); }	// remove leading slash		
		$this->parts = explode('/', $raw);							// split on forwardslashes

		$this->getRequestArguments();								// trim out arguments
		$this->splitRequestURI();									// interpret the rest

	}

	//----------------------------------------------------------------------------------------------
	//.	get arguments from request URL (refences to records, variables, switches, etc)
	//----------------------------------------------------------------------------------------------
	//;	arguments have form /var_value/ where value may contain underscores, variable names cannot.

	function getRequestArguments() {
		$this->args = array();
		$this->mvc = array();

		foreach($this->parts as $part) {
		  if (strlen($part) > 0) {								// non empty
		    if (false !== strpos($part, '_')) { 				// contains an underscore
				$bits = explode('_', $part, 2);					// split at first underscore
				$this->args[$bits[0]] = $bits[1];

		    } else { $this->mvc[] = strtolower($part); }	// no underscore (module or action)
		  }	
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	break a browser request into controller, method, reference and param parts
	//----------------------------------------------------------------------------------------------
	//;	no information is OK, *wrong* information makes 404
	//;
	//;	(1) is the first part the name of a module?
	//;		(a) yes, set this->module
	//;
	//;		(b) no, is the first part an action on the default module?
	//;			(i) yes, set this->module
	//;			(ii) no, is the first part a valid reference?
	//;
	//;	note that this->getRequestArguments() should have been called first
	//; TODO: this could use some tidying / simplification

	function splitRequestURI() {
		global $kapenta;

		$modList = $kapenta->listModules();
		$mvcCount = count($this->mvc);				// number of parts which are not arguments

		if (0 == $mvcCount) { return false; }		// nothing to do

		$partNum = 0;
		if (true == in_array($this->mvc[$partNum], $modList)) {	
													
			$this->module = $this->mvc[$partNum];	// check if the first part names a module
			$partNum += 1;
		}											//else continue with the default module
		$actList = $kapenta->listActions($this->module);
		if (($partNum < $mvcCount) 					//check if this part is an action
				&& (true == in_array($this->mvc[$partNum] . '.act.php', $actList))){	
			$this->action = $this->mvc[$partNum];
			$partNum += 1;
		}											//else continue with default action
		if ($partNum < $mvcCount){					//if there are more parts it must be a reference
			$this->ref = $this->mvc[$partNum];
		}
	}

	/*
	function splitRequestURI() {
		global $kapenta;			// may in future be replaced with 'system' object

		$modList = $kapenta->listModules();
		$mvcCount = count($this->mvc);	// number of parts which are not arguments

		if (0 == $mvcCount) { return false; }	// nothing to do

		if (in_array($this->mvc[0], $modList)) {	// is first part a name of a module?
			//--------------------------------------------------------------------------------------
			// module is explicitly named, check next part against actions of that module
			//--------------------------------------------------------------------------------------
			$this->module = $this->mvc[0];
			$actList = $kapenta->listActions($this->mvc[0]);

			if (($mvcCount > 1) && (in_array($this->mvc[1] . '.act.php', $actList))) {
				//----------------------------------------------------------------------------------
				// action is explicitly named, check if there is an alias or UID
				//----------------------------------------------------------------------------------
				$this->action = $this->mvc[1];

				if ($mvcCount > 2) {
					//------------------------------------------------------------------------------
					// reference is given (presumed alias, UID, etc)
					//------------------------------------------------------------------------------
					$this->ref = $this->mvc[2]; 
				}

			} else {
				//----------------------------------------------------------------------------------
				// action is not explicitly named, use default
				//----------------------------------------------------------------------------------
				if ($mvcCount > 1) { $this->ref = $this->mvc[1]; }
			}

		} else {
			//--------------------------------------------------------------------------------------
			// module is not explicitly named, check against actions on default module
			//--------------------------------------------------------------------------------------
			$actList = $kapenta->listActions($kapenta->defaultModule);

			if (in_array($this->mvc[0] . '.act.php', $actList)) {
				//----------------------------------------------------------------------------------
				// method is explicitly named, any remaining part must be a reference to something
				//----------------------------------------------------------------------------------
				$this->action = $this->mvc[0];

				if ($mvcCount > 1) {
					//------------------------------------------------------------------------------
					// reference is given
					//------------------------------------------------------------------------------
					$this->ref = $this->mvc[1];
				}

			} else {
				//----------------------------------------------------------------------------------
				// assuming default module, default action, request must be a reference
				//----------------------------------------------------------------------------------
				$this->ref = $this->mvc[0];
			}
		}		
		
	}
	*/

	//----------------------------------------------------------------------------------------------
	//.	make array as used by previous Kapenta versions
	//----------------------------------------------------------------------------------------------

	function toArray() {
		$request = array();
		$request['module'] = $this->module;
		$request['action'] = $this->action;
		$request['ref'] = $this->ref;
		$request['args'] = $this->args;
		return $request;
	}

}

?>
