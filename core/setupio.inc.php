<?

//--------------------------------------------------------------------------------------------------
//	code for reading and writing the setup.inc.php
//--------------------------------------------------------------------------------------------------
//	This is used only by the install script and the admin console.  Ordinary users should not be 
//	able to  do this, so these functions are individually checked for auth.

//--------------------------------------------------------------------------------------------------
//	parse setup.inc.php for values
//--------------------------------------------------------------------------------------------------
//	returns array of varname => value

function readGlobalSetup($fileName) {
  	if ($_SESSION['sGroup'] == 'admin') {
	$setupVars = array();

	$raw = implode(file($fileName));
	$lines = explode("\n", $raw);
	
	foreach($lines as $line) {
		$line = trim($line);
		if (substr($line, 0, 1) == '$') {

			//--------------------------------------------------------------------------------------
			//	this line looks like a variable
			//--------------------------------------------------------------------------------------

			$eqPos = strpos($line, '=');	// find equal sign
			$elPos = strpos($line, ';');	// find semicolon

			if (($eqPos > 0) AND ($elPos > $eqPos)) {
				$varName = trim(substr($line, 0, $eqPos));
				$varName = str_replace('$', '', $varName);
				$varVal = trim(substr($line, $eqPos + 1, $elPos - $eqPos - 1));
				$varVal = str_replace("'", '', $varVal);
				$varVal = str_replace("\"", '', $varVal);
				$setupVars[$varName] = $varVal;
			}

		}
	}

	return $setupVars;

//--------------------------------------------------------------------------------------------------
//	parse setup.inc.php and replace variables with those from array
//--------------------------------------------------------------------------------------------------

function writeGlobalSetup($setupVars, $fileName) {
  if ($_SESSION['sGroup'] == 'admin') {

	$newFile = '';

	$raw = implode(file($fileName));
	$lines = explode("\n", $raw);

	foreach($lines as $line) {
	  if (substr(trim($line), 0, 1) == '$') {

		//------------------------------------------------------------------------------------------
		//	this line looks like a variable, see if it can be substituted
		//------------------------------------------------------------------------------------------

		$eqPos = strpos($line, '=');	// find equal sign
		$elPos = strpos($line, ';');	// find semicolon

		if (($eqPos > 0) AND ($elPos > $eqPos)) {
			$varName = trim(substr($line, 0, $eqPos));
			if (array_key_exists($varName, $setupVars)) {

				$newFile = "\t\$" . $varName . " = '" . $setupVars[$varName] . "';\n";

			} else { $newFile .= $line . "\n"; }		
		} else { $newFile .= $line . "\n"; }

	  } else { $newFile .= $line . "\n"; }
	}

	$fH = fopen($fileName, 'w+');
	if ($fH != false) {
		fwrite($fH, $newFile);
		fclose($fH);
		return true;
	} 

  } else { logErr('setupio', 'writeGlobalSetup', 'non-admin user attempt to read setup.inc.php'); }
  return false;
}

?>
