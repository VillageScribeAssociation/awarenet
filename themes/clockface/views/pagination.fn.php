<?

//--------------------------------------------------------------------------------------------------
//	make pagination bar (page caterpillar: << | >> [1][2][3][4]...[15][16] )
//--------------------------------------------------------------------------------------------------
//arg: page - page we're currently on (int) [string]
//arg: total - total number of pages (int) [string]
//arg: link - URL without page_x argument [string]

function theme_pagination($args) {
	$html = '';							//%	return value [string]
	$prevLink = '';						//%	link to previous page, if any [string] 
	$nextLink = '';						//%	link to next page, if any [string]
	$pagination = '';					//%	list of links to other pages [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('page', $args)) { return 'error: page'; }
	if (false == array_key_exists('total', $args)) { return 'error: total'; }
	if (false == array_key_exists('link', $args)) { return 'error: link'; }

	//----------------------------------------------------------------------------------------------
	//	make the block, could probably do with some more error checking
	//----------------------------------------------------------------------------------------------
	if ($args['page'] > 1) { 
		$prevLink = $args['link'] . 'page_' . ($args['page'] - 1);
		$prevLink = "<a href='" . $prevLink . "/' class='black'><< previous </a>" . ' | ';
	}

	if ($args['page'] < $args['total']) { 
		$nextLink = $args['link'] . 'page_' . ($args['page'] + 1);
		$nextLink = "<a href='" . $nextLink . "/' class='black'> next >> </a>"; 
	}

	for ($i = 1; $i <= $args['total']; $i++) {
		if ($i == 1) {
			$pagination .= "<a href='" . $args['link'] . "' class='black'>[" . $i . "]</a> \n";
		} else {
			$link = $args['link'] . "page_" . $i;
			$pagination .= "<a href='" . $link . "/' class='black'>[" . $i . "]</a> \n";
		}
	}

	$html .= "<table noborder width='100%'><tr><td bgcolor='#dddddd'>\n&nbsp;&nbsp;";
	$html .= $prevLink . $nextLink . ' ' . $pagination . "<br/>\n";
	$html .= "</td></tr></table>\n";	

	return $html;
}

?>
