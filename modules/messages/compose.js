
//-------------------------------------------------------------------------------------------------
//	javascript for adding recipients to messages
//-------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
//	add a recipient, given use UID and html block
//-------------------------------------------------------------------------------------------------

function addRecipient(usrUID, usrHtml) {
	var divRecip = document.getElementById('composeDisplayRecip');

	if (mcHasRecipient(usrUID) == true) { return false; }	// prevent duplicates

	//---------------------------------------------------------------------------------------------
	//	add to txtRecipients
	//---------------------------------------------------------------------------------------------

	var txtRecip = document.getElementById('txtRecipients');
	txtRecip.value = txtRecip.value + '|' + usrUID + '|';

	//---------------------------------------------------------------------------------------------
	//	add html
	//---------------------------------------------------------------------------------------------

	html = "<div id='usrd" + usrUID + "'>" 
		 + "<table noborder><tr><td>"
		 + "<a href='#' onClick=\"mcRemoveRecipient('" + usrUID + "')\">"
		 + "<img src='/themes/clockface/icons/arrow_x.jpg' border='0' /><a/>"
		 + "</td><td>" + usrHtml + "</td></tr></table></div>";

	divRecip.innerHTML = divRecip.innerHTML + html;
}

//-------------------------------------------------------------------------------------------------
//	check if a given recipient has already been added to this message
//-------------------------------------------------------------------------------------------------

function mcHasRecipient(usrUID) {
	var txtRecip = document.getElementById('txtRecipients');
	var allRecips = txtRecip.value;
	if (allRecips.indexOf(usrUID) > 0) { return true; }
	return false;
}

//-------------------------------------------------------------------------------------------------
//	remove a recipient from the message
//-------------------------------------------------------------------------------------------------

function mcRemoveRecipient(usrUID) {
	//---------------------------------------------------------------------------------------------
	//	remove from txtRecipients
	//---------------------------------------------------------------------------------------------
	var txtRecip = document.getElementById('txtRecipients');
	txtRecip.value = txtRecip.value.replace(new RegExp(usrUID, "g"), '');

	//---------------------------------------------------------------------------------------------
	//	remove from display
	//---------------------------------------------------------------------------------------------
	var divBlock = document.getElementById('usrd' + usrUID);
	divBlock.innerHTML = '';

	var divParent = document.getElementById('composeDisplayRecip');
	divParent.removeChild(divBlock);
}
