<?

//--------------------------------------------------------------------------------------------------
//	users module API
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	display current user
//--------------------------------------------------------------------------------------------------

if ($request['ref'] == 'current') {
	$ary = array(	'uid' => $user->data['UID'],
					'username' => $user->data['username'],
					'ofgroup' => $user->data['ofGroup'],  
					'firstname' => $user->data['firstname'],  
					'surname' => $user->data['surname'] );

	echo "<?xml version=\"1.0\"?>\n";
	echo arrayToXml2d($ary, 'user');
}

?>
