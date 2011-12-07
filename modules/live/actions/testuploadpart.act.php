<?

//--------------------------------------------------------------------------------------------------
//*	recieves a base64_encoded chunk of data and a hash
//--------------------------------------------------------------------------------------------------

	$filehash = $_POST['filehash'];
	$parthash = $_POST['parthash'];
	$part64 = $_POST['part64'];

	$part = base64_decode($part64);
	$sha1 = sha1($part);

	echo "sha1: $sha1 parthash: $parthash <br/>";

	echo "<textarea row='10' style='width: 100%'>$part64</textarea><br/>";
	echo "<textarea row='10' style='width: 100%'>$part</textarea><br/>";

?>
