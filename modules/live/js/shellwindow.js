//--------------------------------------------------------------------------------------------------
//*	chat window code
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	chat window object
//--------------------------------------------------------------------------------------------------

function Live_ShellWindow(serverPath, userName, hWnd) {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	
	this.serverPath = serverPath;
	this.userName = userName;
	this.hWnd = hWnd;
	this.history = new Array();
	this.bufferLength = 20;
	this.bufferPointer = 0;
	
	this.divHistory = document.getElementById('divHistory');
	this.taPrompt = document.getElementById('content');

	//----------------------------------------------------------------------------------------------
	//	send a command entered by the current user (xmlHTTPRequest POST)
	//----------------------------------------------------------------------------------------------

	this.sendCmd = function(cmdStr) {
		//------------------------------------------------------------------------------------------
		//	make a new shell cmd object and add it to the array
		//------------------------------------------------------------------------------------------
		var cmdUID = createUID();			//%	uniquely identifies this command [string]				

		var newCmd = new Live_ShellCmd(cmdUID, cmdStr);

		if (this.bufferPointer >= this.bufferLength) {
			// bump first item off start of queue
			for (var idx = 0; idx < (this.bufferLength - 1); idx++) {
				this.history[idx] = this.history[idx + 1];
			}
			this.bufferPointer = this.bufferLength;
		} else {
			this.bufferPointer++;
		}

		this.history[this.bufferPointer] = newCmd;

		this.divHistory.innerHTML = this.divHistory.innerHTML + newCmd.toHtml(); 
		this.scrollToBottom();

		//------------------------------------------------------------------------------------------
		//	send to server via xmlHTTPRequest POST
		//------------------------------------------------------------------------------------------
		var params = 'cmd=' + base64_encode(cmdStr);
		var sendUrl = this.serverPath + 'live/docmd/';

		var http = new XMLHttpRequest();
		http.open("POST", sendUrl, true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.setRequestHeader("Content-length", params.length);
		http.setRequestHeader("Connection", "close");
		http.historyUID = cmdUID;		

		http.onreadystatechange = function() {
			if(http.readyState == 4 && http.status == 200) {
				var resultDiv = document.getElementById('result' + http.historyUID);
				resultDiv.innerHTML = http.responseText;
				kshellwindow.scrollToBottom();

				if (http.responseText.indexOf("<!-- kshellwindow.clearHistory() -->") > 0) {
					kshellwindow.clearHistory();
				}

				if (http.responseText.indexOf("<!-- cmd.error() -->") > 0) {
					//resultDiv.class = "chatmessagered";
				}

				if (http.responseText.indexOf("<!-- cmd.ok() -->") > 0) {
					//resultDiv.class = "chatmessagegreen";
				}

			}
		}
		http.send(params);
	}

	//----------------------------------------------------------------------------------------------
	//.	find array index of a message given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a chat message [string]
	//returns: array index if found, -1 if not found [int]

	this.getHistoryIndex = function(UID) {
		for (var i in this.history) { if (this.history[i].UID == UID) { return i; } }
		return -1;
	}

	//----------------------------------------------------------------------------------------------
	//.	scroll history to the bottom
	//----------------------------------------------------------------------------------------------

	this.scrollToBottom = function() {
		this.divHistory.scrollTop = this.divHistory.scrollHeight;
	}

	//----------------------------------------------------------------------------------------------
	//.	clear history
	//----------------------------------------------------------------------------------------------

	this.clearHistory = function() {
		this.history = new Array();
		this.divHistory.innerHTML = '';
	}

	//----------------------------------------------------------------------------------------------
	//.	handle key presses in the textarea
	//----------------------------------------------------------------------------------------------
	//TODO: figure out how to reference this without the global

	this.taKeyUp = function(e) {
		var keyID = (window.event) ? event.keyCode : e.keyCode;
		switch(keyID) {
			case 38:	alert('arrow up');		break;
			case 40:	alert('arrow down');	break;
		}

		if ((kshellwindow.taPrompt.value.indexOf("\n") != -1)||(kshellwindow.taPrompt.value.indexOf("\r") != -1)) {
			kshellwindow.sendCmd(kshellwindow.taPrompt.value);
			kshellwindow.taPrompt.value = '';
		}
	}

	this.taPrompt.onkeyup = this.taKeyUp;
}

//--------------------------------------------------------------------------------------------------
//	object representing a single chat messages
//--------------------------------------------------------------------------------------------------
//note: this is a client-side version of Live_Chat objects.

function Live_ShellCmd(UID, cmdStr) {
	
	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	//TODO: look into setting up a time delta (UTC) on chat messages to correct for TZ differences
	this.UID = UID;					//_	UID of this Live_ShellCmd object [string]
	this.cmdStr = cmdStr;			//_ command as enteredthis.divHistory.innerHTML by user [string]
	this.state = 'new';				//_	current state of execution [string]

	//----------------------------------------------------------------------------------------------
	//	convert to HTML
	//----------------------------------------------------------------------------------------------
	//returns: html as displayed in the messages page of the chat window [string]

	this.toHtml = function() {
		var html = '';		//% return value [string]
		var dc = 'chatmessagegray';
		dc = 'chatmessageblack';

		if ('sent' == this.state) { var dc = 'chatmessagered'; }
		if ('done' == this.state) { var dc = 'chatmessagegreen'; }

		var throbber = jsServerPath 
			+ 'themes/clockface/images/throbbersm.gif';

		html = html + "<div class='" + dc + "' id='hist" + this.UID + "'>"
			+ "<small><b>" + this.cmdStr + "</b></small><br/>"
			+ "<div id='result" + this.UID  + "'>"
			+ "<img src='" + throbber + "' align='right' /><br/>"
			+ "</div>"
			+ "</div>\n";

		return html;
	}

	//----------------------------------------------------------------------------------------------
	//	convert to params string for HTTP POST
	//----------------------------------------------------------------------------------------------
	//returns: urlencoded POST body [string]

	this.toFormData = function() {
		var params = 'cmd=' + base64_encode(this.cmdStr);		//% POST body [string]
		return params;
	}

}
