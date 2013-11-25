
//--------------------------------------------------------------------------------------------------
//*	Javascript functionality to add / modify / remove user relationships
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	show a friend request form in search result
//--------------------------------------------------------------------------------------------------
//arg: toUser - UID of a Users_User object [string]

function users_showFriendRequestForm(toUser) {
	var statusDiv = document.getElementById('divSRStatus' + toUser);
	statusDiv.innerHTML = "<span class='ajaxmsg'>Loading...</span>";

	var block = '[[' + ':users::friendrequestformjs::friendUID=' + toUser + ':]]';

	klive.removeBlock(block);
	klive.bindDivToBlock('divSRStatus' + toUser, block);
}

//--------------------------------------------------------------------------------------------------
//|	submit a friend request via AJAX
//--------------------------------------------------------------------------------------------------
//arg: toUser - UID of a Users_User object [string]

function users_makeRequest(toUser) {
	var statusDiv = document.getElementById('divSRStatus' + toUser);
	var selRelationship = document.getElementById('relationship' + toUser);

	if (!selRelationship) { return false; }

	var url = jsServerPath + 'users/addfriendrequestjs/'

	var params = ''
	 + "action=addFriendReq&"
	 + "friendUID=" + toUser + "&"
	 + "relationship=" + selRelationship.value;

	var cbFn = function(responseText, status) {
		statusDiv.innerHTML = responseText;

		if ('<ok/>' == responseText) {
			statusDiv.innerHTML = "<span class='ajaxmsg'>Done.</span>";

			//TODO: update friend request list in other div.
			//TODO: update friends list in content pane.
		}

		var frBlock = '[[' + ':users::showfriendrequests::userUID=' + jsUserUID + ':]]';
		klive.removeBlock(frBlock);
		klive.bindDivToBlock('divFriendRequestOuter', frBlock, false);
	}

	kutils.httpPost(url, params, cbFn);

	statusDiv.innerHTML = "<span class='ajaxmsg'>Requesting...</span>";
}
