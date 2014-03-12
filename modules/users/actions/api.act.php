<?

//--------------------------------------------------------------------------------------------------
//*	users module API (DEPRECATED)
//--------------------------------------------------------------------------------------------------
//+	Used by firefox plugin. These APIs should be systematized or removed.

//--------------------------------------------------------------------------------------------------
//	display current user
//--------------------------------------------------------------------------------------------------

if ($kapenta->request->ref == 'current') {
	$ary = array(	'uid' => $kapenta->user->UID,
					'username' => $kapenta->user->username,
					'ofgroup' => $kapenta->user->role,  
					'firstname' => $kapenta->user->firstname,  
					'surname' => $kapenta->user->surname );

	echo "<?xml version=\"1.0\"?>\n";
	echo arrayToXml2d($ary, 'user');
}

?>
