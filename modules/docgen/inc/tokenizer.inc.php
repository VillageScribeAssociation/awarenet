<?

//-------------------------------------------------------------------------------------------------
//	Source highlighter
//-------------------------------------------------------------------------------------------------
//#	- used to highlight tokens in a string of PHP code (one dimensional grid)
//#	- state of a cells variables change in response to changes in variables of neighbouring cells
//#	- most state changes propogate in only one direction (towards end of file)
//#
//# - cells are stored serialized in a straight array, unserialized into jagged array:
//#	  - char (character in this cell)
//#   - abs (absolute position within text document)
//#   - inphp (only short opener is recognised)
//#   - type (character describing what type of cell this is
//#     - e (php escape)
//#     - s (single quoted string)
//#     - d (double quoted string)
//#     - i (numeric literal)
//#     - l (long comment /* this */ )
//#	    - c (short comment // this \n )
//#		- v (variable)
//#		- p (property of a class)
//#		- m (method of a class)
//#     - w (whitespace)
//#     - n (newline CR or LF)
//#     - k (keyword; include, class, function, if, else, for, etc)
//#		- t (token)
//#		- x (escaped in string literal, eg \t)
//#     - u (unknown)
//#		- self ([]{}+-
//#   - cblevel (how deep are we inside curly braces {} ?)
//#   - blevel (how deep are we inside braces [] ?)
//#   - plevel (how deep are we inside parentheses () ?)
//#   - lclevel (how deep are we inside nested long comments ?)
//#   - color (for printout, array of rgb)
//#   - bgcolor (for printout, array of rgb)
//#   - line
//#   - col
//#
//# - heredoc and nowdoc syntax not yet supported
//#

//-------------------------------------------------------------------------------------------------
//	read source into array
//-------------------------------------------------------------------------------------------------

