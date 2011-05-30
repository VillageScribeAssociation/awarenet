<?

//-------------------------------------------------------------------------------------------------
//	represents a php source file, may contain statements, classes, functions, variables, constants
//-------------------------------------------------------------------------------------------------

class SourceFile {

	//---------------------------------------------------------------------------------------------
	//	member variables
	//---------------------------------------------------------------------------------------------

	var $xtype = 'sourcefile';

	var $variables;
	var $statements;
	var $functions;
	var $classes;		
	var $constants;		
	var $includes;
	var $parent = false;
	
	var $cells;						// tokenizer output
	var $lit = '';					// source code as string

	//---------------------------------------------------------------------------------------------
	//	constructor
	//---------------------------------------------------------------------------------------------

	function SourceFile($cells, $parent = false) {
		$this->cells = $cells; 
		$this->parent = $parent;

		$this->variables = array();
		$this->statements = array();
		$this->functions = array();
		$this->classes = array();
		$this->constants = array();
		$this->includes = array();
	}

}

?>
