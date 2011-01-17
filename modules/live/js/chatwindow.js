//--------------------------------------------------------------------------------------------------
//*	chat window code
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	chat window object
//--------------------------------------------------------------------------------------------------

function Live_ChatWindow(serverPath, msgDivId, userUID, userName, partnerUID, partnerName, hWnd) {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	
	this.serverPath = serverPath;
	this.msgDivId = msgDivId;
	this.userUID = userUID;
	this.userName = userName;
	this.partnerUID = partnerUID;
	this.partnerName = partnerName;
	this.hWnd = hWnd;

	this.messages = new Array();

	//----------------------------------------------------------------------------------------------
	//	send a message from the current user (xmlHTTPRequest POST)
	//----------------------------------------------------------------------------------------------

	this.addMessage = function(UID, fromUID, toUID, msg, state, createdOn) {
		var msgIndex = this.getMessageIndex(UID);

		if (-1 == msgIndex) {
			// new message, add it
			newMsg = new Live_ChatMessage(UID, fromUID, toUID, msg, state, createdOn);
			this.messages[this.messages.length] = newMsg;

		} else {
			// existing message, update state
			oldMsg = this.messages[msgIndex].state = state;		
		}

		this.refreshMessageDisplay();
		return msgIndex;
	}

	//----------------------------------------------------------------------------------------------
	//	send a message from the current user (xmlHTTPRequest POST)
	//----------------------------------------------------------------------------------------------

	this.sendMessage = function(msgTxt) {
		//------------------------------------------------------------------------------------------
		//	make a new chat message object and add it to the array
		//------------------------------------------------------------------------------------------
		var dt = new Date();
		var dtStr = dt.getFullYear() + '-' + dt.getMonth() + '-' + dt.getDate() + ' ' 
				  + dt.getHours() + ':' + dt.getMinutes() + ':' + dt.getSeconds();

		var toUID = this.partnerUID;
		var msgId = this.messages.length;
		var newMsg = new Live_ChatMessage(createUID(), this.userUID, toUID, msgTxt, 'sending', dtStr);
		this.messages[msgId] = newMsg;

		window.parent.kwindowmanager.windows[kchatwindow.hWnd].setStatus('Sending...');
		this.refreshMessageDisplay();

		//------------------------------------------------------------------------------------------
		//	send to server via xmlHTTPRequest POST
		//------------------------------------------------------------------------------------------

		var params = newMsg.toFormData();
		var sendUrl = this.serverPath + 'live/sendmsg/';

		var http = new XMLHttpRequest();
		http.open("POST", sendUrl, true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.setRequestHeader("Content-length", params.length);
		http.setRequestHeader("Connection", "close");

		http.onreadystatechange = function() {
			if(http.readyState == 4 && http.status == 200) {
				window.parent.kwindowmanager.windows[kchatwindow.hWnd].setStatus(http.responseText);
			}
		}
		http.send(params);
	}

	//----------------------------------------------------------------------------------------------
	//	find array index of a message given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a chat message [string]
	//returns: array index if found, -1 if not found [int]

	this.getMessageIndex = function(UID) {
		for (var i in this.messages) { if (this.messages[i].UID == UID) { return i; } }
		return -1;
	}

	//----------------------------------------------------------------------------------------------
	//	refresh message display (re-create and replace)
	//----------------------------------------------------------------------------------------------

	this.refreshMessageDisplay = function () {
		var divMsg = document.getElementById(this.msgDivId);
		var html = '';

		for (var i in this.messages) { html = html + this.messages[i].toHtml(); }
		divMsg.innerHTML = html;
		divMsg.scrollTop = divMsg.scrollHeight;
	}

	//----------------------------------------------------------------------------------------------
	//	end a chat (set status of all messages to 'dismissed') and close the window
	//----------------------------------------------------------------------------------------------

	this.endChat = function() {
		// resize window, hiding/destroying content and forms
	}

}

//--------------------------------------------------------------------------------------------------
//	object representing a single chat messages
//--------------------------------------------------------------------------------------------------
//note: this is a client-side version of Live_Chat objects.

function Live_ChatMessage(UID, fromUID, toUID, msg, state, createdOn) {
	
	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	//TODO: look into setting up a time delta (UTC) on chat messages to correct for TZ differences
	this.UID = UID;					//_	UID of this Live_Chat object [string]
	this.fromUID = fromUID;			//_ UID of Users_User object which created this message [string]
	this.toUID = toUID;				//_	UID of Users_User object this is addressed to [string]
	this.msg = msg;					//_	content of message [string]
	this.state = state;				//_	status of this message (new|sent) [string]
	this.createdOn = createdOn;		//_ datetime recorded when this message was sent [string]

	//----------------------------------------------------------------------------------------------
	//	convert to HTML
	//----------------------------------------------------------------------------------------------
	//returns: html as displayed in the messages page of the chat window [string]

	this.toHtml = function() {
		var html = '';		//% return value [string]
		var dc = 'chatmessagegray';

		if (this.fromUID == kchatwindow.userUID) { var dc = 'chatmessagegreen'; }
		if ('sending' == this.state) { var dc = 'chatmessagered'; }

		html = html + "<div class='" + dc + "'>"
					+ "<small>" + this.msg + "<br/>"
			 		+ "<span style='color: #bbb;'>"
					+ "<small>" + this.createdOn + "</small></span></small>"
					+ "</div>\n"

		return html;
	}

	//----------------------------------------------------------------------------------------------
	//	convert to params string for HTTP POST
	//----------------------------------------------------------------------------------------------
	//returns: urlencoded POST body [string]

	this.toFormData = function() {
		var params = '';
	
		params = params + "UID=" + this.UID
				+ "&fromUID=" + this.fromUID
				+ "&toUID=" + this.toUID
				+ "&msg=" + this.msg;

		return params;
	}

}