function dgTokenizeSource($source) {
	$cells = dgMakeCellArray($source);
	$len = count($cells);
	$numLit = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
	$selfChars = array(";",":",",",".","{","}","[","]","(",")","!","+","-","*","/","=","^","%","<",">");
	$labelChars = array_merge(array('_'), $numLit, range('a', 'z'), range('A', 'Z'));

	//---------------------------------------------------------------------------------------------
	//	forward pass (from beginning to end)
	//---------------------------------------------------------------------------------------------
	$firstCell = true;
	$prev = array(); $cell = array(); $next = array();

	for ($i = 1; $i < ($len - 1); $i ++) {
		//-------------------------------------------------------------------------------------
		//	separate out this cell and its neighbours
		//-------------------------------------------------------------------------------------
		if (1 == $i) {
			$prev = dgExpandCell($cells[$i - 1]); 	// only explicitly loaded the first time
			$cell = dgExpandCell($cells[$i]);
		} else { 
			$prev = $cell;							// use result of last cycle
			$cell = $next; 
		}
		$next = dgExpandCell($cells[$i + 1]);	

		// shortcuts, change these rather than arrays which contain them
		$pchar = $prev['char']; $cchar = $cell['char']; $nchar = $next['char']; 
		$ptype = $prev['type']; $ctype = $cell['type']; $ntype = $next['type'];

		$cell['col'] = $prev['col'] + 1;
		$cell['line'] = $prev['line'];
		if ("\n" == $pchar) { $cell['col'] = 0; $cell['line'] += 1; }

		//-------------------------------------------------------------------------------------
		//	check if we're in a php section
		//-------------------------------------------------------------------------------------
		$cclsd = (($ctype == 'c') || ($ctype == 'l') || ($ctype == 's') || ($ctype == 'd'));
		if (($pchar . $cchar == '<?') && ($cell['inphp'] == false)) { 
			$ptype = 'e'; $ctype = 'e';
			$next['inphp'] = true; 
		}

		if ($prev['inphp'] == true) { $cell['inphp'] = true; }

		if (($cchar . $nchar == '?>') && (true == $cell['inphp']) && (false == $clsd)) { 
			$ctype = 'e'; $ntype = 'e';
			$cell['inphp'] = false; 
		}

		//-------------------------------------------------------------------------------------
		//	the rest of these apply only to php
		//-------------------------------------------------------------------------------------

		if (true == $cell['inphp']) {
			
			//-------------------------------------------------------------------------------------
			//	set types
			//-------------------------------------------------------------------------------------

			// long comments
			$cell['lclevel'] = $prev['lclevel'];
			$pcsd = (($ptype == 'c') || ($ptype == 's') || ($ptype == 'd'));  // in something else
			if (($cchar . $nchar == '/*') && ($pcsd == false)) { $cell['lclevel']++; }
			if (($cell['lclevel'] > 0)) { $ctype = 'l'; }
			if (($pchar == '*') && ($cchar == '/') && ($pcsd == false)) { $cell['lclevel']--; }

			// beginning of short (single line) comment
			if (($cchar == '/') && ($nchar == '/') && ($ptype != 'l')) { $ctype = 'c'; }
			if (($cchar == '#') && ($ptype != 'l')) { $ctype = 'c'; }

			// beginning of single quoted string
			if (($cchar == "'") && ($ptype != 'l') && ($ptype != 'c') 
				&& ($ptype != 's') && ($ptype != 'd') && ($ctype == 'u')) { $ctype = 's'; }

			// beginning of double quoted string
			if (($cchar == "\"") && ($ptype != 'l') && ($ptype != 'c')  
				&& ($ptype != 's') && ($ctype == 'u')) { $ctype = 'd'; }

			//	new lines
			if (($cchar == "\n") || ($cchar == "\r")) { $ctype = 'n'; }

			// continue short comments to end of line
			if (($ptype == 'c') && ($ctype != 'n')) { $ctype = 'c'; }

			// continue single quoted strings to closing single quote
			if ($ptype == 's') {
				if (($cchar == '\\') && ($pchar == "\\")) { 
					$cchar = " \\"; 
					echo "escaped \\ in single quoted string<br/>\n";
				}

				if (($cchar == "'")  && ($pchar != "\\")) { $ctype = 'sc'; }
				else { $ctype = 's'; }
			}

			// continue double quoted strings to closing couble quote
			if ($ptype == 'd') {
				if (($cchar == '\\') && ($pchar == "\\")) { 
					$cchar = " \\"; 
					echo "escaped \\ in double quoted string<br/>\n";
				}

				if (($cchar == "\"") && ($pchar != "\\")){ $ctype = 'dc'; } 
				else { $ctype = 'd'; }
			}

			//-------------------------------------------------------------------------------------
			// operators with more than once character
			//-------------------------------------------------------------------------------------
			if (($cchar == '-') && ($ptype == 'v')) { $ctype = 'v'; }
			if (($cchar == '>') && ($ptype == 'v')) { $ctype = 'v'; }

			//-------------------------------------------------------------------------------------
			// special chars
			//-------------------------------------------------------------------------------------

			if ($ctype == 'u') {
				// continue variables, properties and methods to end of name
				if (in_array($cchar, $labelChars) == true) {
					if ($ptype == 'v') { $ctype = 'v'; }
					if ($ptype == 'p') { $ctype = 'p'; }
					if ($ptype == 'm') { $ctype = 'm'; }
				}

				// numeric literal
				if (($ptype != 't') && ($ptype != 'v') && ($ptype != 'p') && ($ptype != 'm')) {
					if (in_array($cchar, $numLit) == true) { $ctype = 'i'; }
					if ((($cchar == 'x') || ($cchar == '.')) && ($ptype == 'i')) { $ctype = 'i'; }
				}

				// some characters represent themselves
				if (in_array($cchar, $selfChars) == true) { $ctype = $cchar; }

				switch ($cchar) {
					case ' ': 	$ctype = 'w'; break;
					case "\t":	$ctype = 'w'; break;
					case '$': 	$ctype = 'v'; break;
				}
	
			}

			//-------------------------------------------------------------------------------------
			// class properties and methods
			//-------------------------------------------------------------------------------------

			if (($pchar == '>') && ($ctype == 'v')) { $ctype = 'p'; }

			//-------------------------------------------------------------------------------------
			// set or adjust cblevel (but not if occurs in strings or comments)
			//-------------------------------------------------------------------------------------
			$cell['cblevel'] = $prev['cblevel'];

			if (($ptype != 'c') && ($ptype != 's') && ($ptype != 'd') && ($ptype != 'l')) { 
			if ($pchar == '}') { $cell['cblevel'] = $prev['cblevel'] - 1; }
			}

			$type = $cell['type'];
			if (($ctype != 'c') && ($ctype != 's') && ($ctype != 'd') && ($ctype != 'l')) { 
				if ($cchar == '{') { $cell['cblevel'] = $prev['cblevel'] + 1; }
			}

			//are we currently inside a string literal or a comment?
			$cclsd = (($ctype == 'c') || ($ctype == 's') || ($ctype == 'd') || ($ctype == 'l'));
			$pclsd = (($ptype == 'c') || ($ptype == 's') || ($ptype == 'd') || ($ptype == 'l'));

			//-------------------------------------------------------------------------------------
			// set or adjust cblevel (but not if occurs in strings or comments)
			//-------------------------------------------------------------------------------------
			$cell['cblevel'] = $prev['cblevel'];
			if (($pchar == '}') && ($pclsd == false)) { $cell['cblevel'] = $prev['cblevel'] - 1; }
			if (($cchar == '{') && ($cclsd == false)) { $cell['cblevel'] += 1; }

			//-------------------------------------------------------------------------------------
			// set or adjust blevel (but not if occurs in strings or comments)
			//-------------------------------------------------------------------------------------
			$cell['blevel'] = $prev['blevel'];
			if (($pchar == ']') && ($pclsd == false)) { $cell['blevel'] = $prev['blevel'] - 1; }
			if (($cchar == '[') && ($cclsd == false)) { $cell['blevel'] += 1; }

			//-------------------------------------------------------------------------------------
			// set or adjust plevel (but not if occurs in strings or comments)
			//-------------------------------------------------------------------------------------
			$cell['plevel'] = $prev['plevel'];
			if (($pchar == ')') && ($pclsd == false)) { $cell['plevel'] = $prev['plevel'] - 1; }
			if (($cchar == '(') && ($cclsd == false)) { $cell['plevel'] += 1; }

			//-------------------------------------------------------------------------------------
			// tidy up
			//-------------------------------------------------------------------------------------
			if ($ctype == 'u') { $ctype = 't'; }		// anything unknown is a 'token'
			if ($ptype == 'sc') { $ptype = 's'; }		// patch up closing single quote
			if ($ptype == 'dc') { $ptype = 'd'; }		// patch up closing double quote

		}

		//-----------------------------------------------------------------------------------------
		//	replace shortcuts
		//-----------------------------------------------------------------------------------------

		$cell['char'] = $cchar;
		$cell['type'] = $ctype; $prev['type'] = $ptype; $next['type'] = $ntype;

		$cells[$i - 1] = dgCollapseCell($prev); 
		$cells[$i] = dgCollapseCell($cell); 
		$cells[$i + 1] = dgCollapseCell($next);	

	} // end for each cell (forward)

	//---------------------------------------------------------------------------------------------
	//	backward pass (from end to beginning)
	//---------------------------------------------------------------------------------------------
	for ($i = ($len - 1); $i > 1; $i--) {
		//-------------------------------------------------------------------------------------
		//	separate out this cell and its neighbours
		//-------------------------------------------------------------------------------------
		$prev = dgExpandCell($cells[$i - 1]); 
		$cell = dgExpandCell($cells[$i]);
		$next = dgExpandCell($cells[$i + 1]);

		// shortcuts, change these rather than arrays which contain them
		$pchar = $prev['char']; $cchar = $cell['char']; $nchar = $next['char']; 
		$ptype = $prev['type']; $ctype = $cell['type']; $ntype = $next['type'];

		//-------------------------------------------------------------------------------------
		//	patch up escaped \ in single quoted strings
		//-------------------------------------------------------------------------------------
		if ($cchar == " \\") { $cchar = '\\'; }

		//-------------------------------------------------------------------------------------
		//	class methods and variable functions
		//-------------------------------------------------------------------------------------
		if (($nchar == '(') && ($ctype == 'p')) { $ctype = 'm'; }
		if (($ctype == 'p') && ($ntype == 'm')) { $ctype = 'm'; }

		//-------------------------------------------------------------------------------------
		//	replace shortcuts
		//-------------------------------------------------------------------------------------

		$cell['char'] = $cchar;
		$cell['type'] = $ctype; $prev['type'] = $ptype; $next['type'] = $ntype;

		$cells[$i - 1] = dgCollapseCell($prev); 
		$cells[$i] = dgCollapseCell($cell); 
		$cells[$i + 1] = dgCollapseCell($next);	

	}

	return $cells;
}

