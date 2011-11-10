<?

//--------------------------------------------------------------------------------------------------
//*	object representing user settings (registry section carried around by users, applying to them)
//--------------------------------------------------------------------------------------------------

class Users_Settings {
	
	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;					//_	array of key => base64_encoded_value [string]
	var $loaded = false;			//_	set to true when settings have been loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: serialized registry keys, same as core registry [string]	

	function Users_Settings($data = '') {
		$this->members = array();
		if ('' != $data) {
			$this->expand($data);
			$this->loaded = true;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	unserializes user settings
	//----------------------------------------------------------------------------------------------
	//arg: serialized - serialized user settings, registry format [string]
	//returns: dictionary key => base 64 encoded value [array]

	function expand($serialized) {
		$this->members = array();

		$lines = explode("\n", $serialized);
		foreach($lines as $line) {
			if ((strlen($line) >= 30) && ('#' != substr($line, 0, 1))) {
				$key = trim(substr($line, 0, 30));
				$value = trim(substr($line, 30));
				$this->members[$key] = $value;
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	serializes user settings
	//----------------------------------------------------------------------------------------------
	//returns: string representation of all settings [string]

	function collapse() {
		$serialized = '';						//%	return value [string]
		$thirty = str_repeat(' ', 30);			//%	blank key name [string]

		foreach($this->members as $k => $v) {
			$serialized .= substr($k . $thirty, 0, 30) . $v . "\n";
		}

		return $serialized;	
	}

	//----------------------------------------------------------------------------------------------
	//.	alias of collapse()
	//----------------------------------------------------------------------------------------------
	//returns: string representation of all settings [string]

	function toString() {
		return $this->collapse();
	}

	//----------------------------------------------------------------------------------------------
	//.	retrieve a user setting
	//----------------------------------------------------------------------------------------------
	//arg: key - name of setting [string]
	//returns: value of setting or empty string if not found [string]

	function get($key) {
		$value = '';
		if (false == array_key_exists($key, $this->members)) { return ''; }
		$value = base64_decode($this->members[$key]);
		return $value;
	}

	//----------------------------------------------------------------------------------------------
	//.	store a user setting
	//----------------------------------------------------------------------------------------------
	//arg: key - name of setting [string]
	//arg: value - value to store [string]
	//returns: true on success, false on failure [bool]

	function store($key, $value) {
		if ('public' == $this->role) { return false; }
		$this->members[$key] = base64_encode($value);
		$this->save();
		return true;
	}

}

?>
