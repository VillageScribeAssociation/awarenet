//-------------------------------------------------------------------------------------------------
//*	Utility javascript for PM module
//-------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
//	add a recipient, given use UID and html block
//-------------------------------------------------------------------------------------------------
//arg: usrUID - UID of a Users_User object [string]

function messages_addRecipient(usrUID) {
	var divRecip = document.getElementById('composeDisplayRecip');

	if (true == messages_hasRecipient(usrUID)) { return false; }	// prevent duplicates

	//----------------------------------------------------------------------------------------------
	//	add to txtRecipients
	//----------------------------------------------------------------------------------------------

	var txtRecip = document.getElementById('txtRecipients');
	txtRecip.value = txtRecip.value + '|' + usrUID + '|';

	//----------------------------------------------------------------------------------------------
	//	add html
	//----------------------------------------------------------------------------------------------
	messages_showRecipients();
}

//-------------------------------------------------------------------------------------------------
//	check if a given recipient has already been added to this message
//-------------------------------------------------------------------------------------------------

function messages_hasRecipient(usrUID) {
	var txtRecip = document.getElementById('txtRecipients');
	var allRecips = txtRecip.value;
	if (allRecips.indexOf(usrUID) > 0) { return true; }
	return false;
}

//-------------------------------------------------------------------------------------------------
//	remove a recipient from the message
//-------------------------------------------------------------------------------------------------

function messages_removeRecipient(usrUID) {
	//---------------------------------------------------------------------------------------------
	//	remove from txtRecipients
	//---------------------------------------------------------------------------------------------
	var txtRecip = document.getElementById('txtRecipients');
	txtRecip.value = txtRecip.value.replace(new RegExp(usrUID, "g"), '');

	//---------------------------------------------------------------------------------------------
	//	remove from display
	//---------------------------------------------------------------------------------------------

	messages_showRecipients();
}

//-------------------------------------------------------------------------------------------------
//	update recipients display
//-------------------------------------------------------------------------------------------------

function messages_showRecipients() {
	var txtRecip = document.getElementById('txtRecipients');
	var divRecip = document.getElementById('divRecipients');
	var numRecipients = 0;

	if ((!txtRecip) || (!divRecip)) { return; }
	
	divRecip.innerHTML = '';

	var parts = txtRecip.value.split('|');
	for (var i in parts) {
		if ('' != parts[i]) {
			numRecipients++;

			divRecip.innerHTML = divRecip.innerHTML
			 + "<div class='outline'>"
			 + "<table noborder width='100%'>"
			 + "<tr>"
			 + "<td>"
			 + "<div id='divRecip" + parts[i] + "'>"
			 + "<div class='action'>"
			 + "&nbsp;adding recipient..."
			 + "<span style='float: right'>"
			 + "<img src='" + jsServerPath + "themes/" + jsTheme + "/images/throbber-inline.gif' />"
			 + "&nbsp;</span>"
			 + "</div>"
			 + "</div>"
			 + "</td>"
			 + "<td width='34px'>"
			 + "<a href='javascript:void(0)' onClick=\"messages_removeRecipient('" + parts[i] + "')\">"
			 + "<img src='" + jsServerPath + 'themes/' + jsTheme + "/images/icons/arrow_x.png' />"
			 + "</td>"
			 + "</tr>"
			 + "</table>"
			 + "</div>"
			 + "<div class='spacer'></div>";
		}
	}

	for (var i in parts) {
		if ('' != parts[i]) {
			var userBlock = '[[:users::summarynav::userUID=' + parts[i] + ':]]';
			klive.bindDivToBlock('divRecip' + parts[i], userBlock);
		}
	}

	if (0 == numRecipients) {
		divRecip.innerHTML = divRecip.innerHTML
		 + "<div class='outline' style='color: #bbbbbb;'>"
		 + "Tip: use the contact list or search bar to add recipients."
		 + "</div>";	
	}
}

