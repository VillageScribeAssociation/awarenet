<?

//--------------------------------------------------------------------------------------------------
//*	object for parsing formatting wikicode into html and editable sections
//--------------------------------------------------------------------------------------------------
//+	PROPOSED CHANGES: infobox markup to define blocks instead of lines, infobox sections, references,
//+	etc to be stripped from main document and rendered separately.
//+	TODO: define citation style for references:
//+	@ref: label|URL

class WikiCode {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $source = '';
	var $navsource = '';
	var $sections = array();	// article sections [array]

	var $html = '';				// html [string]
	var $contents = '';			// html [string]
	var $infobox = '';			// html [string]

	var $expanded = false;		// is set to true when item is fully expanded [bool]
	var $transclude = true;		// controls whether transclusions and expanded or ignored [bool]

	//----------------------------------------------------------------------------------------------
	//.	expand wiki text
	//----------------------------------------------------------------------------------------------

	function expandWikiCode() {
		global $aliases;

		$source = str_replace('<', '&lt;', $this->source);
		$source = str_replace('>', '&gt;', $source);
		$source = str_replace("\r", '', $source);

		$navsource = str_replace('<', '&lt;', $this->navsource);
		$navsource = str_replace('>', '&gt;', $navsource);
		$navsource = str_replace("\r", '', $navsource);

		$lines = explode("\n", $source);
		$this->sections = array();
		$this->infobox = $navsource . "\n"; 	//changed from =''; to put the lines entered directly into the nav edit box first

		$currSection = array();
		$currSection['title'] = '';
		$currSection['a'] =  '';				// anchor
		$currSection['content'] = '';
		$currSection['html'] = '';
		$currSection['subsections'] = array();	// format of [anchor] => [subsectionTitle]

		$sIndex = 0;	//% level 2 section index [int]
		$ssIndex = 0;	//% level 3 section index [int]
		$sssIndex = 0;	//% level 4 section index [int]
		$ssssIndex = 0;	//% level 5 section index [int]

		foreach($lines as $line) {

			//--------------------------------------------------------------------------------------
			// check for l5 subsections
			//--------------------------------------------------------------------------------------

			if (substr($line, 0, 5) == '=====') { 
				$line = str_replace('=====', '', $line);
				$ssAnchor = 'l5-' . $currSection['a'] . '-' . $aliases->stringToAlias($line);
				$currSection['subsections'][$ssAnchor] = $line;
				$ssssIndex++;
				$line = $sIndex . '.' . $ssIndex . '.' . $sssIndex . '.' . $ssssIndex . ' ' . $line;
				$currSection['content'] .= "<h4><i><a name='" . $ssAnchor . "'>$line</a></i></h4>\n\n";
				$line = '';  // done with this line
			}

			//--------------------------------------------------------------------------------------
			// check for l4 subsections
			//--------------------------------------------------------------------------------------

			if (substr($line, 0, 4) == '====') { 
				$line = str_replace('====', '', $line);
				$ssAnchor = 'l4-' . $currSection['a'] . '-' . $aliases->stringToAlias($line);
				$currSection['subsections'][$ssAnchor] = $line;
				$sssIndex++;
				$ssssIndex = 0;
				$line = $sIndex . '.' . $ssIndex . '.' . $sssIndex . ' ' . $line;
				$currSection['content'] .= "<h4><a name='" . $ssAnchor . "'>$line</a></h4>\n\n";
				$line = '';  // done with this line
			}

			//--------------------------------------------------------------------------------------
			// check for subsections
			//--------------------------------------------------------------------------------------

			if (substr($line, 0, 3) == '===') { 
				$line = str_replace('===', '', $line);
				$ssAnchor = 'l3-' . $currSection['a'] . '-' . $aliases->stringToAlias($line);
				$currSection['subsections'][$ssAnchor] = $line;
				$ssIndex++;
				$sssIndex = 0;
				$ssssIndex = 0;
				$line = $sIndex . '.' . $ssIndex . ' ' . $line;
				$currSection['content'] .= "<h3><a name='" . $ssAnchor . "'>$line</a></h3>\n\n";
				$line = '';  // done with this line
			}

			//--------------------------------------------------------------------------------------
			// check for start of a new section
			//--------------------------------------------------------------------------------------

			if (substr($line, 0, 2) == '==') {
				$this->sections[] = $currSection;	// done with previous section, add to list

				$line = str_replace('==', '', $line);

				$currSection = array();
				$currSection['title'] = $line;
				$currSection['content'] = '';
				$currSection['a'] = $aliases->stringToAlias($line);
				$currSection['subsections'] = array();
				$sIndex++;
				$ssIndex = 0;
				$sssIndex = 0;
				$ssssIndex = 0;
				$line = $sIndex . ' ' . $line;
				$currSection['html'] = "<h2><a name='". $currSection['a'] ."'>$line</a></h2>\n\n";
				$line = '';  // done with this line
			}

			//--------------------------------------------------------------------------------------
			// check for an infobox line
			//--------------------------------------------------------------------------------------

			if ('=(=' == substr($line, 0, 3)) {
				$line = str_replace('=(=', '', $line);
				$line = str_replace('=)=', '', $line);
				$this->infobox .= $line . "\n";
				$line = '';  // done with this line
			}

			//--------------------------------------------------------------------------------------
			// done with this line
			//--------------------------------------------------------------------------------------

			$currSection['content'] .= $line . "\n";

		}

		// add last section
		$this->sections[] = $currSection;

		//------------------------------------------------------------------------------------------		
		//	render wikicode of each section
		//------------------------------------------------------------------------------------------

		foreach($this->sections as $i => $currSection) { 
			if (strlen(trim($currSection['content'])) > 0) {
				$this->sections[$i]['html'] .= $this->wikiCodeToHtml($currSection['content']); 
			}
		}

		$this->infobox = $this->wikiCodeToHtml($this->infobox);

		//------------------------------------------------------------------------------------------		
		//	assemble into document
		//------------------------------------------------------------------------------------------

		$this->expanded = true;
		$this->compileSections();
	}

