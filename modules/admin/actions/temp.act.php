<?

	$raw = implode(file($installPath . 'themes/clockface/css/clockface.css'));
	echo base64EncodeJs('$css', $raw, false);

?>
