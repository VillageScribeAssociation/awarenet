//==================================================================================================
//	awareNet/kapenta chat client
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//	chat client object	
//--------------------------------------------------------------------------------------------------
//	note that this should be instantiated as a global object called kchatclient

function Live_ChatClient() {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	this.discussions = new Array();			//_	set of chat discussions objects [array]
	this.lastMessage = 0;					//_ datetime when of last message [int]
	this.userUID = jsUserUID;				//_ UID of current user/chat client owner [string]

	//----------------------------------------------------------------------------------------------
	//	receive a message
	//----------------------------------------------------------------------------------------------
	//arg: msg64 - base64 encoded chat message
	//note: when decoded message has format UID|fromUID|toUID|status|createdOn|content64

	this.receive = function(msg64) {
		var parts = msg64.split('|');
		for (var i in parts) { parts[i] = base64_decode(parts[i]); }

		var UID = parts[0];					//%	UID of Live_Chat message [string]
		var fromUID = parts[1];				//%	UID of user which send the message [string]
		var toUID = parts[2];				//% UID of recipient user [string]
		var msg = parts[3];					//% message content [string]
		var sent = parts[4];				//% date when message was sent (?) [string]
		var state = parts[5];				//% current state of message [string]
		var createdOn = parts[6];			//%	date when message was created [string]

		var partnerUID = fromUID;
		if (fromUID == this.userUID) { partnerUID = toUID; }

		//------------------------------------------------------------------------------------------
		//	look up discussion to which this message belongs, or create it if not found
		//------------------------------------------------------------------------------------------
		var dIdx = this.getDiscussionIdx(this.userUID, partnerUID);
		if (-1 == dIdx) { dIdx = this.createDiscussion(partnerUID); }

		//------------------------------------------------------------------------------------------
		//	add message to this discussion
		//------------------------------------------------------------------------------------------
		this.discussions[dIdx].addMessage(UID, fromUID, toUID, msg, state, createdOn);

		//------------------------------------------------------------------------------------------
		//	update lastMessage
		//------------------------------------------------------------------------------------------
		this.lastMessage = createdOn;
		klive.lastChatMessage = createdOn;
	}

	//----------------------------------------------------------------------------------------------
	//	create a new discussion with the specified user
	//----------------------------------------------------------------------------------------------
	//arg: partnerUID - UID of a Users_User object [string]
	//returns: index of discussion in this.discussions array [int]

	this.createDiscussion = function(partnerUID) {
		var idx = this.discussions.length;
		var newDisc = new Live_ChatDiscussion(partnerUID, idx);
		this.discussions[idx] = newDisc;
		return idx;
	}

	//----------------------------------------------------------------------------------------------
	//	find the index of a discussion given partnerUID
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object [string]
	//arg: partnerUID - UID of a Users_User object [string]
	//returns: array index of discussion if found, -1 on failure [int]

	this.getDiscussionIdx = function(userUID, partnerUID) {
		for (var i in this.discussions) {
			var uUID = this.discussions[i].userUID;
			var pUID = this.discussions[i].partnerUID;
			if ((uUID == userUID) && (pUID == partnerUID)) { return i; }
			if ((pUID == userUID) && (uUID == partnerUID)) { return i; }
		}
		return -1;
	}

	//----------------------------------------------------------------------------------------------
	//	tell the discussion object that a window is ready to receive messages
	//----------------------------------------------------------------------------------------------
	//arg: hWnd - index of a window [int]
	//returns: true on success, false on failure [bool]

	this.registerLoaded = function(hWnd) {
		for (var i in this.discussions) {
			if (this.discussions[i].hWnd == hWnd) { this.discussions[i].registerLoaded(); }
			return true;
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//	end a discussion
	//----------------------------------------------------------------------------------------------
	//arg: dIdx - index of a discussion [int]
	//returns: true on success, false on failure [bool]

	this.endDiscussion = function(dIdx) {
		//alert('ending discussion:' + dIdx);

		//------------------------------------------------------------------------------------------
		//	end and remove the discussion
		//------------------------------------------------------------------------------------------
		this.discussions[dIdx].end();
		this.removeDiscussion(dIdx);

		//------------------------------------------------------------------------------------------
		//	close the window
		//------------------------------------------------------------------------------------------
		kwindowmanager.windows[this.discussions[dIdx].hWnd].close();
	}

	//----------------------------------------------------------------------------------------------
	//	remove a discussion from the array
	//----------------------------------------------------------------------------------------------
	//arg: dIdx - index of a discussion [int]
	//returns: true on success, false on failure [bool]
	
	this.removeDiscussion = function(dIdx) {
		//TODO: improve
		this.discussions[dIdx].index = -1;
		this.discussions[dIdx].userUID = '';	
		this.discussions[dIdx].partnerUID = '';
		this.discussions[dIdx].loaded = false;		
	}

}

//--------------------------------------------------------------------------------------------------
//	discussion (represents window object)
//--------------------------------------------------------------------------------------------------

function Live_ChatDiscussion(partnerUID, dIdx) {
	klive.log('Creating chat discussion: ' + partnerUID);
	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	this.index = dIdx;					//_	index of this object in discussions array [int]
	this.userUID = jsUserUID;			//_	UID of Users_User object (current user) [string]		
	this.partnerUID = partnerUID;		//_	UID of Users_User object (chat partner) [string]
	this.loaded = false;				//_	set to true when chat iframe loads [bool]
	this.buffer = new Array();			//_ buffer [array]

	this.chatFrameUrl = jsServerPath + 'live/chat/' + partnerUID;		//_ URL of chat [string]
	this.hWnd = kwindowmanager.createWindow('Chat', this.chatFrameUrl);	//_	window handle [int]
	this.windowUID = kwindowmanager.windows[this.hWnd].UID;				//_	UID of window [string]

	var imgCloseBtn = document.getElementById('wClose' + this.windowUID);
	imgCloseBtn.onclick = function() { kchatclient.endDiscussion(dIdx); }

	//----------------------------------------------------------------------------------------------
	//	check whether a discussion involves a particular user
	//----------------------------------------------------------------------------------------------
	//arg: userUID - user we're curious about [string]
	//returns: true if discussion involves this user [bool]

	this.involves = function(userUID) {
		if ((this.myUID == userUID) || (this.partnerUID == userUID)) { return true; }
		return false;		
	}

	//----------------------------------------------------------------------------------------------
	//	register
	//----------------------------------------------------------------------------------------------

	this.registerLoaded = function() {
		this.loaded = true;
		for(var i in this.buffer) {
			var msgAry = this.buffer[i];
			this.sendToWindow(msgAry[0], msgAry[1], msgAry[2], msgAry[3], msgAry[4], msgAry[5]);
		}
		this.buffer = new Array();		// clear buffer after they're all sent
	}

	//----------------------------------------------------------------------------------------------
	//	send a message to the window, or buffer if window is still loading
	//----------------------------------------------------------------------------------------------

	this.addMessage = function(UID, fromUID, toUID, msg, state, createdOn) {
		if (true == this.loaded) { this.sendToWindow(UID, fromUID, toUID, msg, state, createdOn); }
		this.buffer[this.buffer.length] = new Array(UID, fromUID, toUID, msg, state, createdOn);
	}

	//----------------------------------------------------------------------------------------------
	//	pass message to chat client in iframe
	//----------------------------------------------------------------------------------------------

	this.sendToWindow = function(UID, fromUID, toUID, msg, state, createdOn) {
		var wUID = kwindowmanager.windows[this.hWnd].UID;			//% window UID [string]
		var ifc = document.getElementById('c' + wUID);				//% iframe [object]

		//alert('ifc:' + ifc.contentWindow.kchatwindow.partnerName);

		ifc.contentWindow.kchatwindow.addMessage(UID, fromUID, toUID, msg, state, createdOn);
	}

	//----------------------------------------------------------------------------------------------
	//	end a discussion
	//----------------------------------------------------------------------------------------------

	this.end = function() {
		//------------------------------------------------------------------------------------------
		//	POST to chat server the specifics of the conversation we want to end
		//------------------------------------------------------------------------------------------
		var params = 'userUID=' + escape(this.userUID) + '&partnerUID=' + escape(this.partnerUID);
		//alert('sending: ' + params);

		var req = new XMLHttpRequest();  
		req.open('POST', klive.serverPath + 'live/endchat/', true);  
		req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		//req.setRequestHeader('Content-length', params.length);
		req.setRequestHeader('Connection', 'close'); 
		req.onreadystatechange = function (aEvt) {  
			klive.log('[i] ended chat: ' + req.status);
			if ((4 == req.readyState) && (200 == req.status))  { 
				//alert(req.responseText) 
			}
		}

		req.send(params);
	}

}

