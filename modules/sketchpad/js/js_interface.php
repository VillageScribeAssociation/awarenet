<?

//--------------------------------------------------------------------------------------------------
//*	removed for security sake
//--------------------------------------------------------------------------------------------------
//+	not sure what this is intended for, but this could allow any user to create an admin session.
//+	could probably be made safe by forcing a prefix for all stored / retrieved keys.

/*
if($query = $_SERVER[QUERY_STRING]) {
	$q = query_parse($query);
	if($q['session_stab']) { // write session
		$o = str_replace(Array('%20', '%22'), Array(' ','"'), $q['session_stab']);
		$o = json_decode($o);
		foreach($o as $key=>$value) {
			if(gettype($value) == "NULL") // delete session
				unset($_SESSION[$key]);
			$_SESSION[$key] = $value;
		}
		echo 1;
		exit;
	}
	if($q['session_grab']) { // read session
		$o = $q['session_grab'];
		switch(gettype($o)) {
			case "string": 
				if($_SESSION[$o]) {
					if(gettype($_SESSION[$o]) == 'string')
						echo $_SESSION[$o];
					else
						echo json_encode($_SESSION[$o]);
				}
				break;
			case "array":
				$z = Array();
				foreach($o as $n=>$value)
					$z[$value] = $_SESSION[$value];
				echo json_encode($z);
				break;
		}
		exit;
	}
}
*/

?>