//-------------------------------------------------------------------------------------------------
//	read source into array
//-------------------------------------------------------------------------------------------------

function dgMakeCellArray($source) {
	$source = ' ' . $source . ' ';	// terminal cells
	$len = strlen($source);
	for ($i = 0; $i < $len; $i++) {
		$cell = array(
			'char' => substr($source, $i, 1),
			'abs' => $i,
			'inphp' => false,
			'type' => 'u',
			'cblevel' => 0,
			'blevel' => 0,
			'plevel' => 0,
			'lclevel' => 0,
			'color' => array('r' => 0, 'g' => 0, 'b' => 0),
			'bgcolor' => array('r' => 255, 'g' => 255, 'b' => 255),
			'line' => 0,
			'col' => 0
		);
		$cells[] = dgCollapseCell($cell);
	}
	return $cells;
}

//-------------------------------------------------------------------------------------------------
//	work out the color
//-------------------------------------------------------------------------------------------------

function dgSetSourceColor($cells) {
	$len = count($cells);
	for ($i = 1; $i < ($len - 1); $i ++) {
		$cell = dgExpandCell($cells[$i]);
		$color = $cell['color'];
		$bgcolor = $cell['bgcolor'];

		//-----------------------------------------------------------------------------------------
		//	highlight php
		//-----------------------------------------------------------------------------------------
		if (true == $cell['inphp']) { 
			$bgcolor['r'] = ($bgcolor['b'] * 5 / 6);			
			$bgcolor['g'] = ($bgcolor['g'] * 5 / 6);
			$bgcolor['b'] = ($bgcolor['b'] * 5 / 6);			
		}

		//-----------------------------------------------------------------------------------------
		//	darken background according to cblevel (depth in curly braces)
		//-----------------------------------------------------------------------------------------
		$cbLevel = $cell['cblevel'];
		while ($cbLevel > 0) {
			$bgcolor['r'] = ($bgcolor['b'] * 5 / 6);			
			$bgcolor['g'] = ($bgcolor['g'] * 5 / 6);
			$bgcolor['b'] = ($bgcolor['b'] * 5 / 6);			
			$cbLevel--;
		} 

		//-----------------------------------------------------------------------------------------
		//	darken background according to blevel (depth in curly braces)
		//-----------------------------------------------------------------------------------------
		$bLevel = $cell['blevel'];
		while ($bLevel > 0) {
			$bgcolor['g'] = ($bgcolor['g'] * 5 / 6);
			$bgcolor['b'] = ($bgcolor['b'] * 5 / 6);			
			$bLevel--;
		} 

		//-----------------------------------------------------------------------------------------
		//	darken background according to blevel (depth in curly braces)
		//-----------------------------------------------------------------------------------------
		$pLevel = $cell['plevel'];
		while ($pLevel > 0) {
			$bgcolor['r'] = ($bgcolor['r'] * 5 / 6);
			$bgcolor['g'] = ($bgcolor['g'] * 5 / 6);			
			$pLevel--;
		} 

		//-----------------------------------------------------------------------------------------
		//	set foreground color of various elements
		//-----------------------------------------------------------------------------------------
		switch ($cell['type']) {
			case 'c': $color = array('r' => 0, 'g' => 0, 'b' => 255); break; // short comment
			case 'l': $color = array('r' => 0, 'g' => 0, 'b' => 255); break; // long comment
			case 's': $color = array('r' => 106, 'g' => 90, 'b' => 205); break; // string ''
			case 'd': $color = array('r' => 106, 'g' => 90, 'b' => 205); break; // string ""
			//case 'i': $color = array('r' => 106, 'g' => 90, 'b' => 205); break; // numeric literal
			case 'v': $color = array('r' => 0, 'g' => 138, 'b' => 140);	break; // variable
			case 't': $color = array('r' => 0, 'g' => 0, 'b' => 0);	break; // token
			case 'm': $color = array('r' => 255, 'g' => 0, 'b' => 0); break; // class method
			case 'i': $color = array('r' => 255, 'g' => 0, 'b' => 0); break; // class method
			case 'p': $color = array('r' => 0, 'g' => 255, 'b' => 0); break; // class properties
			case 'e': $color = array('r' => 128, 'g' => 128, 'b' => 0); break; // class properties
		}

		//$htmlColor = imgRgbToHex($color['r'], $color['g'], $color['b']);
		//echo $cell['type'] . $htmlColor . ' ';

		$cell['bgcolor'] = $bgcolor;
		$cell['color'] = $color;
		$cells[$i] = dgCollapseCell($cell);
	}
	return $cells;
}

