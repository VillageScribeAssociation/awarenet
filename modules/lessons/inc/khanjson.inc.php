<?php

	class Entry {
	
		public $id;
		public $kind;
		public $title;
		public $description;
		public $depth;		
		public $children;
		public $childrenIndex;
		public $parentEntry = null;
		public $hasData = false;
	}

//--------------------------------------------------------------------------------------------------
//	rough version of a JSON parser
//--------------------------------------------------------------------------------------------------

class KJson {

	//----------------------------------------------------------------------------------------------
	//	private members
	//----------------------------------------------------------------------------------------------

	var $depth = 0;				//_	nesting depth of current token [int]
	var $isId = true;			//_	set to true if next token is identifier [bool]
	var $isNextTitle = false;
	var $isNextKind = false;
	var $isNextChildren = false;
	var $arrayCount = 0;
	var $mainEntry = null;
	var $currentEntry = null;
	var $previousLevelEntry = null;
	var $entryArray = null;
	var $objId = 0;
	var $isInsideObject = false;
	var $isNewObject = false;
	
	//----------------------------------------------------------------------------------------------
	//	
	//----------------------------------------------------------------------------------------------

	function parse($fileName) {
		global $kapenta;

		$txt = $kapenta->fileGetContents($fileName);
	}

	function buildOutputStringArray($children) {
		$outputString = "";
		foreach ($children as $e){
			$outputString .= "&nbsp&nbsp" . $this->buildOutputStringEntry($e);
		}
		
		return $outputString;
	}
	
	function buildOutputStringEntry($entry) {
		$outputString = $entry->depth . "/" . $entry->id . "/" . $entry->title . "<br>";
		
		if ($entry->children and 0 < sizeof($entry->children)) {
			$outputString .= $this->buildOutputStringArray($entry->children);
		}
		
		return $outputString;
	}
		
	function buildOutputString() {
		return $this->buildOutputStringEntry($this->mainEntry);
	}

	//----------------------------------------------------------------------------------------------
	//	Split JSON into tokens
	//----------------------------------------------------------------------------------------------
	//note: this assumes double quoted strings

	function tokenize($txt) {

		$state = 'json';				
		$cc = 0;				//%	cursor [int]
		$buffer = '';			//%	holds partial token [string]
		$len = strlen($txt);	//%	loop invariant [string]		
		$char = '';				//%	current char [string]

		$whitespace = array(' ', "\t", "\n", "\r");
		$syntax = array('[', ']', '{', '}', ':', ',', ';');

		for ($cc = 0; $cc < $len; $cc++) {
			$char = substr($txt, $cc, 1);

			switch($state) {
				case 'json':	//	somewhere in plain json string

					//echo "jsonchar: $char<br/>";

					if ('"' == $char) {
						//	start of a string
						//echo "startString<br/>";
						if ('' !== $buffer) {
							$this->add($buffer, 'token');
							$buffer = '';
						}

						$state = 'dq';
						$buffer = $char;
						break;
					}

					if (true == in_array($char, $whitespace)) {
						if ('' !== $buffer) {
							$this->add($buffer, 'token');
							$buffer = '';
						}
						break;
					}

					if (true == in_array($char, $syntax)) {
						if ('' !== $buffer) {
							$this->add($buffer, 'token');
							$buffer = '';
						}
						$this->add($char, $char);
						break;
					}

					//	char is part of a label or value
					$buffer .= $char;

					break;		//..................................................................

				case 'dq':
					//echo "dqchar: $char<br/>";
					if ('"' == $char) {
						$this->add($buffer . $char, 'token');
						$buffer = '';
						$state = 'json';
						//echo "endString<br/>";
					} else {
						$buffer .= $char;
					}
					break;		//..................................................................
			}
		}
		
		return $this->buildOutputString();
	}

	//----------------------------------------------------------------------------------------------
	//	receive a token thrown by tokenise()
	//----------------------------------------------------------------------------------------------

