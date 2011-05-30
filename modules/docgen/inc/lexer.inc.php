<?


	require_once($kapenta->installPath . 'modules/docgen/models/sourcefile.mod.php');
	require_once($kapenta->installPath . 'modules/docgen/models/sourceclass.mod.php');
	require_once($kapenta->installPath . 'modules/docgen/models/sourcefunction.mod.php');
	require_once($kapenta->installPath . 'modules/docgen/models/sourcestatement.mod.php');

//-------------------------------------------------------------------------------------------------
// use lexer output to create tree structure to represent program, dependancies, etc
//-------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
//	general knowledge
//-------------------------------------------------------------------------------------------------

	$phpKeywords = "abstract and array as break case catch cfunction class clone const continue "
				 . "declare default do else elseif enddeclare endfor endforeach endif endswitch "
				 . "endwhile extends final for foreach function global goto if implements "
				 . "interface instanceof namespace new old_function or private protected public "
				 . "static switch throw try use var while xor";

	$phpKw = explode(" ", $phpKeywords);

//-------------------------------------------------------------------------------------------------
//	parse lexer output
//-------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
//	read the cells array into a SourceFile object
//-------------------------------------------------------------------------------------------------

function dgTokenizeCells($model) {
	if (is_array($model->cells) == false) { echo "cells array not set<br/>\n"; return false; }

	$cells = $model->cells;
	$len = count($cells);
	$lastType = '';
	$buf = array();		// collects the token or statement we're dealing with
	$lit = '';			// string for comparison

	//---------------------------------------------------------------------------------------------
	//	go though all php in the cells and build tree
	//---------------------------------------------------------------------------------------------
	$skipTo = 1;		// ignore all cells up to this point

	foreach($cells as $cStr) {
		$cell = dgExpandCell($cStr);
		if (($cell['abs'] >= $skipTo) && (true == $cell['inphp'])) {

			//-------------------------------------------------------------------------------------
			//	type changes when we've reached the end of a token
			//-------------------------------------------------------------------------------------
			if ($cell['type'] != $lastType) {
				
				switch($lastType) {
					case 'e':						// php escapes (ignore)
						$buf = array(); 			// just clear the buffer
						break;

					case ';':						// end of a simple statement						
						$statement = new SourceStatement($buf, $model);
						$model->statements[] = $statement;
						$buf = array();				// clear the buffer
						break;

					case 't':						// generic token, may be a fn name, etc
						$endOfBlock = dgCheckToken($model, $cell['abs'], $lit, $buf, $cells);
						if ($endOfBlock > 0) {
							$skipTo = $endOfBlock;	// may have scanned ahead
							$cell['type'] = 'c';	// prevent adding this to buffer;
							$buf = array();			// clear the buffer
						}
						break;

				}
				
				$lit = ''; 							// done with this tokern
			}

			//-------------------------------------------------------------------------------------
			//	add cell to buffer if it is php code (ie, not a comment)
			//-------------------------------------------------------------------------------------
			if (($cell['type'] != 'c') && ($cell['type'] != 'l')) {
				$buf[] = $cStr;				// add to buffer
				$lit .= $cell['char'];		// add to lit 
			}
			$lastType = $cell['type'];		// set last type;
		}
	}

	// return $model;
}

//-------------------------------------------------------------------------------------------------
//	handle class and function definitions, control statements, etc
//-------------------------------------------------------------------------------------------------

function dgCheckToken($model, $idx, $lit, $buf, $cells) {
	echo "checking token: $lit <br/>\n";
	$buf0 = dgExpandCell($buf[0]);

	switch($lit) {
		case 'class': 
			$cbLevel = ($buf0['cblevel'] + 1);							// level of indentation
			$cbPos = dgGetBrace('}', $cells, $buf0['abs'], $cbLevel); 	// end of class abs pos
			$range = dgGetRange($cells, $buf0['abs'], $cbPos);

			$newClass = new SourceClass($range, $model);
			$model->classes[] = $newClass;
			echo "added class:<br/> <textarea rows='20' cols='100'>" . $newClass->toString() . "</textarea><br/>\n";
			return ($cbPos + 1);
			break;


		case 'function': 
			echo "buf start: " . $buf0['abs'] . " token end: " . $idx . "<br/>\n";
			$cbLevel = ($buf0['cblevel'] + 1);							// level of indentation
			$cbPos = dgGetBrace('}', $cells, $buf0['abs'], $cbLevel); 	// get end of block
			$range = dgGetRange($cells, $buf0['abs'], $cbPos);
		
			echo "start: " . $buf0['abs'] . " cbPos: $cbPos <br/>\n";	

			$newFunction = new SourceFunction($range, $model);
			$model->functions[] = $newFunction;
			echo "added function:<br/> <textarea rows='20' cols='100'>" . $newFunction->toString() . "</textarea><br/>\n";
			return ($cbPos + 1);
			break;

	}
	return 0;
}

//-------------------------------------------------------------------------------------------------
//	return a subset of cells given starting and closing absolute positions
//-------------------------------------------------------------------------------------------------

function dgGetRange($cells, $startAbs, $endAbs) {
	$range = array();
	foreach($cells as $cStr) {
 		$cell = dgExpandCell($cStr);
		if (($cell['abs'] >= $startAbs) && ($cell['abs'] <= $endAbs)) {	$range[] = $cStr; }
		if ($cell['abs'] > $endAbs) { return $range; }
	}
	return $range;
}

//-------------------------------------------------------------------------------------------------
//	find the absolute position of the next brace at a given depth (returns false if not found)
//-------------------------------------------------------------------------------------------------

function dgGetBrace($char, $cells, $start, $cblevel) {
	$len = count($cells);
	//echo "dgGetBrace char: $char len: $len start: $start cblevel: $cblevel<br/>";
	foreach($cells as $cStr) {
		$cell = dgExpandCell($cStr);
		if ($cell['abs'] >= $start) {
			if (($char == $cell['type']) && ($cblevel == $cell['cblevel'])) { 
				return $cell['abs']; 
			}
		}
	}
	return false;
}

//-------------------------------------------------------------------------------------------------
//	find the abs position of the next parenthesis at a given depth (returns false if not found)
//-------------------------------------------------------------------------------------------------

function dgGetParen($char, $cells, $start, $plevel) {
	$len = count($cells);
	//echo "dgGetParen char: $char len: $len start: $start plevel: $plevel<br/>";
	foreach($cells as $cStr) {
		$cell = dgExpandCell($cStr);
		if ($cell['abs'] >= $start) {
			if (($char == $cell['type']) && ($plevel == $cell['plevel'])) { 
				return $cell['abs']; 
			}
		}
	}
	return false;
}

//-------------------------------------------------------------------------------------------------
//	convert cells array to string
//-------------------------------------------------------------------------------------------------

function dgCellsToString($cells) {
	$str = '';
	foreach($cells as $cell) { $str .= $cell['char']; }
	return $str;
}

//-------------------------------------------------------------------------------------------------
//	strip newlines out of the buffer
//-------------------------------------------------------------------------------------------------

//function dgCellsToString($cells) {
//	$str = '';
//	foreach($cells as $cell) { $str .= $cell['char']; }
//	return $str;
//}

?>
