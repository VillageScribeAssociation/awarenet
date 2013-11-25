<?

//--------------------------------------------------------------------------------------------------
//*	HTML Tokenizer / Indexder
//--------------------------------------------------------------------------------------------------
//+	builds and index on an HTML / XML document to allow querying

class Scraper_HTok {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	var $html = '';			//_	raw html/xml source [string]
	var $index = '';		//_	index of document [string]
	var $recordSize = 0;	//_	total size of index records, byes [int]
	var $count = 0;			//_	number of tags [int]

	var $s_ordinal = 10;	//_	size of tag ordinal, bytes [int]
	var $s_start = 10;		//_	size of pointer to start of tag, bytes [int]
	var $s_end = 10;		//_	size of pointer to end of tag, bytes [int]
	var $s_type = 20;		//_	max size of entity type, bytes [int]


	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	
	function Scraper_HTok($html = '') {

		$this->recordSize = $this->s_ordinal + $this->s_start + $this->s_end + $this->s_type;

		if ('' != $html) {
			$this->html = $html;
			$this->index();
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	(re)index the document
	//----------------------------------------------------------------------------------------------

	function index() {
		$cc = '';										//%	current char [string]
		$len = strlen($this->html);						//%	length of raw document [string]
		$mode = 'o';									//%	state of state machine [string]
		$buffer = '';									//%	current tag being indexed [string]
		$token = '';									//%	current token within tag [string]
		$start = 0;										//%	offset of first char of tag [int]
		$tokens = array();								//%	set of tokens inside tag [array]

		$spc = array('<', '>', '=', '/');				//%	special chars [array]
		$wsc = array(' ', "\t", "\n", "\r");			//%	whitespace chars [array]

		for ($i = 0; $i < $len; $i++) {
			$cc = substr($this->html, $i, 1);
			switch($mode) {

				//----------------------------------------------------------------------------------
				//	o - outside of a tag
				//----------------------------------------------------------------------------------
				case 'o':
					if ('<' == $cc) {
						$start = $i;					//	set start position
						$buffer = $cc;					//	initialize buffer
						$tokens[] = $cc;
						$mode = 't';					//	start processing a new tag
					}	
					break;		//..................................................................

				//----------------------------------------------------------------------------------
				//	t - inside of a tag, but not in a quoted string
				//----------------------------------------------------------------------------------
				case 't':

					if ((true == in_array($cc, $wsc)) && ('' != $token)) {		
						$tokens[] = $token;					//	hit whitespace, end of token
						$token = '';						//	clear token buffer
					} else { 
						if (true == in_array($cc, $spc)) {
							if ('' != $token) {				
								$tokens[] = $token;			//	hit special char, end of token
								$token = '';				//	clear token buffer
							}
							$tokens[] = $cc;				//	and a token for the special char
						} else { $token .= $cc; }
					}
					

					$buffer = $buffer . $cc;					//	add current character to buffer
					switch($cc) {
						case "'":	$mode = 's';	break;		//	entering a single quoted string
						case "\"":	$mode = 'd';	break;		//	entering a double quoted string
						case ">":
							$mode = 'o';						//	end of this tag, add to index
							$this->addTag($buffer, $start, $i);
							foreach($tokens as $tk) {
								echo "token: " . htmlEntities($tk, ENT_QUOTES, "UTF-8") . "<br/>\n";
							}
							$tokens = array();
							$token = '';
							break;
					}
					break;		//..................................................................

				//----------------------------------------------------------------------------------
				//	s - inside of a single quoted string
				//----------------------------------------------------------------------------------
				case 's':
					$buffer .= $cc;								//	add current character to buffer
					$token .= $cc;								//	and to token buffer
					if ("'" == $cc) { $mode = 't'; }			//	end of single quoted string
					break;		//..................................................................

				//----------------------------------------------------------------------------------
				//	d - inside of a double quoted string
				//----------------------------------------------------------------------------------
				case 'd':
					$buffer = $buffer . $cc;					//	add current character to buffer
					$token .= $cc;								//	and to token buffer
					if ("\"" == $cc) { $mode = 't'; }			//	end of single quoted string
					break;		//..................................................................

			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	add a tag to the index
	//----------------------------------------------------------------------------------------------
	//arg: raw - raw tag [string]
	//arg: start - offset of first char [int]
	//arg: end - offset of last char [int]

	function addTag($raw, $start, $end) {
		echo "found raw tag: " . htmlentities($raw, ENT_QUOTES, "UTF-8") . "<br/>";
	}

}

?>