	function add($token, $type) {

		switch($type) {
			case '[':			
			case '{':
				$this->depth++;											//	fallthrough
				$this->isNewObject = true;
				$this->isInsideObject = true;	
				$this->isId = true;
				break;		//......................................................................

			case ']':			
			case '}':	
				$this->depth--;											//	fallthrough
				$this->isInsideObject = false;
				$this->isId = true;
				break;		//......................................................................

			case ',':
				$this->isId = true;
				break;		//......................................................................

			case ':':
				$this->isId = false;
				break;		//......................................................................

			case 'token':
				if (true == $this->isId) {
					$type = 'key';
					$this->isId = false;
				} else {
					$type = 'value';
					$this->isId = true;
				}
				break;		//......................................................................
		}

		$this->lex($token, $type);
	}

	//----------------------------------------------------------------------------------------------
	//	place to receive passed tokens and manage structre / validation of larger objects
	//----------------------------------------------------------------------------------------------

	function createEntry() {
		$entry = new Entry();
		$entry->depth = $this->depth;
		$entry->id = $this->objId++;
		echo "entry created: " . $entry->id . "<br>";
		$entry->children = [];
		$entry->childrenIndex = 0;
		
		return $entry;
	}
	
	function lex($token, $type) {
		if (null == $this->mainEntry) {
			$this->mainEntry = $this->createEntry();
			$this->currentEntry = $this->mainEntry;
		} 

//		if ($this->isNewObject) {
//			$entry = $this->createEntry();
//			$entry->parentEntry = $this->currentEntry;
//			$this->isNewObject = false;
//			if ($this->depth > $this->currentEntry->depth+1) {
//				echo "adding obj: " . $entry->id . " to: " . $this->currentEntry->id . " at: " .$this->currentEntry->childrenIndex . "<br>";
//				$this->currentEntry->children[$this->currentEntry->childrenIndex++] = $entry;
//				$this->previousLevelEntry = $this->currentEntry;
//				$this->currentEntry = $entry;
//			} else if ($this->depth == $this->currentEntry->depth) {
//				echo "adding obj: " . $entry->id . " to: " . $this->previousLevelEntry->id . " at: " . $this->previousLevelEntry->childrenIndex . "<br>";
//				$this->previousLevelEntry->children[$this->previousLevelEntry->childrenIndex++] = $entry;
//				$this->currentEntry = $entry;
//			}
//		}
		
//		if ($this->depth < $this->currentEntry->depth-1) {
//			$this->currentEntry = $this->previousLevelEntry;
//			$this->previousLevelEntry = $this->currentEntry->parentEntry;
//		}
		
//		if (null == $this->entryArray) {
//			$this->entryArray = [];
//		}
				
		if ("key" === $type) {
			if ("\"title\"" === $token) {
				$this->isNextTitle = true;
			} else if ("\"kind\"" === $token) {
				$this->isNextKind = true;
			} else if ("\"children\"" === $token) {
				$this->isNextChildren = true;
			}			
		} else if ("value" === $type) {
			if (true == $this->isNextTitle) {
				$this->currentEntry->title = $token;
				$this->isNextTitle = false;
				$this->currentEntry->hasData = true;
			} else if (true == $this->isNextKind) {
				$this->isNextKind = false;
//				if ("\"Video\"" === $token) {
					$this->currentEntry->kind = $token;
					$this->currentEntry->hasData = true;
//				}
			} else if (true == $this->isNextChildren) {
				$this->currentEntry->hasData = true;
				$this->isNextChildren = false;
			}
							
//			if (2 == $this->completeCount) {
//				$this->entryArray[++$this->arrayCount] = $this->currentEntry;
//				$this->currentEntry = null;
//				for ($i = 0; $i < $this->depth; $i++) {
//					$this->outputString .= "&nbsp&nbsp";
//				}
				
//				$this->outputString .= "<b>" . $this->currentEntry->kind . "</b> - ";
				
//				$this->outputString .= "Title: " . $this->currentEntry->title . "<br>";
				
//				$this->completeCount = 0;
//			}
		}
	}
}

?>
