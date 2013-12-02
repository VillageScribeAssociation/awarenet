<?

//--------------------------------------------------------------------------------------------------
//	a simple php tokenizer and parser
//--------------------------------------------------------------------------------------------------
//arg: code - PHP source code [string]

function kjs_tokenize($code) {
	$tokens = array();			//%	array of token structs [array]
	$codeLen = strlen($code);	//%	length of code [int]

	//----------------------------------------------------------------------------------------------
	//	define some tokens
	//----------------------------------------------------------------------------------------------

	$t5 = array('<?php');

	// three char tokens
	$t3 = array(
		'===' => 'T_IS_IDENTICAL',			'!==' => 'T_IS_NOT_IDENTICAL',
		'>>=' => 'T_SR_EQUAL',				'<<<' => 'T_START_HEREDOC',
		'<<=' => 'T_SL_EQUAL',				'=>' => 'T_DOUBLE_ARROW',
	);

	// two char tokens
	$t2 = array(
		'//' => 'T_COMMENT',				'/*' => 'T_COMMENT',
		'|=' => 'T_OR_EQUAL',				'&&' => 'T_BOOLEAN_AND',
		'||' => 'T_BOOLEAN_OR',				'--' => 'T_DEC',
		'++' => 'T_INC',					'==' => 'T_IS_EQUAL',
		'<>' => 'T_IS_NOT_EQUAL',			'<=' => 'T_IS_SMALLER_OR_EQUAL',
		'>=' => 'T_IS_GREATER_OR_EQUAL',	'-=' => 'T_MINUS_EQUAL',
		'%=' => 'T_MOD_EQUAL',				'*=' => 'T_MUL_EQUAL',
		'/=' => 'T_DIV_EQUAL',				'^=' => 'T_XOR_EQUAL',
		'+=' => 'T_PLUS_EQUAL',				'>>' => 'T_SR',
		'.=' => 'T_CONCAT_EQUAL',			'&=' => 'T_AND_EQUAL',
		'%>' => 'T_CLOSE_TAG',				'::' => 'T_PAAMAYIM_NEKUDOTAYIM',
		'<<' => 'T_SL',						'->' => 'T_OBJECT_OPERATOR',
		'?>' => 'T_CLOSE_TAG'
	);

	// single char tokens (yes, PHP really does name some tokens after themselves)
	$t1 = array(
		'#' => 'T_COMMENT',					'~' => '~',
		'\\' => '\\',						'`' => '`',							
		'+' => '+',							'-' => '-',
		'/' => '/',							'*' => '*',
		'[' => '[',							']' => ']',
		'{' => '{',							'}' => '}',
		':' => ':',							';' => ';',
		"'" => "'",							'"' => '"',
		'<' => '<',							'>' => '>',
		',' => ',',							'.' => '.',
		'?' => '?',							'|' => '|',
		'@' => '@',							'%' => '%',
		'^' => '^',							'(' => '(',
		')' => ')',							"\t" => 'T_WHITESPACE',
		'\r' => 'T_WHITESPACE',				"\n" => 'T_WHITESPACE',
		' ' => 'T_WHITESPACE',				'$' => 'T_VARIABLE',
	);	

	// escaped characters in complex strings
	$tS = array(
		'\n' => 'DQ_NEWLINE',				'\t' => 'DQ_TAB',
		'\r' => 'DQ_CARRIAGERETURN',		'\v' => 'DQ_VERTICALTAB',
		'\f' => 'DQ_FORMFEED',				'\$' => 'DQ_DOLLAR',
		'\\' . '\\' => 'DQ_BACKSLASH',		'\\"' => 'DQ_DOUBLEQUOTE',
		'\0' => 'DQ_OCTAL',					'\1' => 'DQ_OCTAL',
		'\2' => 'DQ_OCTAL',					'\3' => 'DQ_OCTAL',
		'\4' => 'DQ_OCTAL',					'\5' => 'DQ_OCTAL',
		'\6' => 'DQ_OCTAL',					'\7' => 'DQ_OCTAL',
		'\8' => 'DQ_OCTAL',					'\x' => 'DQ_HEXCHAR',
		'{$' => 'DQ_VARBLOCK'
	);

	// reserved words
	$tX = array(
		'abstract' => 'T_ABSTRACT',			'as' => 'T_AS',
		'break' => 'T_BREAK',				'case' => 'T_CASE',
		'catch' => 'T_CATCH',				'class' => 'T_CLASS',
		'clone' => 'T_CLONE',				'const' => 'T_CONST',
		'continue' => 'T_CONTINUE',			'declare' => 'T_DECLARE',
		'default' => 'T_DEFAULT',			'do' => 'T_DO',
		'echo' => 'T_ECHO',					'else' => 'T_ELSE',
		'elseif' => 'T_ELSEIF',				'empty' => 'T_EMPTY',
		'enddeclare' => 'T_ENDDECLARE',		'endfor' => 'T_ENDFOR',
		'endforeach' => 'T_ENDFOREACH',		'endif' => 'T_ENDIF',
		'endswitch' => 'T_ENDSWITCH',		'endwhile' => 'T_ENDWHILE',
		'exit' => 'T_EXIT',					'extends' => 'T_EXTENDS',
		'final' => 'T_FINAL',				'for' => 'T_FOR',
		'foreach' => 'T_FOREACH',			'function' => 'T_FUNCTION',
		'global' => 'T_GLOBAL',				'goto' => 'T_GOTO',
		'if' => 'T_IF',						'implements' => 'T_IMPLEMENTS',
		'instanceof' => 'T_INSTANCEOF',		'interface' => 'T_INTERFACE',
		'namespace' => 'T_NAMESPACE',		'new' => 'T_NEW',
		'old_function' => 'T_OLD_FUNCTION',	'private' => 'T_PRIVATE',
		'public' => 'T_PUBLIC',				'while' => 'T_WHILE',
		'use' => 'T_USE',					'try' => 'T_TRY',
		'var' => 'T_VAR',					'switch' => 'T_SWITCH',
		'__CLASS__' => 'T_CLASS_C',			'__DIR__' => 'T_DIR',
		'__FILE__' => 'T_FILE',				'__FUNCTION__' => 'T_FUNC_C',
		'__LINE__' => 'T_LINE',				'__NAMESPACE__' => 'T_NS_C',
		'__METHOD__' => 'T_METHOD_C',		'throw' => 'T_THROW',
		'xor' => 'T_LOGICAL_XOR',			'or' => 'T_LOGICAL_OR',
		'and' => 'T_LOGICAL_AND',			'return' => 'T_RETURN'
	);

	//----------------------------------------------------------------------------------------------
	//	state machine 'registers'
	//----------------------------------------------------------------------------------------------
	$mode = 'outside';			//%	current setting of state machine [string]
	$charNo = 0;				//%	location of 'cursor' within the file [int]
	$c1 = '';					//%	current character, if any [char]
	$c2 = '';					//%	next character, if any [char]
	$c3 = '';					//%	next + 1 character, if any [char]
	$c4 = '';					//%	next + 2 character, if any [char]
	$c5 = '';					//%	next + 3 character, if any [char]
	$buffer = '';				//%	contains current token as it is read [string]
	$start = 0;					//%	char position within file where this token started [string]
	$row = 0;					//%	line number [int]
	$col = 0;					//% column number, assuming 4 tab spaces [int]

	if ($codeLen > 0) {	$c2 = substr($code, 0, 1); }	//	initialize $cX for first pass of loop
	if ($codeLen > 1) {	$c3 = substr($code, 1, 1); }	//	...
	if ($codeLen > 2) {	$c4 = substr($code, 2, 1); }
	if ($codeLen > 3) {	$c5 = substr($code, 3, 1); }

	//----------------------------------------------------------------------------------------------
	//	go through the code one char at a time
	//----------------------------------------------------------------------------------------------

	for ($charNo = 0; $charNo < $codeLen; $charNo++) {

		//------------------------------------------------------------------------------------------
		//	set c1, c2, c3, c4, one, two, three and four - TODO: optimize
		//------------------------------------------------------------------------------------------
		$c1 = substr($code, $charNo, 1);
		if (($charNo + 1) < $codeLen) { $c2 = substr($code, ($charNo + 1), 1); } else { $c2 = ''; }
		if (($charNo + 2) < $codeLen) { $c3 = substr($code, ($charNo + 2), 1); } else { $c3 = ''; }
		if (($charNo + 3) < $codeLen) { $c4 = substr($code, ($charNo + 3), 1); } else { $c4 = ''; }
		if (($charNo + 4) < $codeLen) { $c5 = substr($code, ($charNo + 4), 1); } else { $c5 = ''; }

		$one = $c1;								//%	next character [string]
		$two = $c1 . $c2;						//%	next two characters [string]
		$three = $c1 . $c2 . $c3;				//%	next three characters [string]
		$four = $c1 . $c2 . $c3 . $c4;			//%	next four characters [string]
		$five = $c1 . $c2 . $c3 . $c4 . $c5;	//%	next five characters [string]

		//------------------------------------------------------------------------------------------
		//	find out which mode (state) we're in, and behave accordingly
		//------------------------------------------------------------------------------------------
		switch ($mode) {
			
			//--------------------------------------------------------------------------------------
			//	outside of php code, state changes when we encounter <? <% or <?php
			//--------------------------------------------------------------------------------------
			case 'outside':
				if (('<?' == $two) || ('<%' == $two) || ('<?php' == $five)) {	// start of PHP
					if ('' != $buffer) {		
						$tokens[] = array(
							'type' => 'T_INLINE_HTML', 
								'start' => $start, 
							'value' => $buffer
						);
						$buffer = '';			// clear the buffer
					}

					//TODO: T_OPEN_TAG_WITH_ECHO 	<?= or <%= 	escaping from HTML

					$openTag = $two;			//%	opening tag [string]
					$openTagLen = 2;			//%	length of opening tag [int]
					if ('<?php' == $five) { $openTag = $five; $openTagLen = 5; }

					// add token for opening tag
					$tokens[] = array(
						'type' => 'T_OPEN_TAG',
						'start' => $charNo,
						'value' => $openTag
					);

					$start = $charNo + $openTagLen;		// set start of next token
					$charNo += ($openTagLen - 1);		// advance cursor
					$mode = 'php';						// switch to php mode
					//echo "charNo set to $charNo <br/>\n";

				} else { $buffer .= $one; }

				break;

			//--------------------------------------------------------------------------------------
			//	inside of php code, look for generic tokens, single char ones, strings and comments
			//--------------------------------------------------------------------------------------
			case 'php':
				$guess = '';				//%	what type of token we imagine this is [string]
				$size = 1;					//%	size of this token [int]
				//----------------------------------------------------------------------------------
				//	try guess what type of token it is
				//----------------------------------------------------------------------------------
				if (array_key_exists($three,  $t3)) { $guess = $t3[$three]; $size = 3; }
				elseif (array_key_exists($two, $t2)) { $guess = $t2[$two]; $size = 2; }
				elseif (array_key_exists($one, $t1)) { $guess = $t1[$one]; $size = 1; }
				else { $guess = ''; }		// we don't know what it is yet, but its not whitespace				

				//----------------------------------------------------------------------------------
				//	some of these will require switching modes
				//----------------------------------------------------------------------------------
				//echo "charNo: $charNo guess: $guess one: $one two: $two three: $three<br/>\n";
				switch ($guess) {
					case 'T_COMMENT':
						if ('/*' == $two) { $mode = 'longcomment'; }
						if ('//' == $two) { $mode = 'shortcomment'; }
						if ('#' == $one) { $mode = 'shortcomment'; }
						$buffer = $c1;
						$start = $charNo;
						break;

					case 'T_VARIABLE':
						$mode = 'token';				// switch to generic token mode
						$buffer = '';					// clear the buffer
						$start = $charNo;				// token starts here
						$charNo--;						// process this again in sqstring mode
						break;

					case '"':
						$mode = 'dqstring';				// switch to double quoted string mode
						$buffer = '';					// clear the buffer
						$start = $charNo + 1;			// next token starts here
						$type = 'DQ_STRING_START';
						$tokens[] = array('type' => $type, 'start' => $charNo, 'value' => $one);
						break;

					case "'":
						$mode = 'sqstring';				// switch to single quoted string mode
						$buffer = '';					// clear the buffer
						$start = $charNo;				// token starts here
						$charNo--;						// process this again in sqstring mode
						break;

					case "T_CLOSE_TAG":
						// throw the closing tag and switch to outside mode
						$tokens[] = array('type' => $guess, 'start' => $charNo, 'value' => $two);
						$charNo++;
						$start = $charNo + 1;
						$mode = 'outside';
						break;

					case 'T_START_HEREDOC':
						//$mode = 'heredoc';
						//$buffer = $c1;
						//TODO
						break;

					default:
						if ('' == $guess) {					// if we have no dea what this is yet 
							$mode = 'token';				// enter generic 'token' mode
							$buffer = '';					// clear the buffer
							$start = $charNo;				// token starts here
							$charNo--;						// process this again in token mode

						} else {
							$val = $three;
							if (2 == $size) { $val = $two; }
							if (1 == $size) { $val = $one; }
							$tokens[] = array('type' => $guess, 'start' => $charNo, 'value' => $val);
							$charNo += ($size - 1);	// skip to end of this token
						}
						break;

				}

				break;	// end of case 'php'

			//--------------------------------------------------------------------------------------
			//	long comments, end when we encounter */
			//--------------------------------------------------------------------------------------
			case 'longcomment':
				if ('*/' == $two) {
					$buffer .= $two;
					$tokens[] = array('type' => $guess, 'start' => $start, 'value' => $buffer);
					$charNo++;				// skip a char
					$start = $charNo + 2;	// next token might start two chars on
					$mode = 'php';			// switch back to php mode

				} else { $buffer .= $c1; }
				break;

			//--------------------------------------------------------------------------------------
			//	short comments end when we hit a newline or carriage return
			//--------------------------------------------------------------------------------------
			case 'shortcomment':
				$buffer .= $c1;
				if (("\r" == $c2) || ("\n" == $c2)) {
					$tokens[] = array('type' => 'T_COMMENT', 'start' => $start, 'value' => $buffer);
					$buffer = '';			// clear the buffer
					$start = $charNo + 1;	// ... not really necessary?
					$mode = 'php';			// switch back to php mode
				} else {  }
				break;


			//--------------------------------------------------------------------------------------
			//	single quoted string
			//--------------------------------------------------------------------------------------
			case 'sqstring':
				$buffer .= $c1;										// add c1 to buffer first
	
				if (('\\' == $c1) && ('\\' == $c2)) {				// this is an escaped backslash
					$buffer .= $c2;									// add c2 to the buffer
					$charNo++;										// skip past c2

				} elseif (('\\' == $c1) && ('\'' == $c2)) {			// this is an escaped s-quote
					$buffer .= $c2;									// add c2 to the buffer
					$charNo++;										// skip past c2

				} elseif ((("'" == $c1) && ($buffer != "'"))) {		// this is the end of the string
					$tokens[] = array(
						'type' => 'T_CONSTANT_ENCAPSED_STRING',
						'start' => $start,
						'value' => $buffer
					);
					$buffer = '';
					$mode = 'php';
					$start = $charNo + 1;
				}

				break;

			//--------------------------------------------------------------------------------------
			//	generic token mode, continue until we reach a non-label character
			//--------------------------------------------------------------------------------------
			case 'token':
				$buffer .= $c1;					//	add current char to the buffer

				$c2Label = false;				//%	is set to true if c2 is a valid label chr [bool]
				$c2o = ord($c2);				//%	ASCII codepoint of the next char [int]
				if (($c2o > 47) && ($c2o < 58)) { $c2Label = true; }		// is [0-9]
				elseif (($c2o > 64) && ($c2o < 91)) { $c2Label = true; }	// is [A-Z]
				elseif (($c2o > 96) && ($c2o < 123)) { $c2Label = true; }	// is [a-z]
				elseif (95 == $c2o) { $c2Label = true; }					// is _

				if (false == $c2Label) {
					// this is the last char of the token
					$type = 'token';
					if ('$' == substr($buffer, 0, 1)) { $type = 'T_VARIABLE'; }
					if (true == array_key_exists($buffer, $tX)) { $type = $tX[$buffer]; }
					

					$tokens[] = array(
						'type' => $type,
						'start' => $start,
						'value' => $buffer
					);
					$buffer = '';
					$mode = 'php';
					$start = $charNo + 1;
				}
				break;

			//--------------------------------------------------------------------------------------
			//	double quoted string
			//--------------------------------------------------------------------------------------
			case 'dqstring':

				//----------------------------------------------------------------------------------
				// is this character the start of an escape sequence?
				//----------------------------------------------------------------------------------
				if ( (('\\' == $c1)||('{' == $c1)) && (true == array_key_exists($two, $tS)) ) {

					//------------------------------------------------------------------------------
					//	this *is* the beginning of some new escaped part
					//------------------------------------------------------------------------------
					if ('' != $buffer) {					// if there's something in the
						$tokens[] = array(					// buffer then it's an
							'type' => 'DQ_INVARIANT', 		// invariant (literal) string
							'start' => $start, 				// segment.  Throw it and
							'value' => $buffer				// clear the buffer.
						);
						$buffer = '';
					}

					//------------------------------------------------------------------------------
					//	change to the appropriate mode for this type of escape sequence
					//------------------------------------------------------------------------------
					$guess = $tS[$two];
					switch($guess) {

						case 'DQ_VARBLOCK':
							//----------------------------------------------------------------------
							//	this is a variable inside of curly braces
							//----------------------------------------------------------------------						
							$buffer = $two;						// it begins with {$
							$start = $charNo;					// and starts right here
							$charNo++;							// skip the '$'
							$mode = 'dqoctal';					// process the rest in dqoctal mode
							break;

						case 'DQ_OCTAL':
							//----------------------------------------------------------------------
							//	this is an octal char reference, they can be up to 1-3 chars long
							//----------------------------------------------------------------------
							$buffer = $c1;						// it begins with this char	
							$start = $charNo;					// and starts right here
							$mode = 'dqoctal';					// process the rest in dqoctal mode
							break;

						case 'DQ_HEXCHAR':
							//----------------------------------------------------------------------
							//	this is a hex char reference, they can be up to 1-2 chars long
							//----------------------------------------------------------------------
							$buffer = $two;						// 
							$start = $charNo;					// next token begins here
							$charNo++;							// skip the 'x'
							$mode = 'dqhexchar';				// set mode to dqhexchar
							break;

						default:							
							//----------------------------------------------------------------------
							//	this is something simple, like a tab or newline
							//----------------------------------------------------------------------
							$tokens[] = array(
								'type' => $tS[$two],	 		// defined in array above
								'start' => $charNo, 			// begins here
								'value' => $two					// they're all two chars long
							);
							$start = $charNo + 2;				// next token will start in 2 chars
							$charNo++;							// skip the next char
							break;

					} // end switch

				} else {
					//------------------------------------------------------------------------------
					//	not the beginning of an escaped sequence
					//------------------------------------------------------------------------------

					if (("\"" == $c1) || (('$' == $c1))) {
						//--------------------------------------------------------------------------
						// throw any invariant stuff in the buffer
						//--------------------------------------------------------------------------
						if ('' != $buffer) {
								$tokens[] = array(
								'type' => 'DQ_INVARIANT', 
								'start' => $start,
								'value' => $buffer
							);
						}

						//--------------------------------------------------------------------------
						// start of simple embedded variable
						//--------------------------------------------------------------------------						
						if ('$' == $c1) {
							$buffer = $c1;						// begins with $
							$start = $charNo;					// starts here
							$mode = 'dqvariable';				// process the rest of it thusly
						}

						//--------------------------------------------------------------------------
						// end of dq string, throw the end of string token
						//--------------------------------------------------------------------------						
						if ("\"" == $c1) {
							$tokens[] = array(
								'type' => 'DQ_STRING_END', 
								'start' => $charNo, 
								'value' => $one
							);
							$buffer = '';						// clear the buffer
							$mode = 'php';						// back to php mode at last
							$start = $charNo + 1;				// next token begins after this
						}

					} else {
						//--------------------------------------------------------------------------
						//	none of the above, this is just an invariant (literal) string character
						//--------------------------------------------------------------------------
						$buffer .= $c1;

					}
				}
				break;


			//--------------------------------------------------------------------------------------
			//	variable in double quoted string (plain, no curly braces)
			//--------------------------------------------------------------------------------------
			case 'dqvariable':
				$buffer .= $c1;

				$c2Label = false;				//%	is set to true if c2 is a valid label chr [bool]
				$c2o = ord($c2);				//%	ASCII codepoint of the next char [int]
				if (($c2o > 47) && ($c2o < 58)) { $c2Label = true; }		// is [0-9]
				elseif (($c2o > 64) && ($c2o < 91)) { $c2Label = true; }	// is [A-Z]
				elseif (($c2o > 96) && ($c2o < 123)) { $c2Label = true; }	// is [a-z]
				elseif (95 == $c2o) { $c2Label = true; }					// is _

				if (false == $c2Label) {
					$tokens[] = array(
						'type' => 'T_VARIABLE', 
						'start' => $start,
						'value' => $buffer
					);
					$buffer = '';				//	clear the buffer
					$mode = 'dqstring';			//	return to dq string mode
					$start = $charNo + 1;		//	next token starts here
				}

				break;

			//--------------------------------------------------------------------------------------
			//	variable in double quoted string, escaped in {$curlyBraceStyle}
			//--------------------------------------------------------------------------------------
			case 'dqvariableblock':
				
				break;

			//--------------------------------------------------------------------------------------
			//	octal char in double quoted string
			//--------------------------------------------------------------------------------------
			case 'dqoctal':
				$buffer .= $c1;									// should always be [0-9]
				$c2o = ord($c2);
				$atEnd = false;

				if (($c2o > 47) && ($c2o < 58)) {
					//------------------------------------------------------------------------------
					// next char is [0-9]
					//------------------------------------------------------------------------------
					if (strlen($buffer) >= 4) {					// if the buffer holds 4 chars
						$atEnd = true;
					}

				} else {
					//------------------------------------------------------------------------------
					// next char is not [0-9]
					//------------------------------------------------------------------------------
					$atEnd = true;
				}
				
				if (true == $atEnd) {
					$tokens[] = array(							// then we've reached the end
						'type' => 'DQ_OCTAL',	 				// of octal char.  Throw it.
						'start' => $start,
						'value' => $buffer
					);
					$start = $charNo + 1;						// next section begins with c2
					$mode = 'dqstring';							// go back to dqstring mode
				}

				break;

			//--------------------------------------------------------------------------------------
			//	hex char in double quoted string
			//--------------------------------------------------------------------------------------
			case 'dqhexchar':
				$buffer .= $c1;									// should always be [0-9A-Fa-f]
				$c2o = ord(strtolower($c2));
				$atEnd = false;

				if ((($c2o > 47) && ($c2o < 58)) || (($c2o > 97) && ($c2o < 102))) {
					//------------------------------------------------------------------------------
					// next char is [0-9A-Fa-f], but if the buffer holds 3 chars then we're done
					//------------------------------------------------------------------------------
					if (strlen($buffer) >= 3) {	$atEnd = true; }

				} else {
					//------------------------------------------------------------------------------
					// next char is not [0-9]
					//------------------------------------------------------------------------------
					$atEnd = true;
				}

				if (true == $atEnd) {
					$tokens[] = array(							// then we've reached the end
						'type' => 'DQ_HEXCHAR',	 				// of hex char.  Throw it.
						'start' => $start,
						'value' => $buffer
					);
					$start = $charNo + 1;						// next section begins with c2
					$mode = 'dqstring';							// go back to dqstring mode
				}

				break;

			//--------------------------------------------------------------------------------------
			//	error
			//--------------------------------------------------------------------------------------
			default:
				echo "unknown mode: $mode<br/>\n";
				break;
		}

	}

	//------------------------------------------------------------------------------------------
	//	add final token	
	//------------------------------------------------------------------------------------------
	if (('outside' == $mode) && ('' != $buffer)) {
		$tokens[] = array(
			'type' => $type,
			'start' => $start,
			'value' => $buffer
		);
	}

	//------------------------------------------------------------------------------------------
	//	done
	//------------------------------------------------------------------------------------------
	return $tokens;
}

