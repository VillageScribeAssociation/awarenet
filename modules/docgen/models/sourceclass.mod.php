<?

//-------------------------------------------------------------------------------------------------
//	represents a php source file, may contain statements, classes, functions, variables, constants
//-------------------------------------------------------------------------------------------------

class SourceClass {

	//---------------------------------------------------------------------------------------------
	//	member variables
	//---------------------------------------------------------------------------------------------

	var $xtype = 'sourceclass';

	var $name;
	var $variables;
	var $statements;
	var $functions;
	var $classes;		
	var $constants;		
	var $includes;
	var $parent;
	
	var $cells;						// tokenizer output
	var $lit = '';					// plain text of code

	//---------------------------------------------------------------------------------------------
	//	constructor
	//---------------------------------------------------------------------------------------------

	function SourceClass($cells, $parent) {
		$this->cells = $cells;
		$this->parent = $parent;

		$this->variables = array();
		$this->statements = array();
		$this->functions = array();
		$this->classes = array();
		$this->constants = array();
		$this->includes = array();

		$this->init();
	}

	//--------------------------------------------------------------------------------------------
	//	init (expand structures within this one)
	//--------------------------------------------------------------------------------------------

	function init() {
		$firstCell = dgExpandCell($this->cells[0]);
		$lastCell = dgExpandCell($this->cells[count($this->cells)]);

		$cbStart = dgGetBrace('{', $this->cells, $firstCell['abs'], ($firstCell['cblevel'] + 1));
		$cbEnd = dgGetBrace('}', $this->cells, $firstCell['abs'], ($firstCell['cblevel'] + 1));
		if (false == $cbEnd) { $cbEnd = $lastCell['abs']; }

		echo "cbStart: $cbStart cbEnd: $dbEnd <br/>\n";	

		//-----------------------------------------------------------------------------------------
		//	get class name
		//-----------------------------------------------------------------------------------------
		$def = dgGetRange($this->cells, 0, $cbStart);
		echo "def<br/><textarea rows=10 cols=80>" . dgCellsToString($def) . "</textarea>";

		//-----------------------------------------------------------------------------------------
		//	process class body
		//-----------------------------------------------------------------------------------------

		$range = dgGetRange($this->cells, ($cbStart + 1), ($cbEnd - 1));
		echo "range<br/><textarea rows=10 cols=80>" . dgCellsToString($range) . "</textarea>";

		$this->cells = $range;
		dgTokenizeCells($this);
	}

	//--------------------------------------------------------------------------------------------
	//	read source code from cells
	//--------------------------------------------------------------------------------------------

	function toString() { return dgCellsToString($this->cells); }

}

?>
