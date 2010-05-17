<?

//--------------------------------------------------------------------------------------------------
//*	HOW XML PARSING WORKS
//--------------------------------------------------------------------------------------------------
//+	an XmlEntity object is passed a string containing XML, the first opening tag is found.
//+	if no other opening tags exist within that first tag is used for this element and the remainder
//+	of the string is passed to this XmlEntity's parent.
//+
//+	If another opening tag is incountered before this entities closing tag, a child XmlEntity object
//+	is created and added to this objects collection of children, and the string, starting from the
//+	end of the opening tag is passed to the child object.  The child object will then
//+	pass back the remainder of the string when it is finished.
//+
//+	The cycle then repeats, if an opening tag is found before this entity's closing tag, another
//+	child is created until there are no more to process.  Any remaining xml after this entities
//+	tag is passed to this entities parent.  If this is the root entity, it is stashed in surplus
//+	for the program using this class to deal with.
//+
//+	CDATA sections are stripped out into a global array before processing and replaced by a 
//+	marker in the XML.  This marker is replaced by the CDATA section after the document tree has
//+	been created.
//+
//+	CDATA sections will set $this->cdata to true, and wrapping is added back on toString/toXml
//+	
//--------------------------------------------------------------------------------------------------
//+	TO CONSIDER:
//+	- adding a UID to every entity for easy addressing 
//+	- replace globals with values on the root entity passed by reference
//--------------------------------------------------------------------------------------------------

global $xml_proc_curr;		// ugly, but leave for now
global $xml_cdata_set;

class XmlEntity {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $isRoot = true;		// only for the root entity
	var $parent;

	var $attributes;
	var $children;
	var $comments;			// comments are stripped out by the root entity

	var $value = '';		
	var $type = '';			// html, body, p, a, i, b, etc
	var $surplus = '';		// text after XML
	var $cdata = FALSE;

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: xml - raw XML to parse [string]

	function XmlEntity($xml = '') {
		$this->attributes = array();
		$this->children = array();
		$this->comments = array();
		if ($xml != '') { $this->loadFromString($xml); }
	}

	//----------------------------------------------------------------------------------------------
	//.	convert XML into more XmlEntity objects
	//----------------------------------------------------------------------------------------------
	//opt: xml - raw XML to parse [string]
	//returns: false if XML not parsed [bool]
	//: this is in a recursive relationship with childPassBack()

	function loadFromString($xml = '') {
		global $xml_proc_curr;
		global $xml_cdata_set;		

		//------------------------------------------------------------------------------------------
		//	initialization (only done once)
		//------------------------------------------------------------------------------------------

		$xml = str_replace('<br>', '<br/>', $xml);

		if ($xml != '') { 									// only filled on first request
			$xml_proc_curr = $xml; 							// use global after that
			$xml_cdata_set = $this->stripCDATA($xml);		// store CDATA in this array
			$this->stripComments();							// and ditch the HTML/XML comments
		}

		if (trim($xml_proc_curr) == '') { return false; }

		//------------------------------------------------------------------------------------------
		//	look for and process any opening tag within the XML  passed
		//------------------------------------------------------------------------------------------
		$op = $this->getFirstOpen(0);

		if ($op == false) { 
			//--------------------------------------------------------------------------------------
			//	no XML in string passed, return it to parent if there is one
			//--------------------------------------------------------------------------------------
			return false; 
		}					

		$opentag = $this->expandTag($op['tag']);			// string to array
		$this->type = $opentag['type'];						// set my type 
		$this->attributes = $opentag['attribs'];			// set my attributes

		if (strpos($opStr, '/>' != false)) { 
			//--------------------------------------------------------------------------------------			
			//	self-closing tag, no possibility of child entities
			//--------------------------------------------------------------------------------------
			$xml_proc_curr = substr($xml_proc_curr, $op['end']);
			return true;

		} else {
			//--------------------------------------------------------------------------------------			
			//	might be child entities, look for them
			//--------------------------------------------------------------------------------------
			$xml_proc_curr = substr($xml_proc_curr, $op['end']);
			$this->childPassBack();		// look for child entities

			// done, return to parent
			return true;
		}
	} // end loadFromString()

