<?

//-------------------------------------------------------------------------------------------------
//	represents a simple statement such as a variable assignment or function call
//-------------------------------------------------------------------------------------------------

require_once($kapenta->installPath . 'modules/docgen/inc/lexer.inc.php');

class SourceStatement {

	//---------------------------------------------------------------------------------------------
	//	member variables
	//---------------------------------------------------------------------------------------------

	var $xtype = 'statement';
	var $UID;
	var $cells;
	var $parent;
	var $lit = '';

	//---------------------------------------------------------------------------------------------
	//	constructor
	//---------------------------------------------------------------------------------------------

	function SourceStatement($cells = false) {
		global $kapenta;
		$this->UID = $kapenta->createUID();
		$this->args = array();
		if (false != $cells) { 
			$this->cells = $cells; 
			$this->lit = $this->toString();
		}
	}

	//--------------------------------------------------------------------------------------------
	//	read code from cells
	//--------------------------------------------------------------------------------------------

	function toString() { dgCellsToString($this->cells); }

}

?>
