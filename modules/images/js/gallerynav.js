
//-------------------------------------------------------------------------------------------------
//*	javascript for gallery navigation
//-------------------------------------------------------------------------------------------------
//+	requires a div with id 'gallerynavjs'
//+	requires variable galleryNavCurrIdx to be initialized with current position

//TODO: convert to object-oriented javascript and use an AJAX-bound view

//-------------------------------------------------------------------------------------------------
//	set current index and draw for first time
//-------------------------------------------------------------------------------------------------

function galleryNav_init() { galleryNav_refresh(); }

//-------------------------------------------------------------------------------------------------
//	redraw the div
//-------------------------------------------------------------------------------------------------

function galleryNav_refresh() {
	if (galThumbs.length == 0) {
		divSetContent('galleryNavJs', '(there are no images in this gallery)<br>');
		return false;
	}

	var html = "";

	var prevButton = "<a href='#' onClick='galleryNavCurrIdx--; galleryNav_refresh();'>"
				   + "<img src='" + jsServerPath + "themes/clockface/icons/arrow_left.jpg' border='0' /></a>";

	var nextButton = "<a href='#' onClick='galleryNavCurrIdx++; galleryNav_refresh();'>"
				   + "<img src='" + jsServerPath + "themes/clockface/icons/arrow_right.jpg' border='0' /></a>";

	html = html + "<table noborder>\n";
	html = html + "  <tr>\n";
	html = html + "    <td>" + prevButton + "</td>\n";

	if (galleryNavCurrIdx == -1) { galleryNavCurrIdx = galThumbs.length; }

	for (i = galleryNavCurrIdx; i < (galleryNavCurrIdx + 4); i++) {

		idx = i % galThumbs.length;

		html = html + "    <td>" 
					+ "<a href='" + jsServerPath + "gallery/image/" + galThumbs[idx][2] + "'>" 
					+ "<img src='" + jsServerPath + "images/thumbsm/" + galThumbs[idx][2] + "' border='0' />" 
					+ "</a></td>\n";

	}

	html = html + "    <td>" + nextButton + "</td>\n";
	html = html + "  </tr>\n";
	html = html + "</table>\n";

	divSetContent('galleryNavJs', html);
}
