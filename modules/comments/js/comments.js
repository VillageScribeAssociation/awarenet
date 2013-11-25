
//--------------------------------------------------------------------------------------------------
//*	javascript for adding and displaying comments
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	show inline comment reply form
//--------------------------------------------------------------------------------------------------
//arg: parentUID - UID of a parent Comments_Comment object [string]

function comments_showReplyInline(parentUID) {
	var divId = 'divReply' + parentUID;

	if ($('#' +	divId).html() == '') { 
		var replyBlock = '[' + '[:comments::replyform::parentUID=' + parentUID + ':]' + ']';
		klive.bindDivToBlock(divId, replyBlock);
	} else {
		$('#' +	divId).show();
	}
}

//--------------------------------------------------------------------------------------------------
//|	hide inline comment reply form
//--------------------------------------------------------------------------------------------------
//arg: parentUID - UID of a parent Comments_Comment object [string]

function comments_hideReplyInline(parentUID) {
	var divId = 'divReply' + parentUID;
	$('#' +	divId).hide();
}

//--------------------------------------------------------------------------------------------------
//|	send a reply
//--------------------------------------------------------------------------------------------------
//arg: parentUID - UID of a parent Comments_Comment object, identifies form [string]

function comments_saveReply(parentUID) {

	var msgEmpty = "<span class='ajaxwarn'>Please enter a comment first.</span>";

	var msgSend = ''
		 + "<span class='ajaxmsg'>Saving reply "
		 + "<img src='" + jsServerPath + "themes/clockface/images/throbber-inline.gif'>"
		 + "</span>";

	khta.updateAllAreas();

	//var divId = 'divReply' + parentUID;
	var commentTxt = $('#hdnreply' + parentUID).val();
	commentTxt = kutils.trim(commentTxt);

	if (('' == commentTxt) || ('<br/>' == commentTxt) || ('<br>' == commentTxt)) {
		comments_setReplyStatus(parentUID, msgEmpty);
		return;
	} else {
		comments_setReplyStatus(parentUID, msgSend);
	}

	var theForm = document.getElementById('frmReply' + parentUID);	
	if (!theForm) { alert('Missing form'); return; }

	var postUrl = jsServerPath + 'comments/reply/';

	var params = urlEncodeForm(theForm);
	//alert(params);

	var cbFn = function(responseText, status) {
		//alert('status: ' + status + "\n" + responseText);

		var blockReplies = '[' + '[:comments::replies::parentUID=' + parentUID + ':]' + ']';

		klive.removeBlock(blockReplies);
		klive.bindDivToBlock('divReplies' + parentUID, blockReplies);

		if ('200' == status) {
			comments_setReplyStatus(parentUID, "<span class='ajaxmsg'>Saved.</span>");
			window.setTimeout("comments_setReplyStatus('" + parentUID + "', '');", 1000);
		} else {
			comments_setReplyStatus(parentUID, "<span class='ajaxwarn'>Comment not saved.</span>");
			window.setTimeout("comments_setReplyStatus('" + parentUID + "', '');", 1000);
		}
	}

	kutils.httpPost(postUrl, params, cbFn);

	var hta = khta.getArea('reply' + parentUID);
	//alert(hta.name);
	//alert(hta.getContent());
	hta.setContent('');
	hta.update();

	if (isMobile) { alert('isMobile'); }

	comments_hideReplyInline(parentUID);
}

function comments_setReplyStatus(parentUID, msg) {
	$('#divCRStatus' + parentUID).html(msg);
}
