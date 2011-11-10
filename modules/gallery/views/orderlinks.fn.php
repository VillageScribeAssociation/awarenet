<?

//--------------------------------------------------------------------------------------------------
//|	shows option to order gallery list (everyone) by different fields
//--------------------------------------------------------------------------------------------------

function gallery_orderlinks($args) {
	$html = '';

	$linkBase = '%%serverPath%%gallery/listall/orderby_';

	$html = ''
	 . "<table noborder width='100%'><tr><td bgcolor='#dddddd'>" 
		. "&nbsp;&nbsp; list by: "
		. "<a href='" . $linkBase . "title'>[title]</a> "
		. "<a href='" . $linkBase . "ownerName'>[creator]</a> "
		. "<a href='" . $linkBase . "schoolName'>[school]</a> "
		. "<a href='" . $linkBase . "imagecount'>[number of images]</a> "
		. "<a href='" . $linkBase . "createdOn'>[creation date]</a> "
		. "<a href='" . $linkBase . "editedOn'>[most recently updated]</a> "
	. "</td></tr></table><hr/>";

	return $html;
}

?>
