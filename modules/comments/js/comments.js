
//-------------------------------------------------------------------------------------------------
//	javascript for adding and displaying comments
//-------------------------------------------------------------------------------------------------
//	requires aryComments (an array of comments) to exist on the page, and a div called
//	divCommentsJs to populate with display

//-------------------------------------------------------------------------------------------------
//	handle an event
//-------------------------------------------------------------------------------------------------

function msgh_comments(channel, event, msg) {
	//alert('comments message recieved, type: ' + event);
	switch(event) {
		case 'add': 	msgh_commentsAdd(msg); break;
		case 'remove':	msgh_commentsRemove(msg); break;
		case 'update':	msgh_commentsUpadte(msg); break;
	}
}

//-------------------------------------------------------------------------------------------------
//	add a new comment to the array and refresh
//-------------------------------------------------------------------------------------------------
//	the message must be UID|escaped message html

function msgh_commentsAdd(msg) {
	var splitPos = msg.indexOf('|');
	if (splitPos > 0) {
		var commentUID = msg.substring(0, splitPos);
		var commentB64 = msg.substring(splitPos + 1);
		aryComments.reverse();
		aryComments.push(new Array(commentUID, commentB64));
		aryComments.reverse();
	}
	//alert('added new comment');
	msgh_commentsRefresh();
}

//-------------------------------------------------------------------------------------------------
//	remove a comment from the array
//-------------------------------------------------------------------------------------------------

function msgh_commentsRemove() {
	
}

//-------------------------------------------------------------------------------------------------
//	remove a comment from the array
//-------------------------------------------------------------------------------------------------

function msgh_commentsUpdate() {
	
}

//-------------------------------------------------------------------------------------------------
//	refresh the display
//-------------------------------------------------------------------------------------------------

function msgh_commentsRefresh() {
	printPage('divCommentsJs', 'commentsPageChanged', aryComments, commentsPage, commentsPageSize);
}

function commentsPageChanged(pageNo) {
	commentsPage = pageNo;
	msgh_commentsRefresh();
}
