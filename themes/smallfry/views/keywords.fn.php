<?php

//-------------------------------------------------------------------------------------------------
//|	view of keywords for front page
//-------------------------------------------------------------------------------------------------
//opt: show - selected keyword [string]

function theme_keywords($args) {
	global $kapenta;
	global $theme;
	global $utils;

	$html = '';								//	return value;

	if (false == array_key_exists('show', $args)) {
		//-----------------------------------------------------------------------------------------
		//	if no keyword has been given, show the tag cloud
		//-----------------------------------------------------------------------------------------

		$tags = array();
		$files = $kapenta->fileSearch('themes/smallfry/keywords/', 'html');

		foreach($files as $file) {
			$parts = explode(".", basename($file));

			$keyword = $parts[0];
			$size = $parts[1];

			$tags[] = array(
				'name' =>$keyword,
				'weight' => (int)$size,
				'link' => "javascript:home_keyword('$keyword', '$size');"
			);

		}
		
		$html .= "
		<script>
			function home_keyword(kwName, kwSize) {
				var block = '[[' + ':theme::keywords::show=' + kwName + '::size=' + kwSize + ':]]';
				klive.bindDivToBlock('divLeftContent', block, false);
			}
		</script>
		";

		$html .= "<div style='color: red;'>" . $theme->makeTagCloud($tags) . "</div>";

	} else {

		//-----------------------------------------------------------------------------------------
		//	show a specific keyword
		//-----------------------------------------------------------------------------------------

		$fileName = ''
		 . "themes/smallfry/keywords/"
		 . $utils->makeAlphaNumeric($args['show'], '-') . "."
		 . $utils->makeAlphaNumeric($args['size'], '-') . ".html";

		if (true == $kapenta->fileExists($fileName)) {
			$html .= $kapenta->filegetContents($fileName);
		} else {
			$html .= "<h2>(not found) $fileName </h2>";
		}

	}

	return $html;

}

?>
