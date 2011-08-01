<?

//--------------------------------------------------------------------------------------------------
//*	an experimental wikicode parser
//--------------------------------------------------------------------------------------------------
//+	each document has at least one section, 'abstract' and any defined beneath that

//+	sections are represented as dictionaries with the following format:
//+		id - index of this section in array [int]
//+		parent - index of parent, -1 for root [int]
//+		title - section title [string]
//+		wikicode - raw wikicode [string]
//+		html - wikicode parsed into HTML [string]

class Wiki_Parser {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	var $wikicode = '';				//_	raw wikicode to be parsed [string]
	var $sections;					//_	array of document sections [array]
	var $debug = false;				//_	set to true to enable debugging mode [bool]
	var $log = '';					//_	debug log [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: wikicode - raw wiki document [string]
	//opt: debug - enable debugging mode [bool]

	function Wiki_Parser($wikicode = '', $debug = false) {
		$this->sections = array();
		$this->wikicode = $wikicode;
		$this->debug = $debug;
		if ('' != $this->wikicode) { 
			$this->parseDocument();
			//$this->parseSections();
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	parse document into sections
	//----------------------------------------------------------------------------------------------

	function parseDocument() {
		$this->sections = array();

		$ordinals = array(0, 0, 0, 0, 0, 0);	

		$this->sections[0] = array(
			'id' => 0,
			'ordinal' => '',
			'depth' => 0,
			'title' => 'Abstract',
			'wikicode' => '',
			'html' => ''
		);

		$currSection = 0;

		$lines = explode("\n", $this->wikicode);
		foreach($lines as $line) {
			if ('==' == substr($line . '  ', 0, 2)) {
				//----------------------------------------------------------------------------------
				//	get depth of this new section
				//----------------------------------------------------------------------------------
				$level = 6;				//%	loop counter, start at max depth [int]
				$depth = 0;				//%	depth of next section (1 - 5) [int]
				$title = $line;			//%	section title [string]
				$ordinal = '';			//%	section numbering [string]

				while ($level >= 0) {
					$match = str_repeat('=', ($level + 1));				//%	line begins [string]

					if (substr($line, 0, strlen($match)) == $match) {
						$this->log("line begins: $match line: $line level: $level");
						$title = str_replace($match, '', $title);
						$depth = $level;

						//--------------------------------------------------------------------------
						// increment ordinal at this depth
						//--------------------------------------------------------------------------
						$ordinals[$depth] += 1;
						$this->log("ordinal $depth := " . $ordinals[$depth]);

						//--------------------------------------------------------------------------
						// set all ordinals greater than this to 0
						//--------------------------------------------------------------------------
						while ($level < (count($ordinals) - 1)) {
							$level++;
							$ordinals[$level] = 0;
						}

						$ordinal = implode('.', $ordinals);				// eg, "0.3.1.4.0.0"
						$ordinal = substr($ordinal, 2);					// remove leading 0
						$ordinal = str_replace('.0', '', $ordinal);		// remove trailing 0s

						break;
					}
					$level--;
				}

				//----------------------------------------------------------------------------------
				//	start a new section
				//----------------------------------------------------------------------------------
				$this->sections[$currSection + 1] = array(
					'id' => count($this->sections),
					'depth' => $depth,
					'ordinal' => $ordinal,
					'title' => $title,
					'wikicode' => '',
					'html' => ''
				);

				$currSection++;

			} else {
				//----------------------------------------------------------------------------------
				//	add line to current section
				//----------------------------------------------------------------------------------
				$this->sections[$currSection]['wikicode'] .= $line . "\n";

			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	debugging log 
	//----------------------------------------------------------------------------------------------

	function log($msg) {
		if (true == $this->debug) {
			$this->log .= $msg . "<br/>\n";
		}
	}

}

?>
