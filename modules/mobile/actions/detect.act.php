<?

//--------------------------------------------------------------------------------------------------
//*	detect details mobile client 
//--------------------------------------------------------------------------------------------------
//+	future versions will attempt to detect the screen size (/w javascript and table)
//+	current bootstrap assumes a screen size of 320 or so px wide

	$agent = $_SERVER['HTTP_USER_AGENT'];  
      
    if(preg_match('/iPhone|Android|Blackberry/i', $agent)){  
		include($kapenta->installPath . 'modules/mobile/actions/on.act.php');
    } else {
		include($kapenta->installPath . 'modules/mobile/actions/off.act.php');
	}


?>