	//----------------------------------------------------------------------------------------------
	//.	no longer called by children due to stack space issues (consider renaming)
	//----------------------------------------------------------------------------------------------
	//: this is where the structure of XmlEntity objects get built and linked together, important

	function childPassBack() { 
		global $xml_proc_curr;
		global $xml_cdata_set;

		if (trim($xml_proc_curr) == '') { return false; }
		while (true) {

			//--------------------------------------------------------------------------------------
			// find my closing tag (or one of the same type)
			//--------------------------------------------------------------------------------------
			$close = $this->getFirstClose($this->type, 0);
	
			if (false == $close) {
				//----------------------------------------------------------------------------------
				//	no closing tag (invalid XML), let parent continue with any other tags
				//----------------------------------------------------------------------------------
				return false;
			}

			//--------------------------------------------------------------------------------------
			//	check if any opening tags (child entities) exist before closing tag
			//---------------------------------------------------------------------------------------
			$open = $this->getFirstOpen(0);

			//--------------------------------------------------------------------------------------
			//	if no opening tag
			//--------------------------------------------------------------------------------------

			if (false == $open) {
				//----------------------------------------------------------------------------------
				//	look for any text which needs to be added as a value or inline
				//-----------------------------------------------------------------------------------
				$inlineTxt .= trim(substr($xml_proc_curr, 0, $close['start']));
				if (($inlineTxt != '') && (count($this->children) > 0)) {
					$inline = new XmlEntity();
					$inline->isRoot = false;		// add everything up to the
					$inline->parent = $this;		// beginning of the closing tag
					$inline->type = 'inline';		// as a new inline element
					$inline->value = $inlineTxt;
					$index = count($this->children);
					$this->children[$index] = $inline;

				} else { $this->value .= $inlineTxt; }

				// expand any CDATA labels in value
				$CDATA = $this->hasCDATA($this->value);
				if ($CDATA != false) {
					$cdValue = $xml_cdata_set[$CDATA];
					$this->value = str_replace(	'CDATA:' . $CDATA, $cdValue, $this->value);
				}

				// done here, return to parent object
				$xml_proc_curr = substr($xml_proc_curr, $close['end']);	
				return true;
			}

			//--------------------------------------------------------------------------------------
			//	there is an opening tag, if it is before closing tag then there is child entity
			//--------------------------------------------------------------------------------------		
			if ($open['start'] < $close['start']) { 	//	there is (at least one) child entitiy

				//----------------------------------------------------------------------------------
				//	add child element for inline text section (if it actually contains text)
				//----------------------------------------------------------------------------------
				$inlineTxt = trim(substr($xml_proc_curr, 0, $open['start']));
				if ($inlineTxt != '') {
					$inline = new XmlEntity();
					$inline->isRoot = false;
					$inline->parent = $this;
					$inline->type = 'inline';
					$inline->value = $inlineTxt;
					$index = count($this->children);
					$this->children[$index] = $inline;

				}

				//----------------------------------------------------------------------------------
				//	create a new XmlEntiy for the nested element
				//----------------------------------------------------------------------------------
				$child = new XmlEntity();
				$child->isRoot = false;
				$child->parent = $this;
				$index = count($this->children);
				$xml_proc_curr = substr($xml_proc_curr, $open['start']);

				//----------------------------------------------------------------------------------
				//	and have it load itself
				//----------------------------------------------------------------------------------
				$child->loadFromString();
				$this->children[$index] = $child;
				// continue while loop...

			} else {
				//----------------------------------------------------------------------------------
				//	next opening tag is AFTER closing tag, no nested entity
				//----------------------------------------------------------------------------------
				$inlineTxt .= trim(substr($xml_proc_curr, 0, $close['start']));
				if (($inlineTxt != '') && (count($this->children) > 0)) {
					$inline = new XmlEntity();
					$inline->isRoot = false;		// add any text remaining in the
					$inline->parent = $this;		// element as a new inline entity
					$inline->type = 'inline';	
					$inline->value = $inlineTxt;
					$index = count($this->children);
					$this->children[$index] = $inline;

				} else {
					$this->value .= $inlineTxt;		// just a value, not inline

					// expand any CDATA labels in value
					$CDATA = $this->hasCDATA($this->value);
					if ($CDATA != false) {
						$cdValue = $xml_cdata_set[$CDATA];
						$this->value = str_replace(	'CDATA:' . $CDATA, $cdValue, $this->value);
					}

				}

				//----------------------------------------------------------------------------------
				//	nothing more in this element, return to parent
				//----------------------------------------------------------------------------------
				$xml_proc_curr = substr($xml_proc_curr, $close['end']);
				return true;	// exit point of loop

			}

		} // end while

	} // end childPassBlock