//--------------------------------------------------------------------------------------------------
//|	a very rough conversion to javascript
//--------------------------------------------------------------------------------------------------
//arg: $tokens - raw stream of PHP tokens
//returns: hacked stream which is closer to Javascript
//:	Note - this is a mess, it's not meant to perfectly reproduce PHP code, just to take some of the
//:	work out of porting simple code to Kapenta.JS

function kjs_rough_convert_js($tokens) {
	//==============================================================================================
	//	some initial transformations to convert into Javascript
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//	remove 'global' and 'require_once' statements
	//----------------------------------------------------------------------------------------------

	$skipuntil = '';

	foreach($tokens as $idx => $tk) {
		if (('require_once' == $tk['value']) && ('token' == $tk['type'])) { $skipuntil = ';'; }
		if ('T_GLOBAL' == $tk['type']) { $skipuntil = ';'; }
		if ('T_OPEN_TAG' == $tk['type']) { $tokens[$idx]['value'] = ''; }
		if ('T_CLOSE_TAG' == $tk['type']) { $tokens[$idx]['value'] = ''; }

		if ('' != $skipuntil) {
			if ($tk['value'] == $skipuntil) { $skipuntil = ''; }
			$tokens[$idx]['value'] = '';
			$tokens[$idx]['type'] = '';
		}
	}

	//----------------------------------------------------------------------------------------------
	//	declare local variables
	//----------------------------------------------------------------------------------------------

	$inFn = false;			//%	are we currently in a function [bool]
	$fnBD = 0;				//%	brace depth function started at [int]
	$fnIdx = 0;
	$insertIdx = 0;
	$braceDepth = 0;		//%	current brace {} depth [int]
	$bracketDepth = 0;		//%	current bracket () depth [int]

	$lastVarIdx = 0;

	$fnArgs = array();
	$localVars = array();
	$skipLocalVars = array('args');

	foreach($tokens as $idx => $tk) {
		switch($tk['type']) {
			case '{':
				if ((true == $inFn) && ($braceDepth == $fnBD)) {
					//	start of function code
					$insertIdx = $idx;
				}
				$braceDepth++;
				break;	//..........................................................................

			case '}':
				$braceDepth--;
				if ($braceDepth == $fnBD) {
					//	end of function code
					$inFn = false;
					foreach($localVars as $varName) {
						$jsDefault = "''";
						switch($varName) {
							case 'db':		$jsDefault = 'kapenta.db';			break;
							case 'user':	$jsDefault = 'kapenta.user';		break;
							case 'theme':	$jsDefault = 'kapenta.theme';		break;
						}

						if (false == in_array($varName, $skipLocalVars)) {
							$tokens[$insertIdx]['value'] .= ''
							 . "\n\tvar " . $varName . " = " . $jsDefault . ";\t\t//%";
						}
					}
				}
				break;	//..........................................................................

			case '(':	$bracketDepth++;	break;
			case ')':	$bracketDepth--;	break;

			case 'T_FUNCTION':
				$inFn = true;
				$fnBD = $braceDepth;
				$fnIdx = $idx;
				break;	//..........................................................................

			case 'T_VARIABLE':
				$tk['value'] = str_replace('$', '', $tk['value']);
				$tokens[$idx]['value'] = $tk['value'];
				$lastVarIdx = $idx;

				//TODO: fix this up - make it find function arguments

				if (true == $inFn) {
					if (
						(false == in_array($tk['value'], $localVars)) &&
						(false == in_array($tk['value'], $fnArgs))
					) {
						$localVars[] = $tk['value'];		//	found a new local variable
					}
				} 
				break;	//..........................................................................

			case 'T_COMMENT':
				if ('//%' == substr($tk['value'], 0, 3)) {
					//	found a local variable definition
					$skipLocalVars[] = $tokens[$lastVarIdx]['value'];
					$tokens[$lastVarIdx]['value'] = 'var ' . $tokens[$lastVarIdx]['value'];
				}
				break;	//..........................................................................

		}

	}

	//----------------------------------------------------------------------------------------------
	//	fix string operator, convert args object and convert object instantiate
	//----------------------------------------------------------------------------------------------

	$val = array('', '', '', '');
	$typ = array('', '', '', '');

	foreach($tokens as $idx => $tk) {
		if ('.' == $tk['type']) { $tokens[$idx]['value'] = '+'; }

		$val[3] = $val[2];
		$val[2] = $val[1];
		$val[1] = $val[0];
		$val[0] = $tk['value'];

		$typ[3] = $typ[2];
		$typ[2] = $typ[1];
		$typ[1] = $typ[0];
		$typ[0] = $tk['type'];


		//------------------------------------------------------------------------------------------
		//	convert $args['something'] to args.something
		//------------------------------------------------------------------------------------------

		if (
			('args' == $val[3]) &&
			('[' == $val[2]) &&
			('T_CONSTANT_ENCAPSED_STRING' == $typ[1]) &&
			(']' == $val[0])
		) {
			$val[1] = str_replace("'", '', $val[1]);
			$val[1] = str_replace("\"", '', $val[1]);
			$tokens[$idx - 2]['value'] = ".";
			$tokens[$idx - 1]['value'] = $val[1];
			$tokens[$idx]['value'] = "";
		}

		//------------------------------------------------------------------------------------------
		//	convert new Some_Model(..) to kapenta.create('some', 'model', ...)
		//------------------------------------------------------------------------------------------
		
		if (
			('T_NEW' == $typ[3]) &&
			('T_WHITESPACE' == $typ[2]) && 
			('token' == $typ[1]) &&
			('(' == $typ[0])
		) {
			$parts = explode('_', strtolower($val[1]));
			if (1 == count($parts)) { $parts[1] = '/* TODO: add to kapenta object */'; }

			$tokens[$idx - 3]['value'] = "kapenta.create(";
			$tokens[$idx - 2]['value'] = "";
			$tokens[$idx - 1]['value'] = "'" . $parts[0] . "', '" . $parts[1] . "', ";
			$tokens[$idx]['value'] = "";
		}

		//------------------------------------------------------------------------------------------
		//	note typecasting
		//------------------------------------------------------------------------------------------

		if (
			('(' == $typ[2]) && 
			('token' == $typ[1]) &&
			(')' == $typ[0])
		) {
			$casts = array(
				'int' => 'Number',
				'float' => 'Number',
				'string' => 'String',
				'bool' => 'Bool'
			);

			if (true == array_key_exists($val[1], $casts)) {
				//echo "found cast: " . $val[1] . "<br/>\n";
				$tokens[$idx - 2]['value'] = "/* TODO: cast to " . $casts[$val[1]] . " */";
				$tokens[$idx - 1]['value'] = '';
				$tokens[$idx]['value'] = ' ';
			}
		}		

	}

	//----------------------------------------------------------------------------------------------
	//	fix array definitions and string functions
	//----------------------------------------------------------------------------------------------

	$inArray = 0;
	$cb = 0;		//%	closing braces from array definitions [int]

	$replacements = array(
		'array_key_exists' => 'kapenta.php.array_key_exists',
		'strlen' => 'kapenta.php.strlen',
		'substr' => 'kapenta.php.substr',
		'explode' => 'kapenta.php.explode',
		'implode' => 'kapenta.php.implode',
		'strtolower' => 'kapenta.php.strtolower',
		'str_replace' => 'kapenta.php.str_replace',
		'base64_encode' => 'kapenta.utils.base64_encode',
		'base64_decode' => 'kapenta.utils.base64_decode',
	);

	foreach($tokens as $idx => $tk) {
		switch($tk['type']) {
			case 'token':
				if ('array' == $tk['value']) {
					$inArray++;
					$tokens[$idx]['value'] = '';
				}

				foreach($replacements as $find => $replace) {
					if ($tk['value'] == $find) { $tokens[$idx]['value'] = $replace; }
				}

				break;	//..........................................................................

			case '(':
				//	only count the ( followng an array
				if ($inArray > 0) { $tokens[$idx]['value'] = '['; }
				break;	//..........................................................................

			case ')':
				//	only count the ( followng an array
				if ($inArray > 0) {
					$tokens[$idx]['value'] = ']';
					$inArray--;
				}
				break;	//..........................................................................

			case 'T_OBJECT_OPERATOR':
				$tokens[$idx]['value'] = '.';
				break;	//..........................................................................

		}
	}

	return $tokens;
}

function kjs_mkToken($type, $token, $start, $end) {
	$newToken = array();
	$newToken['type'] = $type;
	$newToken['token'] = $token;
	$newToken['start'] = $start;
	$newToken['end'] = $end;
	return $newToken;
}

?>
