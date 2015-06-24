<?

//--------------------------------------------------------------------------------------------------
//*	this object represents the HTTP request made of the web server
//--------------------------------------------------------------------------------------------------

class KRequest { 

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $raw;				//_	the REQUEST_URI as reported by Apache [string]
	var $parts;				//_	separated by forwardslash [array]
	var $module;			//_	module requested [string]
	var $action;			//_	action to take on this module [string]
	var $ref;				//_	object on which the action is taken [string]
	var $args;				//_	arguments [array]
	var $mvc;				//_	module, model, action parts [array]
	var $local = false;		//_	set to true if client is on same subnet as server [bool]
	var $agent = '';		//_ requesting user agent [string]
	var $httpArgs;			//_ set of http arguments [array]

	//----------------------------------------------------------------------------------------------
	//	constructor (breaks up request)
	//----------------------------------------------------------------------------------------------

	function KRequest($raw) {
		global $kapenta;

		//------------------------------------------------------------------------------------------
		//	first detect if we are running from a subdirectory
		//------------------------------------------------------------------------------------------
		$subdir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']); 
		if ('/' !== $subdir) {
			$subdir = substr($subdir, 1);
			$raw = str_replace($subdir, '', $raw);
		}

		//------------------------------------------------------------------------------------------
		//	first detect if we are running from a subdirectory
		//------------------------------------------------------------------------------------------

        $this->args = array();

		$this->raw = $raw;											// store for future reference
		$this->module = $kapenta->registry->get('kapenta.modules.default');	// see setup.inc.php
		$this->action = 'default';									// they should all have one
		$this->ref = '';

		if ('/' == substr($raw, 0, 1)) { $raw = substr($raw, 1); }	// remove leading slash		
		$this->parts = explode('/', $raw);							// split on forwardslashes

		$this->getRequestArguments();								// trim out arguments
		$this->splitRequestURI();									// interpret the rest
		$this->local = $this->checkIfLocal();						// check subnet of client
		$this->httpArgs = $this->getHTTPArguments();				// collect http request data
	}
	
	//----------------------------------------------------------------------------------------------
	//.	get http arguments from Server
	//----------------------------------------------------------------------------------------------
	function getHTTPArguments() {
		$requestURI = $_SERVER['REQUEST_URI'];
		$requestQuery = $_SERVER['QUERY_STRING'];
		$remoteAddr = $_SERVER['REMOTE_ADDR'];
		$remotePort = $_SERVER['REMOTE_PORT'];
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		$args = array(
			'method' => $requestMethod,
			'uri' => $requestURI,
			'query' => $requestQuery,
			'remoteAddr' => $remoteAddr,
			'remotePort' => $remotePort,
		);
		
		return $args;
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

	//----------------------------------------------------------------------------------------------
	//.	discover if the client IP is within our network
	//----------------------------------------------------------------------------------------------
	//returns: true if client is local, false if not or on error [bool]

	function checkIfLocal() {
		global $registry;

		if ('127.0.0.1' == $_SERVER['REMOTE_ADDR']) { return true; }
		if ('127.0.1.1' == $_SERVER['REMOTE_ADDR']) { return true; }

		$snStart = $registry->get('kapenta.snstart');
		$snEnd = $registry->get('kapenta.snend');

		if (('' == $snStart) || ('' == $snEnd)) { return false; }

		$snIStart = $this->ipToInt($snStart);
		$snIEnd = $this->ipToInt($snEnd);

		$clientI = $this->ipToInt($_SERVER['REMOTE_ADDR']);

		if (($clientI >= $snIStart) && ($clientI <= $snIEnd)) { return true; }

		$snIStart = $this->ipToInt('196.23.167.0');
		$snIEnd = $this->ipToInt('196.23.167.255');

		if (($clientI >= $snIStart) && ($clientI <= $snIEnd)) { return true; }

		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	convert an IP address to integer format
	//----------------------------------------------------------------------------------------------
	//arg: ip - IP address in dotted decimal notation, four octets, eg 127.0.0.1 [string]
	//returns: ip address as a base 10 integer [int]

	function ipToInt($ip) {
		$parts = explode('.', trim($ip));
		if (4 != count($parts)) { return 0; }
		$num = 0
		 + ((int)trim($parts[3]))
		 + ((int)trim($parts[2]) * 256)
		 + ((int)trim($parts[1]) * 65536)
		 + ((int)trim($parts[0]) * 16777216);
		return $num;
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
	//.	guess browser profile on first request
	//----------------------------------------------------------------------------------------------
	//returns: device profile name [string]

	function guessDeviceProfile() {
		$deviceProfile = 'desktop';

		$specific = array();

		$specific['colpad2'] = ''
		 . "Mozilla/5.0 (Linux; U; Android 4.0.3; en-us; Full AOSP on Rk29sdk Build/IML74K)"
		 . " AppleWebKit/534.30 (KHTML, like Gecko)"
		 . " Version/4.0 Safari/534.30";



		if (true == array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
			$this->agent = $_SERVER['HTTP_USER_AGENT'];
			if (preg_match('/iPhone|Android|Blackberry/i', $this->agent)) {
				$deviceProfile = 'mobile';
			}
		}

		foreach($specific as $profile => $ua) {
			if ($ua == $this->agent) { $deviceProfile = $profile; }
		}

		$specific['colpad2'] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.34 Safari/534.24";

		foreach($specific as $profile => $ua) {
			if ($ua == $this->agent) { $deviceProfile = $profile; }
		}

		return $deviceProfile;
	}

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
