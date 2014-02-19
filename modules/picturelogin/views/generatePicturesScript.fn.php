<?

	require_once($kapenta->installPath . 'modules/picturelogin/inc/characterArray.php');

//--------------------------------------------------------------------------------------------------
//*	Functionality to define script of generate button in picture login
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	return a html string of <script ... /> 
//--------------------------------------------------------------------------------------------------

	function picturelogin_generatePicturesScript() {
		$charArr = getCharacterArray();
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
						
						function getPassChars() {
							var passChars = new Array();";
							
							for ($i = 0; $i < $count; $i++){
								$string = $string . "passChars[" . $i . "] = '" . $charArr[$i] . "';"; 
							}

		$string = $string . "return passChars;
						};
						
						function getPasswordCharacterArray(username, password) {
							var retArr = new Array();
							var passChars = getPassChars();
							var int = 0;
							var txt = '';

							var passChars1 = getPassChars();
							var userNameI = establishUsernameNum(username);
							var count = " . $count . ";

							for (var i = 0; i < password.length; i++) {

								int = passChars1.indexOf(password.charAt(i)) - userNameI;
								
								if (int < 0) {
									int = int + count;
								}
																
								txt = txt + int + '/';
								txt = txt + passChars[int];
								passChars[int] = '';
							}			
							
							retArr[0] = txt;
							retArr[1] = passChars;
							
							return retArr;	
						};
						
						function captureFormPics() {
							var elem;
							var elem1 = document.getElementById('source');
							var elem2 = document.getElementById('txtUser');
							var username = elem2.value;
							var elem3 = document.getElementById('txtPassword');
							var password = elem3.value;
							var passChars = getPassChars();

							if ('' == password) {
								alert('Please specify your current password!');
								return false;
							}

							var arr = getPasswordCharacterArray(username, password);
							
							var flag = false;
							var id = '';
							var count = 0;
							var link;
							for (var i = 0; i < arr[0].length; i++) {
								if (true == flag) {
									link = document.createElement('a');
									link.setAttribute('data-icon', arr[0].charAt(i));
									link.setAttribute('class', 'icon');
									link.setAttribute('draggable', 'true');
									link.id = id;
									count++;
									elem = document.getElementById('drop' + count);
									if (0 < elem.children.length) {
										elem.removeChild(elem.children[0]);
									}
									elem.appendChild(link);
									flag = false;
									id = '';
								}
								else if ('/' == arr[0].charAt(i)) {
									flag = true;
								} else if (false == flag) {
									id = id + arr[0].charAt(i);
								}
							}
							var i2 = 0;
							var link1;

							while (0 < elem1.childNodes.length) {
								elem1.removeChild(elem1.childNodes[0]);
							}
							
							for (var i1 = 0;  i1 < arr[1].length; i1++) {
								link1 = document.createElement('a');
									if ('' != arr[1][i1]) {
										link1.setAttribute('data-icon', arr[1][i1]);
										link1.setAttribute('class', 'icon');
										link1.setAttribute('draggable', 'true');
										link1.id = i1;
										elem1.appendChild(link1);
										i2++;
									}
									
								if (0 < i2 && 0 == (i2+1)%27) {
									var link2 = document.createElement('br');
									elem1.appendChild(link2);
								}
							}
														
							return false;				
						};
					</script>";
		return $string;
	}

?>