	//----------------------------------------------------------------------------------------------
	//.	convert a section of wikicode to html
	//----------------------------------------------------------------------------------------------
	//arg: text - wikicode to be rendered in HTML [string]
	//returns: true on success, false on failure [bool]

	function wikiCodeToHtml($text) {
		$lines = explode("\n", $text);
		$html = '';	

		foreach($lines as $line) {

			//--------------------------------------------------------------------------------------
			// check for transclusion
			//--------------------------------------------------------------------------------------

			if ((true == $this->transclude) && ('={=' == substr($line, 0, 3))) { 
				$html .= $this->getTransclusion($line);
			}

			//--------------------------------------------------------------------------------------
			// check for unordered list item lines (third order)
			//--------------------------------------------------------------------------------------
			if ('***' == substr($line, 0, 3)) {
				$html .= '<li3>' . substr($line, 3) . "</li3>";
				$line = '';  // done with this line
			}

			//--------------------------------------------------------------------------------------
			// check for unordered list item lines (second order)
			//--------------------------------------------------------------------------------------
			if ('**' == substr($line, 0, 2)) {
				$html .= '<li2>' . substr($line, 2) . "</li2>";
				$line = '';  // done with this line
			}

			//--------------------------------------------------------------------------------------
			// check for unordered list item lines (first order)
			//--------------------------------------------------------------------------------------
			if ('*' == substr($line, 0, 1)) {
				$html .= '<li1>' . substr($line, 1)  . "</li1>";
				$line = '';  // done with this line
			}

			//--------------------------------------------------------------------------------------
			// check for table row (title)
			//--------------------------------------------------------------------------------------
			if ('|*|' == substr($line, 0, 3)) {
				$line = str_replace('|*|', '||', $line);
				$cells = explode("||", substr($line, 2));
				$html .= "<tr>";
				foreach ($cells as $cell) {	$html .= "<td class='title'>$cell</td>"; }
				$html .= "</tr>";
				$line = '';  // done with this line
			}

			//--------------------------------------------------------------------------------------
			// check for table row (ordinary)
			//--------------------------------------------------------------------------------------
			if (substr($line, 0, 2) == '||') {
				$cells = explode("||", substr($line, 2));
				$html .= "<tr>";
				foreach ($cells as $cell) {	$html .= "<td class='wireframe'>$cell</td>"; }
				$html .= "</tr>";
				$line = '';  // done with this line
			}

			//--------------------------------------------------------------------------------------
			// ordinary paragraphs
			//--------------------------------------------------------------------------------------

			if (trim($line) != '') { $html .= "<p>" . trim($line) . "</p>"; } 
		}

		$html = $this->wrapCollectedElements($html);
		$html = $this->replacePhpBBCode($html);
		$html = $this->replaceWikiLinks($html);
		$html = $this->enforcePre($html);
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//.	compile html version of content
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function compileSections() {
		if ($this->expanded == false) { return false; }
		foreach($this->sections as $index => $section) {

			if (trim($section['title']) != '') {

				// open contents section
				if ($this->contents == '') {
					$this->contents = "<div class='inlinequote'>\n<h1>Contents</h1>\n";
				}

				// add section title link to contents
				$this->contents .= "&nbsp;&nbsp;<a href='#" . $section['a'] . "'>"
								 . $index . ' ' . $section['title'] ."</a><br/>\n";

				// add subsection title links to contents
				$ssIndex = 0;	// level 3
				$sssIndex = 0;	// level 4
				$ssssIndex = 0;	// level 5
				foreach($section['subsections'] as $ssa => $ssTitle) {
						//echo "ssa: $ssa BECOMES ($ssTitle)<br/>\n";
						if ('l3' == substr($ssa, 0, 2)) {
						$ssIndex++;
						$sssIndex = 0;
						$ssssIndex = 0;
						$this->contents .= "&nbsp;&nbsp;&nbsp;&nbsp;"
										 . "<a href='#" . $ssa . "'>" 
										 . $index . '.' . $ssIndex . ' ' . $ssTitle 
										 . "</a><br/>\n";

						}
						if ('l4' == substr($ssa, 0, 2)) {
						$sssIndex++;
						$ssssIndex = 0;
						$this->contents .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
										 . "<a href='#" . $ssa . "'>" 
										 . $index . '.' . $ssIndex .  '.' . $sssIndex . ' '
										 . $ssTitle 
										 . "</a><br/>\n";

						}
						if ('l5' == substr($ssa, 0, 2)) {
						$ssssIndex++;
						$this->contents .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
										 . "&nbsp;&nbsp;&nbsp;&nbsp;<a href='#" . $ssa . "'>" 
										 . $index . '.' . $ssIndex .  '.' . $sssIndex . '.'
										 . $ssssIndex  . ' ' . $ssTitle 
										 . "</a><br/>\n";
						}


				}
			}

			// add html content
			$this->html .= $section['html'];
			if ($index == 0) { $this->html .= "%%wikitoc%%"; }

		}

		// close contents section
		if ($this->contents != '') { $this->contents .= "<br/></div>\n"; }

		// add TOC to article below first section
		$this->html = str_replace('%%wikitoc%%', $this->contents, $this->html);

		//------------------------------------------------------------------------------------------
		//	 done
		//------------------------------------------------------------------------------------------
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	replace phpBBcode
	//----------------------------------------------------------------------------------------------
	//arg: text - wikicode which may contain phpBBcode [string]
	//returns: wikicode with phpBBcode converted to HTML [string]


	function replacePhpBBCode($text) {
		$search = array('[i]', '[/i]', '[b]', '[/b]', '[box]', '[/box]', 
						'[pre]', '[/pre]', '[small]', '[/small]', '[h3]', '[/h3]');

		$replace = array('<i>', '</i>', '<b>', '</b>', "<div class='inlinequote'>", '</div>', 
						'<pre>', '</pre>', '<small>', '</small>', '<h3>', '</h3>');

		$text = str_replace($search, $replace, $text);
		return $text;
	}

	//----------------------------------------------------------------------------------------------
	//.	make anchors from [[wiki|links]]
	//----------------------------------------------------------------------------------------------
	//arg: text - wikicode which may contain anchors [string]
	//returns: wikicode with anchors converted to HTML [string]

	function replaceWikiLinks($text) {
		// remove blocks
		$temp = str_replace('[[:', '', $text);
		$temp = str_replace(':]]', '', $temp);

		// make array of wikilinks
		$parts = explode("[[", $temp);
		foreach($parts as $part) {
			$endPos = strpos($part, "]]");
			if ($endPos > 0) {
				$wikiLink = substr($part, 0, $endPos);
				$anchor = $this->wiki2anchor($wikiLink);
				$text = str_replace('[[' . $wikiLink . ']]', $anchor, $text);
			}
		}

		return $text;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an anchor from a single wikilink
	//----------------------------------------------------------------------------------------------
	//arg: wikiLink - a reference to another wiki article or URL [string]
	//returns: link converted to HTML anchor tag [string]

	function wiki2anchor($wikiLink) {
		global $aliases;
		//------------------------------------------------------------------------------------------
		// split into label and reference
		//------------------------------------------------------------------------------------------
		$pipePos = strpos($wikiLink, '|');
		if (0 == $pipePos) { return $wikiLink; }
		if ($pipePos == strlen($wikiLink)) { return $wikiLink; }
		$ref = trim(substr($wikiLink, 0, $pipePos));
		$label = trim(substr($wikiLink, $pipePos + 1));

		//------------------------------------------------------------------------------------------
		// decide if this is an internal or external link
		//------------------------------------------------------------------------------------------

		if (false != strpos($ref, '://')) {
			//--------------------------------------------------------------------------------------
			// external link
			//--------------------------------------------------------------------------------------
			if (substr(strtolower($ref), 0, 4) == 'java') { return $wikiLink; }	// security
			$ref = str_replace("'", '', $ref);									// security
			return "<a href='" . $ref . "'>" . $label . "</a>";					// done
			
		} else {
			//--------------------------------------------------------------------------------------
			// internal link
			//--------------------------------------------------------------------------------------
			$type = ''; $hash = '';

			//--------------------------------------------------------------------------------------
			// decide which type of internal link
			//--------------------------------------------------------------------------------------
			if ('talk:' == substr($ref, 0, 5)) 		{ $ref = substr($ref, 5); $type = 'talk/'; }
			if ('history:' == substr($ref, 0, 8)) 	{ $ref = substr($ref, 8); $type = 'history/'; }
			if ('' == $ref) { return $wikiLink; }

			//--------------------------------------------------------------------------------------
			// if there's a hash, remove it
			//--------------------------------------------------------------------------------------
			$hashPos = strpos($ref, '#');
			if ($hashPos != false) {
				$hash = substr($ref, $hashPos);
				$ref = substr($ref, 0, $hashPos);
			}
			$refUID = $aliases->getOwner('wiki', 'wiki_article', $ref);
			if (false == $refUID) {
				//----------------------------------------------------------------------------------
				// link to article which does not yet exist
				//----------------------------------------------------------------------------------
				$link = '%%serverPath%%wiki/' . $ref;
				return "<a href='" . $link . "' style='color: red;'>" . $label . "</a>";				

			} else {
				//----------------------------------------------------------------------------------
				// link to article which does exist
				//----------------------------------------------------------------------------------
				$link = '%%serverPath%%wiki/' . $type . $ref . $hash;
				return "<a href='" . $link . "'>" . $label . "</a>";

			}	
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	wrap sets of elements, <li></li> => <ul><li></li></ul>
	//----------------------------------------------------------------------------------------------
	//arg: text - wikicode which may contain grouped elements [string]
	//returns: text with grouped elements rendered into HTML [string]

	function wrapCollectedElements($text) {
		$text = $this->wrapElement($text, 'li3', 'ul3');
		$text = $this->wrapElement($text, 'li2', 'ul2');
		$text = $this->wrapElement($text, 'li1', 'ul1');
		$text = $this->wrapElement($text, 'tr', 'xtable');

		// Not all that pretty and only handles all combinations of 1st, 2nd and 3rd order lists,
		// prehaps a more general solution could be found.
		// could quite easily be done with an extra pass through the text specifically looking for
		// these combination of lists tags and seeing if its getting deeper or shallower and adding 
		// the appropriate number of tags
		$search = 	array('<li1>', 	'</li1>', '<li2>', '</li2>', '<li3>', '</li3>',
				'/ul1><ul2', '/ul2><ul3', '/ul1><ul3', '/ul3><ul2', '/ul2><ul1', '/ul3><ul1',
				'<ul1>','<ul2>','<ul3>','</ul1>','</ul2>','</ul3>');
		$replace = 	array('<li>', '</li>', '<li>', '</li>', '<li>', '</li>',
 				'ul', 'ul', 'ul><ul', '/ul', '/ul', '/ul></ul',
				'<ul>','<ul><ul>','<ul><ul><ul>','</ul>','</ul></ul>','</ul></ul></ul>');

		$text = str_replace($search, $replace, $text);

		$search = 	array('<xtable>', '</xtable>', "</tr>", "</td>", '<tr>', '<td ');
		$replace = 	array("<table class='wireframe' width='100%'>\n", 
						  "</table>\n", "  </tr>\n", "</td>\n", "  <tr>\n", "    <td ");
		$text = str_replace($search, $replace, $text);

		return $text;
	}

	//----------------------------------------------------------------------------------------------
	//.	ensure a given tag is flanked by another; eg, add 'ul' to 'li', 'table' to 'tr', etc
	//----------------------------------------------------------------------------------------------
	//arg: text - wikicode which may contain an HTML tag to be wrapped in another HTML tag [string]
	//arg: m - marker, eg li [string]
	//arg: w - wrap, eg ul [string]
	//returns: text with any grouped HTML elements wrapped in container tag

	function wrapElement($text, $m, $w) { 								// m = marker, w = wrap
		$text = str_replace("<$m>", "<$w><$m>", $text);					// add <wrap>
		$text = str_replace("</$m><$w><$m>", "</$m>\n<$m>", $text);	// dont need <wrap>
		$text = str_replace("</$m>", "</$m></$w>", $text);				// add </wrap>
		$text = str_replace("</$m></$w>\n<$m>", "</$m>\n<$m>", $text); 	// dont need </wrap>
		$text = str_replace("<$w>", "<$w>\n", $text);
		$text = str_replace("</$w>", "\n</$w>", $text);
		return $text;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove any html tags from <pre> elements
	//----------------------------------------------------------------------------------------------
	//arg: text - wikicode which may contain <pre> sections [string]
	//returns: text with HTML tags removed from <pre> sections [string]

	function enforcePre($text) {
		$text = ' ' . $text;		// padded with ' ' in case $text begins with '<pre>'
		$working = true;

		while ($working == true) {
			$startPos = strpos($text, "<pre>");
			$endPos = strpos($text, "</pre>", $startPos) + 6;

			if (($startPos > 0) AND ($endPos > 6)) {
				$preStr = substr($text, $startPos, $endPos - $startPos);
				$cleanStr = '[pre]' . strip_tags($preStr) . '[/pre]';
				$text = str_replace($preStr, $cleanStr, $text);

			} else { $working = false; }
		}

		$text = str_replace("[pre]", "<pre>", $text);
		$text = str_replace("[/pre]", "</pre>", $text);
		return substr($text, 1);
	}

	//----------------------------------------------------------------------------------------------
	//.	retrieve a transcluded section ={=recordAlias|section=}=
	//----------------------------------------------------------------------------------------------
	//arg: line - line containing transclusion tag [string]
	//returns: wikicode to be transcluded [string]	

	function getTransclusion($line) {
		$recordAlias = ''; $section = '';

		//------------------------------------------------------------------------------------------
		//	separate alias and section
		//------------------------------------------------------------------------------------------
		$replace = array(0 => '={=', 1 => '=}=', 2 => "\r", 3 => '\t');
		$with = array(0 => '', 1 => '', 2 => '', 3 => '');
		$line = str_replace($replace, $with, $line);		

		$recordAlias = $line;		
		$pipePos = strpos($line, '|');
		if ($pipePos > 0) {
			$recordAlias = trim(substr($line, 0, $pipePos));
			$section = trim(substr($line, $pipePos + 1));
		}

		//------------------------------------------------------------------------------------------
		//	determine if recordAlias exists, load the article
		//------------------------------------------------------------------------------------------
		$UID = raGetOwner($recordAlias, 'wiki');
		if (false == $UID) { return '(transclusion not found)'; }
		$article = new Wiki($UID);
		// TODO: check, load

		//------------------------------------------------------------------------------------------
		//	determine if recordAlias exists
		//------------------------------------------------------------------------------------------

		$model = new WikiCode();
	}

}

?>
