
//==================================================================================================
//*	front end javascript for notifications (so far just for hiding them)
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//|	collapse a notification div and hide it via AJAX
//--------------------------------------------------------------------------------------------------

function notifications_hide(nUID) {
	var divId = 'divN' + nUID;
	var theDiv = document.getElementById(divId);

	//----------------------------------------------------------------------------------------------
	//	collapse the feed item
	//----------------------------------------------------------------------------------------------
	theDiv.innerHTML = ''
		 + "<div class='action'>"
		 + "&nbsp;Removing..."
		 + "<span style='float: right'>"
		 + "<img src='" + jsServerPath + "themes/clockface/images/throbber-inline.gif' />"
		 + "&nbsp;</span>"
		 + "</div>";

	//----------------------------------------------------------------------------------------------
	//	make async call back to server
	//----------------------------------------------------------------------------------------------
	var url = jsServerPath + 'notifications/hide/' + nUID;
	var params = 'x=y';

	cbFn = function(responseText, status) { 
		if (200 == status) {
			var sDiv = document.getElementById(divId);
			sDiv.innerHTML = '';
		} else {
			alert('WARNING: ' + status + "\n" + responseText);
		}
	}

	kutils.httpPost(url, params, cbFn);

}