	//----------------------------------------------------------------------------------------------
	//.	find first <tag> or <tag /> which is not a closing tag starting from a given position
	//----------------------------------------------------------------------------------------------
	//arg: searchFrom - position to begin searching from in xml_proc_curr
	//returns: array of (start, end) or false on failure [array] [bool]

	function getFirstOpen($searchFrom) {
		global $xml_proc_curr;
		$rv = array();

		while (true) {
			$rv['start'] = strpos($xml_proc_curr, '<', $searchFrom);			// find <
			if (false === $rv['start']) { return false; }			// no more < to expore
			else {
				if (substr($xml_proc_curr, $rv['start'], 2) == '</')  
					{ $searchFrom = $rv['start'] + 2; }				// ignore closing tags
			
				else {
					$rv['end'] = strpos($xml_proc_curr, '>', $rv['start']);	// found
					if ($rv['end'] != false) { 
						$rv['end']++;								// include > in tag
						$rv['tag'] = substr($xml_proc_curr, $rv['start'], $rv['end'] - $rv['start']);
						return $rv; 
					}
					else { return false; }							// no closing brace
				}
			} 
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	find first </tag> starting from a given position
	//----------------------------------------------------------------------------------------------
	//arg: tag - entity type to look for [string]
	//arg: searchFrom - position within xml_proc_curr [int]
	//returns: array of (start, end) or false if not found [array] [bool]

	function getFirstClose($tag, $searchFrom) {
		global $xml_proc_curr;

		$rv = array();
		$rv['start'] = strpos($xml_proc_curr, '</' . $tag . '>', $startPos);
		if (false === $rv['start']) { return false; }
		$rv['end'] = $rv['start'] + strlen($tag) + 3;
		return $rv;
	}

	//----------------------------------------------------------------------------------------------
	//.	divides an opening tag into its components ( <tag x='2' y='5'> into array [type][attribs] )
	//----------------------------------------------------------------------------------------------
	//arg: tag - a self-closing XML entity or opening tag [string]
	//returns: array of type and nested associative array of attributes [array]
	//: test case <something x='223' y=" soe  ">

	function expandTag($tag) {
		$rv = array();
		$rv['attribs'] = array();

		//------------------------------------------------------------------------------------------
		//	first some cleaning
		//------------------------------------------------------------------------------------------
		$find = 	array("'",  '<', '/>', '>', "\t", "\r", "\n");
		$replace = 	array("\"", '',  '',   '',  ' ',  ' ',  ' ' );
		$tag = trim(str_replace($find, $replace, $tag));

		//------------------------------------------------------------------------------------------
		//	get tag name
		//------------------------------------------------------------------------------------------
		$spPos = strpos($tag, ' ');								
		if (false == $spPos) { 
			$rv['type'] = $tag;
			return $rv; 

		} else {
			$rv['type'] = substr($tag, 0, $spPos);
			$tag = trim(substr($tag, $spPos));
		}
	
		//------------------------------------------------------------------------------------------
		//	get tag attributes
		//------------------------------------------------------------------------------------------
		$tagLen = strlen($tag);
		$inStr = false;			// currently in a quoted string?
		$ab = '';				// attrubute name buffer
		$sb = '';				// string buffer

		for ($i = 0; $i < $tagLen; $i++) {
			$cc = substr($tag, $i, 1);

			switch($cc) {
				case ' ': // spaces outside of quoted values are ignored
					if (true == $inStr) { $sb .= $cc; }; 
					break;

				case '=': // values should be quoted
					if (true == $inStr) { $sb .= $cc; }
					break; 

				case "\"":	// toggle in-quoted-string
					if (true == $inStr) { 							// end of quoted string
						$rv['attribs'][$ab] = $sb; $inStr = false; 	// copy to array of attribs
						$ab = '';									// clear attrib name buffer
						$sb = '';									// clear quoted string buffer
					} else { $inStr = true; } 
					break;	

				default:	// add either to attribute name or quoted string
					if (true == $inStr) { $sb .= $cc; }
					else { $ab .= $cc; }
			}	

		}

		//------------------------------------------------------------------------------------------
		//	done
		//------------------------------------------------------------------------------------------
		return $rv;

	}

	//----------------------------------------------------------------------------------------------
	//	return the value of the first element of a given type
	//----------------------------------------------------------------------------------------------

	function getFirst($type) {
		//------------------------------------------------------------------------------------------
		//	check self first
		//------------------------------------------------------------------------------------------
		if ($this->type == $type) { 
			if (count($this->children) > 0) {
				//collape XML and remove indents
				$rv = '';
				foreach($this->children as $index => $child) { $rv .= $child->toString(); }
				$rv = str_replace("<indent>", '', $rv);
				$rv = str_replace("</indent>", '', $rv);
				return $rv;

			} else { 
				return $this->value; 
			}
		}

		//------------------------------------------------------------------------------------------
		//	check all children
		//------------------------------------------------------------------------------------------
		foreach($this->children as $index => $child) {
			$rv = $child->getFirst($type);
			if ($rv != false) { return $rv; }
		}

		return false;
	}

	//----------------------------------------------------------------------------------------------
	//	return all entities of a given type, say all img from a web page
	//----------------------------------------------------------------------------------------------
	
	function getTypeArray($type) {
		$ta = array();
		
		// check self
		if ($this->type == $type) {	$ta[] = $this;	}

		// check children
		foreach($this->children as $index => $child) 
			{ $ta = array_merge($ta, $child->getTypeArray($type)); }

		return $ta;
	}

	//----------------------------------------------------------------------------------------------
	//	make xml string from tree of XmlEntity objects
	//----------------------------------------------------------------------------------------------


	function toXml() {
		$xml = $this->toString();
		$xml = str_replace('<indent>', '', $xml);
		$xml = str_replace('</indent>', '', $xml);
		return $xml;
	}

	function toString() {
		$xml = $this->toStringIndent();
		$xml = str_replace('[indent]', '', $xml);
		return $xml;
	}

	function toStringIndent() {

		$attribs = '';
		foreach($this->attributes as $aName => $aValue) 
			{ $attribs .= ' ' . $aName . "=\"" . $aValue . "\""; }

		$open = "<" . $this->type . $attribs . ">";
		$close = "</" . $this->type . ">";
		$value = $this->value;
		if (true == $this->cdata) { 
			$value = str_replace('<inline>', '', $value);
			$value = str_replace('</inline>', '', $value);
			$value = "<![CDATA[" . $value . "]]>"; 
		}

		if (count($this->children) == 0) {
			if ('' == $this->value) { return "[indent]<" . $this->type . $attribs . "/>\n"; }
			else { return "[indent]" . $open . $value . "</" . $this->type . ">\n"; }

		} else {
			$rv = "[indent]" . $open . $this->value . "\n";
			foreach($this->children as $index => $child) 
				{ $rv .= str_replace('[indent]', "[indent]\t", $child->toStringIndent()); }
			$rv .= "[indent]" . $close . "\n";
			return $rv;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	make html string from tree of XmlEntity objects
	//----------------------------------------------------------------------------------------------

	function toHtml() {
		$xml = $this->toHtmlIndent();
		$xml = str_replace('[indent]', '', $xml);
		return $xml;		
	}

	function toHtmlIndent() {
		//------------------------------------------------------------------------------------------
		//	attributes of this tag
		//------------------------------------------------------------------------------------------
		$attribs = '';
		foreach($this->attributes as $aName => $aValue) { 
			$attribs .= " <font color='red'>" . $aName . "</font>=<font color='blue'>\""
					 . $aValue . "\"</font>"; 
		}

		$open = "&lt;<b>" . $this->type . "</b>" . $attribs . "&gt;";
		$close = "&lt;<b>/" . $this->type . "</b>&gt;";
		$value = "<font color='green'>" . $this->value . "</font>";

		if (count($this->children) == 0) {
			//--------------------------------------------------------------------------------------
			//	simple tag
			//--------------------------------------------------------------------------------------
			if ('' == $this->value) 
				{ return "[indent]&lt;<b>" . $this->type . "</b>" . $attribs . " /&gt;<br/>\n";	}
			else { return "[indent]" . $open . $value . $close . "<br/>\n"; }

		} else {
			//--------------------------------------------------------------------------------------
			//	with nested children
			//--------------------------------------------------------------------------------------
			$rv = "[indent]" . $open . $this->value . "<br/>\n";
			foreach($this->children as $index => $child) { 
				$childStr = $child->toHtmlIndent();
				$rv .= str_replace('[indent]', "[indent]&nbsp;&nbsp;&nbsp;", $childStr); 
			}
			$rv .= "[indent]" . $close . "<br/>\n";
			return $rv;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	remove all elements, leaving only inlines and values
	//----------------------------------------------------------------------------------------------

	function toTxt() {
		$rv = $this->value . ' ';
		foreach($this->children as $index => $child) 
			{ $rv .= $child->toTxt(); }
		return $rv;
	}

	//----------------------------------------------------------------------------------------------
	//	add a child element	// TODO - adding attributes
	//----------------------------------------------------------------------------------------------

	function addChild($type, $value = '', $cdata = FALSE) {
		$newChild = new XmlEntity();
		$newChild->isRoot = false;
		$newChild->type = $type;
		$newChild->value = $value;
		$newChild->cdata = $cdata;
		$index = count($this->children);
		$this->children[$index] = $newChild;
		return $index;
	}

	//----------------------------------------------------------------------------------------------
	//	save as commented PHP (precludes casual viewing/execution)
	//----------------------------------------------------------------------------------------------

	function savePhpCommented($fileName) {
		$fH = fopen($fileName, 'w+');
		if (false == $fH) { return $false; }
		$xml = "<" . "? /" . "*\n" . $this->toString() . "*" . "/ ?" . ">";
		fwrite($fH, $xml);
		fclose($fH);
	}

	//----------------------------------------------------------------------------------------------
	//	strip comments from xml ( <! to > )
	//----------------------------------------------------------------------------------------------

	function stripComments() {
		global $xml_proc_curr;

		$this->comments = array();
		while (true) {
			$next = $this->findFirstComment();
			if (false == $next) { return true; }	// no more comments
			$this->comments[] = $next['comment'];
			$xml_proc_curr = substr($xml_proc_curr, 0, $next['start']) 
						   . substr($xml_proc_curr, $next['end']) ;
		}
	}

	function findFirstComment() {
		global $xml_proc_curr;
		$rv = array();
		$rv['start'] = strpos($xml_proc_curr, '<!');
		if (false === $rv['start']) { return false; }
		$rv['end'] = strpos($xml_proc_curr, ">", $rv['start']);
		if (false === $rv['end']) { return false; }
		$rv['end']++;
		$rv['comment'] = substr($xml_proc_curr, $rv['start'], $rv['end'] - $rv['start']);
		return $rv;
	}

	//----------------------------------------------------------------------------------------------
	//	strip CDATA sections from $xml_proc_curr (global) and add to $xml_cdata_set (global, array)
	//----------------------------------------------------------------------------------------------

	function stripCDATA() {
		global $xml_proc_curr;
		global $xml_cdata_set;

		$sections = array();
		$continue = true;
		$count = 10;

		while ($continue == true) {		
			$count--;									// remove this
			if ($count == 0) { $continue = false; }		// once tested

			$next = $this->getFirstCDATA($xml_proc_curr);
			if ($next == false) { $continue = false; }
			else {					

				//echo "next[start]: " . $next['start'] . " (" . substr($xml_proc_curr, $next['start'], 1) . ")<br/>\n";
				//echo "next[end]: " . $next['end'] . " (" . substr($xml_proc_curr, $next['end'], 1) . ")<br/>\n";
				//echo "next[startd]: " . $next['startd'] . " (" . substr($xml_proc_curr, $next['startd'], 1) . ")<br/>\n";
				//echo "next[endd]: " . $next['endd'] . " (" . substr($xml_proc_curr, $next['endd'], 1) . ")<br/>\n";
				//echo "xml proc curr: $xml_proc_curr <br/>\n";
				//echo "segment: " . substr($xml_proc_curr, $next['start'], $next['end'] - $next['start']) . "<br/>\n";
				//echo "content: " . substr($xml_proc_curr, $next['startd'], $next['endd'] - $next['startd']) . "<br/>\n";
					
				$uid = createUID();
				$section = substr($xml_proc_curr, $next['startd'], $next['endd'] - $next['startd']);
				$sections[$uid] = $section;
				//echo "adding section $uid : $section <br/>\n";

				$xml_proc_curr = substr($xml_proc_curr, 0, $next['start']) 
					 . 'CDATA:' . $uid . substr($xml_proc_curr, $next['end']);	// replace /w marker

				//echo "xml is now: <textarea rows='5' cols='80'>$xml_proc_curr</textarea><br/>\n";

			}
		}

		return $sections;
	}

	//----------------------------------------------------------------------------------------------
	//	find first CDATA section, returns false if none found or array of positions:
	//----------------------------------------------------------------------------------------------
	//	<![CDATA[protected content not treated as xml]>>
	//	^        ^                                  ^  ^
	//  start    startd                             endd, end

	function getFirstCDATA($xml) {
		$startPos = strpos($xml, '<![CDATA[');		
		if ($startPos == false) { return false; }			
		$endPos = strpos($xml, ']]>', $startPos);		
		if ($endPos == false) { $endPos = strlen($xml); }

		$retVal = array(	'start' => $startPos, 
							'end' => $endPos + 3, 
							'startd' => $startPos + 9, 
							'endd' => $endPos			);

		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//	determine whether a string contains a valid cdata label
	//----------------------------------------------------------------------------------------------
	// note that there should only be one CDATA section per value

	function hasCDATA($text) {
		global $xml_cdata_set;
		if (strpos('x' . $text, 'CDATA:') != false) {		// likely?
			foreach($xml_cdata_set as $uid => $value) {		// yes -> check for each UID in table	
				if (strpos('x' . $text, 'CDATA:' . $uid) != false) { return $uid; }
			}
		}
		return false;	// no cdata label found
	}

	//----------------------------------------------------------------------------------------------
	//	make html cdata table
	//----------------------------------------------------------------------------------------------

	function cdataToHtml() {
		global $xml_cdata_set;
		$html = "<table>";
		foreach($xml_cdata_set as $uid => $value) {
			$html .= "\t<tr>\n";
			$html .= "\t\t<td>$uid</td>\n";
			$html .= "\t\t<td>$value</td>\n";
			$html .= "\t</tr>\n";
		}
		$html .= "</table>";
		return $html;
	}

}

?>
