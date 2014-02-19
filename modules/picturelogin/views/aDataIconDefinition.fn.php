<?
	require_once($kapenta->installPath . 'modules/picturelogin/inc/characterArray.php');

//--------------------------------------------------------------------------------------------------
//*	Functionality to add character set of pictures to login
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	return a html string of <a data icon definitions ... /> 
//--------------------------------------------------------------------------------------------------

	function picturelogin_aDataIconDefinition() {
		$charArr = getCharacterArray("", "");
		$count = count($charArr);
		$string = "";
		for ($i = 0; $i < $count; $i++) {
			$string = $string . "<a data-icon='" . $charArr[$i] . "' class='icon' draggable='true' id='" . $i . "'></a>";
			if (0 < $i and 0 == ($i+1)%27) {
				$string = $string . "<br>";
			}
		}
		return $string;
	}

?>
