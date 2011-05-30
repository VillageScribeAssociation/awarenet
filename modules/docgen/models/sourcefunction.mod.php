<?

	require_once($kapenta->installPath . 'modules/docgen/inc/lexer.inc.php');

//-------------------------------------------------------------------------------------------------
//	represents a function or class method
//-------------------------------------------------------------------------------------------------
//	$args is an array of 'varName', 'optional', 'default', 'byref'

class SourceFunction {
	
	//--------------------------------------------------------------------------------------------
	//	member variables
	//--------------------------------------------------------------------------------------------
	
	var $xtype = 'function';
	var $UID;
	var $args;
	var $statements;	
	var $parent;
	var $cells;
	var $rawCells;					// before any structure is read
	var $lit = '';					// source code as string

	//--------------------------------------------------------------------------------------------
	//	constructor
	//--------------------------------------------------------------------------------------------

	function SourceFunction($cells, $parent) {
		global $kapenta;
		$this->rawCells = $cells; 
		$this->parent = $parent;
		$this->UID = $kapenta->createUID();
		$this->args = array();

		$this->lit = $this->toString();
		$this->init();
	}

	//--------------------------------------------------------------------------------------------
	//	init (expand structures within this one)
	//--------------------------------------------------------------------------------------------

	function init() {
		$firstCell = dgExpandCell($this->rawCells[0]);
		$lastCell = dgExpandCell($this->rawCells[count($this->rawCells)]);

		$cbStart = dgGetBrace('{', $this->rawCells, $firstCell['abs'], ($firstCell['cblevel'] + 1));
		$cbEnd = dgGetBrace('}', $this->rawCells, $firstCell['abs'], ($firstCell['cblevel'] + 1));
		if (false == $cbEnd) { $cbEnd = $lastCell['abs']; }

		echo "cbStart: $cbStart cbEnd: $dbEnd <br/>\n";	

		//-----------------------------------------------------------------------------------------
		//	get function name
		//-----------------------------------------------------------------------------------------
		$def = dgGetRange($this->rawCells, 0, $cbStart);
		echo "def<br/><textarea rows=10 cols=80>" . dgCellsToString($def) . "</textarea><br/>";

		//-----------------------------------------------------------------------------------------
		//	get argument list
		//-----------------------------------------------------------------------------------------		
		$pStart = dgGetParen('(', $this->rawCells, $firstCell['abs'], ($firstCell['plevel'] + 1));
		$pEnd = dgGetParen(')', $this->rawCells, $firstCell['abs'], ($firstCell['plevel'] + 1));
		$args = dgGetRange($this->rawCells, $pStart, $pEnd);
		echo "args<br/><textarea rows=1 cols=80>" . dgCellsToString($args) . "</textarea><br/>";

		//-----------------------------------------------------------------------------------------
		//	process class body
		//-----------------------------------------------------------------------------------------
		$range = dgGetRange($this->rawCells, ($cbStart + 1), ($cbEnd - 1));
		echo "range<br/><textarea rows=10 cols=80>" . dgCellsToString($range) . "</textarea><br/>";

		$this->cells = $range;
		dgTokenizeCells($this);
	}

	//--------------------------------------------------------------------------------------------
	//	read code from cells
	//--------------------------------------------------------------------------------------------

	function toString() { return dgCellsToString($this->rawCells); }

}

?>