//-------------------------------------------------------------------------------------------------
//	print it out
//-------------------------------------------------------------------------------------------------

function dgCellsToHtml($cells) {
	$len = count($cells);
	$lastColor = '';
	$html = "<tt>\n";

	for ($i = 1; $i < ($len - 1); $i ++) {
		$cell = dgExpandCell($cells[$i]);
		$color = $cell['color'];
		$bgcolor = $cell['bgcolor'];

		// replace whitespace with HTML equivalent
		switch ($cell['char']) {
			case "\n":	$cell['char'] = "<br/>\n"; break;
			case "\t":	$cell['char'] = "&nbsp;&nbsp;&nbsp;&nbsp;"; break;
			case " ":	$cell['char'] = "&nbsp;"; break;
			case "<":	$cell['char'] = "&lt;"; break;
			case ">":	$cell['char'] = "&gt;"; break;
			case "&":	$cell['char'] = "&amp;"; break;
		}

		// specify color only when it changes
		$htmlColor = imgRgbToHex($color['r'], $color['g'], $color['b']);
		$htmlBgColor = imgRgbToHex($bgcolor['r'], $bgcolor['g'], $bgcolor['b']);
		//$htmlBgColor = '#ffff';

		if ($lastColor != $htmlColor . $htmlBgColor) { 
			if ($lastColor != '') { echo '</span>'; }
			$style = "color: #" . $htmlColor . "; background-color: #" . $htmlBgColor;
			$html .= "<span style='$style'>";
			$lastColor = $htmlColor . $htmlBgColor;
		}

		$html .= $cell['char']; // . $cell['type'];
	}

	if ($lastColor != '') { $html.= '</span>'; }

	$html .= "</tt>\n";
	return $html;
}

