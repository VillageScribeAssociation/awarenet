
//-------------------------------------------------------------------------------------------------
//*	javascript do display results of user search and allow selection
//-------------------------------------------------------------------------------------------------

var usrPage = 0;			// page we're currently on
var usrPageSize = 5;		// user search result page size

function renderResultsHtml(atPage) {
	var divResults = document.getElementById('userSearchResults');
	var html = '';

	if (0 == userList.length) {
		divResults.innerHTML = "<br/>no users match your search :-(<br/>\n";
		return;
	}

	var startResult = (atPage * usrPageSize);
	var endResult = startResult + usrPageSize;

	if (startResult > userList.length) { startResult = userList.length; }
	if (endResult > userList.length) { endResult = userList.length; }

	html = html + usrMkPagination(atPage);
	html = html + "&nbsp; Showing " + startResult + " to " + endResult + " of " + userList.length + " matches.";
	html = html + "<table noborder>";
	for (i = startResult; i < endResult; i++) { 
		html = html
		 + "<tr>"
			+ "<td>"
			 + "<a href='#' onClick='usrSelect(" + i + ");'>"
			 + "<img src='" + jsServerPath + "themes/clockface/images/icons/arrow_left.jpg' border='0' />"
			 + "</a>"
			 + "</td>"
			+ "<td>" + jsRemoveMarkup(userList[i][4]) + "</td>"
		 + "</tr>"; 
	}
	html = html + "</table>";

	divResults.innerHTML = html;
	resizeFrame();
}

function usrMkPagination(atPage) {
	var html = '';
	var maxPages = Math.ceil(userList.length / usrPageSize);

	if (atPage > 0) { html = html + "<a href='#' onClick='renderResultsHtml(" + (atPage - 1) + ");'>&lt;&lt; prev</a>||"; }
	if (atPage < (maxPages - 1)) { html = html + "<a href='#' onClick='renderResultsHtml(" + (atPage + 1) + ");'>next &gt;&gt;</a>"; }

	return html;
}

function usrSelect(usrIdx) {
	usrHtml = jsRemoveMarkup(userList[usrIdx][4]);
	window.parent.addRecipient(userList[usrIdx][0], usrHtml);
}

function jsRemoveMarkup(txt) {
	txt = txt.replace(/--newline--/g, "\n");
	txt = txt.replace(/--squote--/g, "'");
	txt = txt.replace(/--dquote--/g, "\"");
	return txt;
}
