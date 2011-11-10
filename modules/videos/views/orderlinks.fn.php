<?

//--------------------------------------------------------------------------------------------------
//|	make links to order main video list by
//--------------------------------------------------------------------------------------------------

function videos_orderlinks($args) {
	$linkBase = '%%serverPath%%videos/listallgalleries/orderby_';

	$html = ''
	 . "<table noborder width='100%'><tr><td bgcolor='#dddddd'>" 
		. "&nbsp;&nbsp; list by: "
		. "<a href='" . $linkBase . "title'>[title]</a> "
		. "<a href='" . $linkBase . "videocount'>[number of videos]</a> "
		. "<a href='" . $linkBase . "createdOn'>[creation date]</a> "
		. "<a href='" . $linkBase . "editedOn'>[most recent update]</a> "
	. "</td></tr></table><hr/>";
	
	return $html;
}

?>
