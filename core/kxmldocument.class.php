<?

//--------------------------------------------------------------------------------------------------
//*	a simple XML parser for converting XML documents into other objects
//--------------------------------------------------------------------------------------------------
//+	This parser first finds all <tags> within the raw document, collect them into the tag array
//+	then folds the tag array into a doubly linked tree of XML entities.
//+
//+	Tags array contains the following metadata:
//+	 [start] => offset within raw document [int]
//+	 [content] => the tag itself [string]
//+	 [length] => length of content [int]
//+	 [type] => name of tag, eg p, img, body, etc [string]
//+	 [attributes] => associative array [array]
//+	 [category] => open, close or selfclose [string]
//+	 [entity] => XML entity this is part of [int]
//+
//+	Entities array contains the following metadata:
//+  [handle] => handle to this entity [int]
//+  [category] => pair or single? [bool]
//+  [parent] => handle to parent, 0 if root [int]
//+  [type] => entity type [string]
//+  [start] => offset within raw [int]
//+  [length] => to end of closing tag [int]
//+  [vstart] => value offset within raw [int]
//+  [vlength] => to end of value [int]
//+  [children] => array of pointers to children [array]
//+  [attributes] => associative array of attributes ('foo' => 'bar;) [array]
//+  [tag1] => array index of first tag [int]
//+  [tag2] => array index of second tag (if any) [int]

//+
//+	handles to entities are always greater than 0
//+ 

class KXmlDocument {
	
	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	var $raw;				// raw XML document [string]
	var $entities;			// XML entities [array]
	var $tags;				// <tags>all of them</tags> [array]
	var $loaded = false;	// set to true when an XML document is successfully loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raw - a complete XML document or filename [string]
	//opt: isFile - set to true raw is a filename [bool]

