<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//*	object to interpret shell commands
//--------------------------------------------------------------------------------------------------
//+	very simple, no piping, etc as yet

class Live_CmdInterpreter {

	//----------------------------------------------------------------------------------------------
	//.	members
	//----------------------------------------------------------------------------------------------
	
	var $cmd = '';				//_	name of the command being called [string]
	var $arguments;				//_	array of arguments [array:string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Live_CmdInterpreter($raw) {
		$this->arguments = array();		
		$this->parse($raw . ' ');
		$this->expandAlias();
	}

	//----------------------------------------------------------------------------------------------
	//.	parse string
	//----------------------------------------------------------------------------------------------

	function parse($raw) {
		$curr = '';
		$mode = 'raw';
		while(strlen($raw) > 1) {
			$char = substr($raw, 0, 1);
			$raw = substr($raw, 1);

			switch($mode) {
				case 'raw':
					if ((' ' == $char)||("\t" == $char)||("\n" == $char)||("\r"==$char)) {
						if (strlen($curr) > 0) {
							$this->addArgument($curr);
							$curr = '';
						}	
					} else {
						if ("\"" == $char) {
							if (strlen($curr) > 0) {
								$this->addArgument($curr);
								$curr = '';
							}	
							$curr = $char;
							$mode = 'str';
						} else {
							$curr .= $char;
						}
						
					}
					break;

				case 'str':
					$curr .= $char;
					if ("\"" == $char) {
						$this->addArgument(str_replace("\"", '', $curr));
						$curr = '';
						$mode = 'raw';
					}
					break;
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	add an argument	
	//----------------------------------------------------------------------------------------------

	function addArgument($arg) {
		if ('' == $this->cmd) { 
			$this->cmd = $arg; 
		} else {
			$this->arguments[] = $arg;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	expand any alias
	//----------------------------------------------------------------------------------------------

	function expandAlias() {
		$aliases = new Live_CmdAliases();
		$this->cmd = $aliases->find($this->cmd);			
	}

	//----------------------------------------------------------------------------------------------
	//.	display	
	//----------------------------------------------------------------------------------------------

	function toHtml() {
		$html = "command: " . $this->cmd . "<br/>\n";
		foreach($this->arguments as $idx =>$arg) { $html .= "argument $idx: $arg<br/>"; }
		return $html;
	}
}

?>