//-------------------------------------------------------------------------------------------------
//	collapse a cell array
//-------------------------------------------------------------------------------------------------

function dgCollapseCell($cell) {
	if ($cell['char'] == '|') { $cell['char'] = 'pipe'; }
	$cell['color'] = implode(',', $cell['color']);
	$cell['bgcolor'] = implode(',', $cell['bgcolor']);
	return implode('|', $cell);
}

//-------------------------------------------------------------------------------------------------
//	expand a cell array
//-------------------------------------------------------------------------------------------------

function dgExpandCell($str) {
	$parts = explode('|', $str);
	$cell = array(
			'char' => $parts[0],
			'abs' => (int)$parts[1],
			'inphp' => (boolean)$parts[2],
			'type' => $parts[3],
			'cblevel' => (int)$parts[4],
			'blevel' => (int)$parts[5],
			'plevel' => (int)$parts[6],
			'lclevel' => (int)$parts[7],
			'color' => $parts[8],
			'bgcolor' => $parts[9],
			'line' => (int)$parts[10],
			'col' => (int)$parts[11] );

	if ($cell['char'] == 'pipe') { $cell['char'] = '|'; }

	$color = explode(',', $cell['color']);
	$cell['color'] = array();
	$cell['color']['r'] =  (int)$color[0];
	$cell['color']['g'] =  (int)$color[1];
	$cell['color']['b'] =  (int)$color[2];

	$bgcolor = explode(',', $cell['bgcolor']);
	$cell['bgcolor'] = array();
	$cell['bgcolor']['r'] =  (int)$bgcolor[0];
	$cell['bgcolor']['g'] =  (int)$bgcolor[1];
	$cell['bgcolor']['b'] =  (int)$bgcolor[2];

	return $cell;
}

//-------------------------------------------------------------------------------------------------
//	go through source file, throw each token to the specified function
//-------------------------------------------------------------------------------------------------

function dgThrowTokens($cells, $callBack) {
	$len = count($cells);
	$buf = array();
	$lastType = '';
	for ($i = 1; $i < ($len - 1); $i ++) {
		$cell = dgExpandCell($cells[$i]);
		if ($cell['type'] != $lastType) {
			$callBack($buf);
			$buf = array();
		}
		$buf[] = $cell;
		$lastType = $cell['type'];
	}
}

?>