	function KXmlDocument($raw = '', $isFile = false) {
		global $kapenta;
		$this->entities = array();
		$this->tags = array();

		if ((true == $isFile) && ('' != $raw)) {
			// try to load XML from file
			$raw = $kapenta->fs->get($raw, false, true);
			if (false == $raw) { $raw = ''; }
		}

		if ($raw != '') {
			$this->raw = $raw;
			$this->parseAllTags();
			$this->linkEntities(0, 0);
			$this->clearTags();
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	parse entities into doubly linked tree (also doubly linked list)
	//----------------------------------------------------------------------------------------------

	function parseAllTags() {
		if ('' == $this->raw) { return false; }
		$this->entities = array();		//	clear entities array 

		//------------------------------------------------------------------------------------------
		//	some local variables
		//------------------------------------------------------------------------------------------
		$mode = 'outside';				//%	state of state machine [string]
		$rawLen = strlen($this->raw);	//%	length or raw document [int]
		$this->tags = array();			//%	individual tags [array]
		$buffer = '';					//%	holds current item being read

		$currTag = array();				//%	current tag
		$currTagId = 0;					//%	array index of current tag
	
		$inType = false;				//%	are we currently reading a type name? [bool]
		$inAttrib = false;				//%	are we currently reading an attribute name? [bool]
		$inDq = false;					//%	are we currently reading a dq string? [bool]
		$inSq = false;					//%	are we currently reading an sq string? [bool]

		//------------------------------------------------------------------------------------------
		//	first parse for tags (not entities)
		//------------------------------------------------------------------------------------------

		for ($i = 0; $i < $rawLen; $i++) {
			$char = substr($this->raw, $i, 1);	//%	current char

			//--------------------------------------------------------------------------------------
			//	outside mode (not within an entity or value)
			//--------------------------------------------------------------------------------------
			if (('outside' == $mode) && ('<' == $char)) {
				// check this isn't CDATA
				if ('<![CDATA[' == substr($this->raw, $i, 9)) {
					// we've discovered a CDATA section, ignore it
					$endCd = strpos($this->raw, ']]>', $i);
					if (false == $endCd) { $i = strlen($this->raw); }	// unclosed CDATA
					else { $i = $endCd; }								// skip to end of CDATA
				} else {
					// we've discovered a tag, make a new array for it
					$mode = 'tag';
					$currTag = array('start' => $i, 'content' => $char, 'length' => 0);
					$char = '';	// don't need this any more, will only confuse other modes
				}
			}

			//--------------------------------------------------------------------------------------
			//	tag mode (inside a tag, but not any spacific part of one)
			//--------------------------------------------------------------------------------------
			if ('tag' == $mode) {
				if (">" == $char) {
					//	we've reached the end of the tag, add to tag array and process further
					$currTag['content'] .= $char;
					$currTag['length'] = strlen($currTag['content']);

					//	check it's not a comment or XML typedef
					$allOk = true;
					if ('<?xml' == substr(trim($currTag['content']), 0, 5)) { $allOk = false; }
					if ('<!--' == substr(trim($currTag['content']), 0, 4)) { $allOk = false; }

					//	add it to the collection
					if (true == $allOk) {
						$this->tags[$currTagId] = $currTag;		// 	add to tags array
						$this->parseTag($currTagId);			//	extract attributes, etc
						$currTagId++;
					}
					$mode = 'outside';

				} else {
					// not at end yet, check for state changes to string modes
					if ("'" == $char) { $mode = 'sqstring'; }
					if ("\"" == $char) { $mode = 'dqstring'; }
				}
				$currTag['content'] .= $char;					
				$char = '';	// don't need this any more, will only confuse other modes
			}

	
			//--------------------------------------------------------------------------------------
			//	sq double string mode (inside tag)
			//--------------------------------------------------------------------------------------
			if ('sqstring' == $mode) { 
				$currTag['content'] .= $char;
				if ("'" == $char) { $mode = 'tag'; }
			}

			//--------------------------------------------------------------------------------------
			//	dq double string mode (inside tag)
			//--------------------------------------------------------------------------------------
			if ('dqstring' == $mode) { 
				$currTag['content'] .= $char;
				if ("\"" == $char) { $mode = 'tag'; }
			}

		}
	}

	//----------------------------------------------------------------------------------------------
	//.	parse a single tag for type, attributes and category
	//----------------------------------------------------------------------------------------------
	//arg: id - array index of tag we'd like to know more about [int]

	function parseTag($id) {
		$this->tags[$id]['type'] = '';					//	tag name, eg img, p, body, etc
		$this->tags[$id]['attributes'] = array();		//	associative array
		$this->tags[$id]['category'] = 'open';			//	may be changed to close or selfclose
		$this->tags[$id]['entity'] = -1;				//	claimed by an entity?

		$tag = $this->tags[$id];						// local cache

		//------------------------------------------------------------------------------------------
		// remove angle brackets, look for opening and closing slashes to guess category
		//------------------------------------------------------------------------------------------
		$content = trim(substr($tag['content'], 1, $tag['length'] - 2));
		$contentLen = strlen($content);
		if ('/' == substr($content, 0, 1)) { 
			$content = substr($content, 1);						//	remove the opening slash
			$this->tags[$id]['category'] = 'close'; 			//	set category
		}
		if ('/' == substr(strrev($content), 0, 1)) { 
			$content = substr($content, 0, strlen($content) - 1);	//	remove the closing slash
			$this->tags[$id]['category'] = 'selfclose'; 		//	set category
		}

		//------------------------------------------------------------------------------------------
		// parse for pieces of tag
		//------------------------------------------------------------------------------------------
		$pieces = array();
		$piece = '';
		$mode = 'inside';
		
		for ($i = 0; $i < $contentLen; $i++) {
			$char = substr($content, $i, 1);
			$ws = false;	//%	is this char some witespace [bool]
			if ((' ' == $char)||("\n" == $char)||("\r" == $char)||("\t" == $char)) { $ws = true; }

			//--------------------------------------------------------------------------------------
			//	look for tokens, equal signs, single and double quoted strings
			//--------------------------------------------------------------------------------------
			if ('inside' == $mode) {
				if (false == $ws) {
					switch($char) {
						case "\"":	$mode = 'dqstring';		break;
						case "'":	$mode = 'sqstring';		break;
						case "=":	$mode = 'equals';		break;
						default:	$mode = 'token';		break;
					}
				}
			}

			//--------------------------------------------------------------------------------------
			//	token mode: continue to end of token (whitespace or '=')
			//--------------------------------------------------------------------------------------
			if ('token' == $mode) {
				// if whitespace then add this token to pieces, we're done
				if (true == $ws) { $pieces[] = $piece; $piece = ''; $mode = 'inside'; }
				// if eq sign then add this token to pieces, we're done
				if ('=' == $char) { $pieces[] = $piece; $piece = ''; $mode = 'equals'; }
				// if mode is still token
				if ('token' == $mode) { $piece .= $char; }
			}

			//--------------------------------------------------------------------------------------
			//	double quoted string, continue to end of dq string
			//--------------------------------------------------------------------------------------
			if ('dqstring' == $mode) {
				$piece .= $char;
				if (("\"" == $char) && ("\"" != $piece)) 
					{ $pieces[] = $piece; $piece = ''; $mode = 'inside'; }
			}

			//--------------------------------------------------------------------------------------
			//	single quoted string, continue to end of sq string
			//--------------------------------------------------------------------------------------
			if ('sqstring' == $mode) {
				$piece .= $char;
				if (("'" == $char)&&("'" != $piece))
					{ $pieces[] = $piece; $piece = ''; $mode = 'inside'; }
			}

			//--------------------------------------------------------------------------------------
			//	equals (always a single character)
			//--------------------------------------------------------------------------------------
			if ('equals' == $mode) { $pieces[] = '='; $piece = ''; $mode = 'inside'; }

		}

		if ('' != $piece) { $pieces[] = $piece; }			// clear anything left in the buffer

		//------------------------------------------------------------------------------------------
		// look for type and attributes
		//------------------------------------------------------------------------------------------
		$numPieces = count($pieces);
		if ($numPieces > 0) {
			$this->tags[$id]['type'] = $pieces[0];			// first piece is always type
			$pieces[0] = '';
		}

		foreach($pieces as $pid => $piece) {
			if ($pid < ($numPieces  + 2)) {
				$one = $piece; $two = ''; $three = '';
				if (true == array_key_exists(($pid + 1), $pieces)) { $two = $pieces[($pid + 1)]; }
				if (true == array_key_exists(($pid + 2), $pieces)) { $three = $pieces[($pid + 2)]; }

				if (('=' == $two) && ('' != $one) && ('' != $three)) 
					{ $this->tags[$id]['attributes'][$one] = $three; }
			}
		}		
		
	}

	//----------------------------------------------------------------------------------------------
	//.	make entities tree
	//----------------------------------------------------------------------------------------------
	//arg: parentId - array index of parent entity, initially 0 [int]
	//arg: tagId - array index of the first tag, initially 0 [int]
	//returns: true on success, false on failure [bool]

	function linkEntities($parentId, $tagId) {
		if (0 == count($this->tags)) { return false; }
		if (false == array_key_exists($tagId, $this->tags)) { return false; }

		$tag = $this->tags[$tagId];
		if (-1 != $tag['entity']) { return false; }
		//echo "linking tag $tagId to parent $parentId (tt: " . $tag['type'] . ")<br/>\n";

		switch ($tag['category']) {
			case 'open':	
				//----------------------------------------------------------------------------------
				// add as a new XML entity
				//----------------------------------------------------------------------------------
				$newEntity = $this->makeNewEntity($parentId, $tagId);
				$found = false;

				//----------------------------------------------------------------------------------
				// go through tags recursively from this point on until we find the closing tag
				//----------------------------------------------------------------------------------
				$numTags = count($this->tags);
				for ($nextTagId = $tagId + 1; $nextTagId < $numTags; $nextTagId++) {
					$nextTag = $this->tags[$nextTagId];
					if  ( ($nextTag['type'] == $tag['type']) 
						&& (-1 == $nextTag['entity'])
						&& ('close' == $nextTag['category']) ) {
	
						//--------------------------------------------------------------------------
						//	we've found the closing tag
						//--------------------------------------------------------------------------
						// link entity and tag
						$this->tags[$nextTagId]['entity'] = $newEntity['handle'];
						$newEntity['tag2'] = $nextTagId;
						// set value start and length (end of open tag to start of close tag)
						$newEntity['vstart'] = $newEntity['start'] + $newEntity['length'];
						$newEntity['vlength'] = $nextTag['start'] - $newEntity['vstart'];
						// set entity length 
						$newEntity['length'] += ($nextTag['length'] + $newEntity['vlength']);
						// done with loop
						$numTags = 0;
						$found = true;

					} else {
						//--------------------------------------------------------------------------
						//	we've found some other tag, try add it as a child entity of this one
						//--------------------------------------------------------------------------
						$this->linkEntities($newEntity['handle'], $nextTagId);

					}
				}

				if (false == $found) { /* should throw error here */ }
				$newEntity['children'] = $this->entities[$newEntity['handle']]['children'];
				$this->entities[$newEntity['handle']] = $newEntity;			// and we're done :-)
				break;

			case 'selfclose':
				//----------------------------------------------------------------------------------
				// add as a new XML entity
				//----------------------------------------------------------------------------------
				$newEntity = $this->makeNewEntity($parentId, $tagId);		//	convert to entity
				$this->tags[$tagId]['entity'] = $newEntity['handle'];		//	link child object
				$this->entities[$newEntity['handle']] = $newEntity;			//	and we're done :-)
				break;

			case 'close':	break;		// shouldn't happen, maybe add error checking here
			default:		break; 		// shouldn't happen, maybe add error checking here
		}

		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove excess data from the tags array once it has been added to entities
	//----------------------------------------------------------------------------------------------

	function clearTags() {
		foreach($this->tags as $tagId => $tag) {
			$newTag = array(
				'start' => $tag['start'],
				'length' => $tag['length'],
				'category' => $tag['category'],
				'entity' => $tag['entity']
			);
			$this->tags[$tagId] = $newTag;
		}
	}	

	//----------------------------------------------------------------------------------------------
	//.	make a new XML entity, add it to entities array and return it
	//----------------------------------------------------------------------------------------------
	//arg: parentId - array index of parent entity [int]
	//arg: tagId - array index of a tag to base this entity on [int]
	//returns: xml entity array, or false on failure [array][bool]

	function makeNewEntity($parentId, $tagId) {
		if (false == array_key_exists($tagId, $this->tags)) { return false; }
		if ((0 != $parentId) && (false == array_key_exists($parentId, $this->entities))) 
			{ return false; }

		//------------------------------------------------------------------------------------------
		//	make the entity array
		//------------------------------------------------------------------------------------------
		$tag = $this->tags[$tagId];
		$category = 'pair';
		if ('selfclose' == $tag['category']) { $category = 'single'; }

		$entity = array(
			'handle' => (count($this->entities) + 1), 
			'category' => $category,
			'parent' => $parentId, 
			'type' => $tag['type'], 
			'start' => $tag['start'], 
			'length' => $tag['length'],
			'valuestart' => -1,
			'valuelength' => 0,
			'children' => array(),
			'attributes' => $tag['attributes'],
			'tag1' => $tagId,
			'tag2' => -1,
		 );

		//------------------------------------------------------------------------------------------
		//	link tag and parent to the new entity, add to entities array and return it
		//------------------------------------------------------------------------------------------
		$this->tags[$tagId]['entity'] = $entity['handle'];
		if (0 != $parentId) { $this->entities[$parentId]['children'][] = $entity['handle']; }
		$this->entities[$entity['handle']] = $entity;					

		return $entity;
	}

	//----------------------------------------------------------------------------------------------
	//.	get details of entity given handle
	//----------------------------------------------------------------------------------------------
	//arg: handle - array index of an entity [int]	
	//returns: array of handles metadata, or false on failure [array][bool]

	function getEntity($handle) {
		if (false == array_key_exists($handle, $this->entities)) { return false; }
		$entity = $this->entities[$handle];
		$entity['value'] = $this->getInnerXml($handle);
		return $entity;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the value of an entity
	//----------------------------------------------------------------------------------------------
	//arg: handle - array index of an entity [int]
	//opt: tags - include tags [bool]
	//returns: contents, optionally wrapped in original tags [string]

	function getInnerXml($handle, $tags = false) {
		if (false == array_key_exists($handle, $this->entities)) { return false; }
		$entity = $this->entities[$handle];
		if (true == $tags) {
			return substr($this->raw, $entity['start'], $entity['length']);
		} else {
			if ('single' == $entity['category']) { return ''; }		// self closing tag, no contents
			if (false == array_key_exists('vstart', $entity)) { return ''; } 	// error condition
			if (false == array_key_exists('vlength', $entity)) { return ''; } 	// TODO: log this
			return substr($this->raw, $entity['vstart'], $entity['vlength']);
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	get array of handles child entities
	//----------------------------------------------------------------------------------------------
	//opt: handle - array index of an entity [int]	
	//returns: array (posibly empty) of handles to children, or false on failure [array][bool]

	function getChildren($handle = 1) {
		if (false == array_key_exists($handle, $this->entities)) { return false; }
		return $this->entities[$handle]['children'];
	}

	//----------------------------------------------------------------------------------------------
	//.	get array of child entities as associative array (value => type)
	//----------------------------------------------------------------------------------------------
	//opt: handle - array index of an entity, default is root [int]
	//returns: array (possibly empty) of of child types and values, false on failure [array][bool]
	//; Note that there can be only one child of each type and attributes, etc not returned

	function getChildren2d($handle = 1) {
		if (false == array_key_exists($handle, $this->entities)) { return false; }
		$ary = array();
		foreach($this->entities[$handle]['children'] as $childId) {
			$child = $this->entities[$childId];
			$ary[$child['type']] = $this->getInnerXml($child['handle']); 
		}
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	try to fill a 2d array with children of a given entity
	//----------------------------------------------------------------------------------------------
	//arg: handle - array index of an entity [int]
	//arg: fill - associative array to fill [array]
	//returns: array with any values filled in, or false on failure [array][bool]
	//;	for example, if the array has a key called foo, then there should be a child of type foo
	//;	note that array keys and entity types are case insensitive for this method

	function fill2dArray($handle, $fill) {
		if (false == array_key_exists($handle, $this->entities)) { return false; }
		//TODO: remove the N^2 loop, lowercase the fill keys into a separate array
		//echo "handle: $handle (" . $this->entities[$handle]['type'] . ")<br/>\n";
		foreach ($this->entities[$handle]['children'] as $childHandle) {
			$typelc = strtolower($this->entities[$childHandle]['type']);
			foreach ($fill as $key => $val) {
				if (strtolower($key) == $typelc) { $fill[$key] = $this->getInnerXml($childHandle); }
			}
		}
		return $fill;
	}

	//----------------------------------------------------------------------------------------------
	//.	print tags for debugging
	//----------------------------------------------------------------------------------------------
	//returns: html table [string]

	function printTags() {
		global $utils, $theme;

		$table = array();
		$table[] = array('handle', 'type', 'cat', 'start', 'content');
		foreach($this->tags as $id => $tag) {
			$row = array();
			$row[] = $id;
			$row[] = $tag['type'];
			$row[] = $tag['category'];
			$row[] = $tag['start'];
			$row[] = htmlEntities($tag['content']);
			$table[] = $row;
		}

		$html = $theme->arrayToHtmlTable($table, true, true);
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//.	print entities for debugging
	//----------------------------------------------------------------------------------------------
	//returns: html table [string]

	function printEntities() {
		global $theme;
		$table = array();
		$table[] = array('handle', 'parent', 'cat', 'type', 'tag1', 'tag2', 'children', 'contents');
		foreach($this->entities as $e) {
			$row = array(	$e['handle'], 	$e['parent'], 	$e['category'], 	$e['type'],
							$e['tag1'], 	$e['tag2'],		implode('|', $e['children']),
							htmlEntities($this->getInnerXml($e['handle'], false)) );
			$table[] = $row;
		}

		$html = $theme->arrayToHtmlTable($table, true, true);
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove CDATA wrapper from a value
	//----------------------------------------------------------------------------------------------
	//arg: txt - string wrapped in CDATA [string]
	//returns: string without CDATA [string]

	function stripCDATA($txt) {
		$txt = trim($txt);
		if ('<![CDATA[' == substr($txt, 0, 9)) { $txt = substr($txt, 10); }
		$len = strlen($txt);
		if (']]>' == substr($txt, ($len - 3))) { $txt = substr($txt, 0, $len - 3); }
		return $txt;
	}

}

?>
