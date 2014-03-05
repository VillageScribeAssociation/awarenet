<?

	require_once($kapenta->installPath . 'modules/picturelogin/inc/characterArray.php');

//--------------------------------------------------------------------------------------------------
//*	Functionality to define script of generate button in picture login
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	return a html string of <script ... /> 
//--------------------------------------------------------------------------------------------------

	function picturelogin_generatePasswordScript() {
		$charArr = getCharacterArray();
		$specMap = getSpecCharacterMap('=>');
		$count = count($charArr);
		$string = "<script>
						function establishUsernameNum(username) {
							var retVal = 0;
							for (var i = 0; i < username.length; i++) {
								retVal = retVal + username.charCodeAt(i);
							}
							retVal = retVal%" . $count . ";
							return retVal;
						};
						
						function captureForm() {
							var elements = new Array();
							var element;
							var txt = '';
							var int = -1;
							var passChars = new Array();
							var elemUser = document.getElementById('txtUser');
							var username = elemUser.value;
							var check = false;

							for (var i = 1; i < 21; i++) {
								element = document.getElementById('drop' + i);
								if (0 < element.children.length) {
									check = true;
								}
							}
							
							if ('' == username) {
								alert('Please specify a username!');
								return false;
							}
							if (false == check) {
								alert('Please drag icons into <Picture Password fields> in order to form your password!');
								return false;
							}
							check = false;";
														
							for ($i = 0; $i < $count; $i++) {
								if (isset($specMap[$charArr[$i]])) {
									$char = $specMap[$charArr[$i]];
								} else {
									$char =$charArr[$i];
								}
								
								$string = $string . "passChars[" . $i . "] = '" . $char . "';"; 
							}

		$string = $string . "var count = " . $count . ";
							for (var i = 0; i < 20; i++) {
								element = document.getElementById('drop' + (i+1));
								if (0 < element.children.length) {
									int = parseInt(element.children[0].id);
								
									int = int + establishUsernameNum(username);
									if (int > count) {
										int = int - count;
									}
									txt = txt + passChars[int];
								}
							}
							var elem1 = document.getElementById('genpass');
							elem1.value = txt;
							return false;				
						};
					</script>";
		return $string;
	}

?>