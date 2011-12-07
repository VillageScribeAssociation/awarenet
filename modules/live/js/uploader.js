//--------------------------------------------------------------------------------------------------
//*	object for uploading large files via AJAX, sending a file as small hashed parts one at a time
//--------------------------------------------------------------------------------------------------
//+	NOTE: this object expects itself to be called kupload

//arg: divId - id of a div to render the drop target into [string]
//arg: notifyUrl - URL to POOST details of newly uploaded files to [string]
//arg: refModule - name of a kapenta module to which files will be uploader [string]
//arg: refModel - type of object which will own files [string]
//arg: refUID - UID of object which will own files [string]

function Live_Uploader(divId, fileType, refModule, refModel, refUID) {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	this.divId = divId;							//_	id of drop target div [string]
	this.fileType = fileType;					//_	may be 'video', 'image' or 'all' [string]
	this.refModule = refModule;					//_	module to attach to [string]
	this.refModel = refModel;					//_	type of owner [string]
	this.refUID = refUID;						//_	UID of owner [string]
	this.state = 'idle';						//_	mode of operation [string]	
	this.files = new Array();					//_	allows multiple files to be queued up [array]
	this.currentFile = 0;						//_	files are sent one at a time [int]
	this.sendUrl = jsServerPath + '/live/';		//_	interface which accepts files and parts [string]
	this.lastMsg = '';							//_	last log message [string]
	this.partSize = 512 * 1024;					//_	send x * kb at a time [int]
	this.active = false;						//_	controls regular timer [bool]
	this.uploading = false;						//_	set to true while upload is in progress [bool]
	this.hashing = false;						//_	set to true while hash is in progress [bool]

	//----------------------------------------------------------------------------------------------
	//.	render the drag/drop div
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	this.render = function() {	
		var theDiv = document.getElementById(this.divId);
		if (!theDiv) { return false; }

		//------------------------------------------------------------------------------------------
		//	add drop target
		//------------------------------------------------------------------------------------------
		var ctl = ''
		 + "<div class='inlinequote' id='divDropTarget'><br/>\n"
		 + "<div id='divDropConsole' class='inlinequote'></div>\n"
		 + "<img \n"
		 + "	id='imgPreview'\n"
		 + "	src='" + jsServerPath + "themes/clockface/icons/no.png'\n"
		 + "	width='50' />Drop Files Here<br/>\n"
		 + "</div>\n"
		 + "<div id='divProgress'></div>\n"
		 + "<div id='divUploaderLog'></div>\n"
		 + "<script language='Javascript'>\n";

		theDiv.innerHTML = ctl;
		this.log('rendering control...');

		//------------------------------------------------------------------------------------------
		//	add drop target
		//------------------------------------------------------------------------------------------
		if ('undefined' === typeof FileReader) { 
			log('Browser does not support this feature.'); 
			var divDT = document.getElementById('divDropTarget');
			divDT.innerHTML = "<b>Your browser does not support drag and drop for files.</b><br/>"
				+ "This feature is known to work with recent versions of Mozilla FireFox, "
				+ "Chromium and Google Chrome, though necessary features are planned "
				+ "for upcoming versions of Microsoft Internet Explorer and Apple Safari.";

			return false;
		} else {
			this.log('FileReader API present...');
		}

		//------------------------------------------------------------------------------------------
		// create event handlers
		//------------------------------------------------------------------------------------------
		var that = this;

		var dragEnter = function(evt) {
			//that.log('fired event: dragEnter');	// noisy
			evt.stopPropagation();
			evt.preventDefault();
		}

		var dragExit = function(evt) {
			//that.log('fired event: dragExit');	// noisy
			evt.stopPropagation();
			evt.preventDefault();
		}

		var dragOver = function(evt) {
			//that.log('fired event: dragOver');	// noisy
			evt.stopPropagation();
			evt.preventDefault();
		}

		function drop(evt) {
			that.log('fired event: drop');
			evt.stopPropagation();
			evt.preventDefault();
	
			var files = evt.dataTransfer.files;
			var count = files.length;
			that.log('dropped ' + count + ' files...');

			// Only call the handler if 1 or more files was dropped.
			if (count > 0) { 
				for (var i = 0; i < files.length; i++) {
					var file = files[i];
					that.log('adding file: ' + file.name);
					that.addFile(file);
				}
			}
		}

		//------------------------------------------------------------------------------------------
		// attach event handlers
		//------------------------------------------------------------------------------------------
		this.log('adding event handlers...');
		var dropbox = document.getElementById('divDropTarget')

		dropbox.addEventListener('dragenter', dragEnter, false);
		dropbox.addEventListener('dragexit', dragExit, false);
		dropbox.addEventListener('dragover', dragOver, false);
		dropbox.addEventListener('drop', drop, false);

		this.log('init complete');

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	add a file for uploading
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	this.addFile = function(oFile) {
		for (var i in this.files) {
			if (this.files[i].name == oFile.name) {
				this.log('discarding duplicate file: ' + oFile.name);
				return false;
			}
		}

		this.log('adding file: ' + oFile.name);
		var upload = new Live_Upload(this, oFile);
		this.files[this.files.length] = upload;
		this.renderFiles();
		this.startCycle();
		return true;
	}


	//------------------------------------------------------------------------------------------
	//.	update files display
	//------------------------------------------------------------------------------------------
	
	this.renderFiles = function() {
		//this.log("Rendering progress div...");
		var divProgress = document.getElementById('divProgress');
		var html = '';

		if (divProgress) {
			for (var i in this.files) { html = html + this.files[i].render(); }
			divProgress.innerHTML = html;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	check if all downloads have been completed and we can refesh the page
	//----------------------------------------------------------------------------------------------

	this.checkAllDone = function() {
		var allDone = true;
		for (var idx in this.files) {
			var oFile = this.files[idx];
			if (
				(oFile.status) &&
				('sent' !== oFile.status) &&
				('invalid' !== oFile.status) &&
				('complete' !== oFile.status)
			) { allDone = false; }
		}

		// reload the iframe one all uploads are complete
		if (true == allDone) {
			this.log('All files sent - reloading page...');
			setTimeout("window.location.reload();", 3000);
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load a message into the debug div - development only TODO: remove
	//----------------------------------------------------------------------------------------------

	this.log = function(msg) {
		var theDiv = document.getElementById('divUploaderLog');
		if (theDiv) { 
			if (msg == this.lastMsg) { return; }
			theDiv.innerHTML = "<div class='chatmessageblack'>" + msg + "</div>";
			//theDiv.innerHTML = theDiv.innerHTML + "<div class='chatmessageblack'>" + msg + "</div>";
			this.lastMsg = msg;
		} else {
			alert(msg);
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	this object regularly pings itself until there is no more work to do
	//----------------------------------------------------------------------------------------------

	this.startCycle = function() {
		this.log('Starting cycle... (active: ' + this.active + ')');
		if (false == this.active) {
			this.active = true;
			this.cycle();
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	find something to do and do it, or set this.active = false
	//----------------------------------------------------------------------------------------------

	this.cycle = function() {
		if (false == this.active) { this.log('Cycle: uploader not active...'); return; }

		//------------------------------------------------------------------------------------------
		//	if we're not hashing anything, check if there is hashing to do
		//------------------------------------------------------------------------------------------
		if (false == this.hashing) {
			//this.log('Cycle: looking for parts to hash...');
			for (var i in this.files) {
				if ('hashing' == this.files[i].status) {
					this.files[i].hashNextPart();
					break;
				}
			}
		}

		//------------------------------------------------------------------------------------------
		//	if we're not uploading something, check if there is anything to upload
		//------------------------------------------------------------------------------------------
		if (false == this.uploading) {
			//this.log('Cycle: looking for parts to upload...');
			for (var i in this.files) {
				//----------------------------------------------------------------------------------
				//	first send manifests
				//----------------------------------------------------------------------------------
				if ('ready' == this.files[i].status) { this.files[i].sendManifest(); break; }

				//----------------------------------------------------------------------------------
				//	then tell the owner module about it
				//----------------------------------------------------------------------------------
				if ('sent' == this.files[i].status) { this.files[i].sendUploadComplete(); break; }

				//----------------------------------------------------------------------------------
				//	then send file parts (lowest prioroty for uploads)
				//----------------------------------------------------------------------------------
				if ('sending' == this.files[i].status) { this.files[i].sendNextPart(); break; }

			}
		}

		//TODO: when all files uploaded set this.active to false to stop the timer

		//--------------------------------------------------------------------------------------
		//	queue up next cycle
		//--------------------------------------------------------------------------------------
		var that = this;
		setTimeout(function() { that.cycle(); }, 250);

	} // end this.cycle()

}


//--------------------------------------------------------------------------------------------------
//	file upload metadata object
//--------------------------------------------------------------------------------------------------
//:	status progresses from 
//:	
//:		-	'new'		- calculate hashes of all parts
//:		-	'ready'		- ready for upload, send manifest
//:		-	'sending'	- manifest confirmed, send parts one at a time 
//:		-	'sent'		- all parts sent, send 'uploadcomplete'
//:		-	'complete'	- owner module has cleaned up, nothing further to do
//:		-	'error'		- unrecoverable error, eg auth failure or server out of disk space

//arg: oUploader - upload manager this upload belongs to [object]
//arg: oFile - a File object [object]

function Live_Upload(oUploader, oFile) {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	this.oUploader = oUploader;			//_	link to parent [object]
	this.oFile = oFile;					//_	File [object]

	this.UID = kutils.createUID();		//_	unique ID of this file part [string]
	this.status = 'hashing';			//_	status of this upload [string]
	this.size = oFile.size;				//_	size of this file [string]
	this.parts = new Array();			//_	array of Live_UploadPart objects [string]
	this.name = oFile.name;				//_	fileName [string]
	this.extension = '';				//_	file extension [string]
	this.partSize = oUploader.partSize;	//_	size of chunks to be uploaded [string]
	this.hash = '';						//_	sha1 hash of all part hashes [string]

	this.count = Math.ceil(this.size / this.partSize);			//_	number of parts [int]
	this.oProgress = new Live_ProgressBar(this.count, 0);		//_	progress bar [object]

	var msg = ''
	 + 'Creating file upload metadata object.<br/>'
	 + 'File name: ' + oFile.name + '<br/>'
	 + 'File size: ' + oFile.size + '<br/>';
	this.oUploader.log(msg);

	//----------------------------------------------------------------------------------------------
	//.	get extension
	//----------------------------------------------------------------------------------------------
	var dotparts = this.name.split('.');
	for (var i in dotparts) { this.extension = dotparts[i].toLowerCase(); }
	if (1 == dotparts.length) { this.extension = ''; }
	//alert('File Extension: ' + this.extension);

	//----------------------------------------------------------------------------------------------
	//.	check that the extension matches what we are expecting
	//----------------------------------------------------------------------------------------------

	this.checkExtension = function() {
		if ('video' == this.oUploader.fileType) {	// flash videos only
			if (
				('flv' == this.extension) ||
				('mp3' == this.extension) ||
				('mp4' == this.extension)
			) { return true; }
			return false;
		}

		if ('image' == this.oUploader.fileType) {	// images only
			if (
				('jpg' == this.extension) ||
				('jpeg' == this.extension) ||
				('gif' == this.extension) ||    
				('png' == this.extension)
			) { return true; }
			return false;
		}

		return false;								// no restriction on type
	}

	if (false == this.checkExtension()) { this.status = 'invalid'; }

	//----------------------------------------------------------------------------------------------
	//.	populate parts array
	//----------------------------------------------------------------------------------------------
	//arg: someFile - a File object, from which a FileReader can be made [object]

	this.populate = function() {
		for (var i = 0; i < this.count; i++) {
			//this.oUploader.log('Adding part ' + i);
			this.parts[i] = new Live_UploadPart(this, i, this.partSize);
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	hash next part
	//----------------------------------------------------------------------------------------------

	this.hashNextPart = function() {
		if (true == this.oUploader.hashing) { return; }		// one at a time
		for (var i in this.parts) {
			if ('new' == this.parts[i].status) { 
				this.parts[i].calcHash();
				return;										// found part to hash
			}
		}

		// lastly, make hash of entire file
		var allHashes = '';
		for (var i in this.parts) { allHashes = allHashes + this.parts[i].hash; }
		this.hash = kutils.sha1Hash(allHashes);
		this.status = 'ready';
	}

	//----------------------------------------------------------------------------------------------
	//.	make xml manifest of this file's contents
	//----------------------------------------------------------------------------------------------

	this.getManifest = function() {
		var parts = '';
		for (var i in this.parts) {
			parts = parts + ''
			 + "\t\t<part>\n"
			 + "\t\t\t<index>" + this.parts[i].index + "</index>\n"
			 + "\t\t\t<hash>" + this.parts[i].hash + "</hash>\n"
			 + "\t\t\t<size>" + this.parts[i].size + "</size>\n"
			 + "\t\t\t<status>waiting</status>\n"
			 + "\t\t</part>\n";
		}

		var xml = ''
		 + "<upload>\n"
		 + "\t<UID>" + this.UID + "</UID>\n"
		 + "\t<refModule>" + this.oUploader.refModule + "</refModule>\n"
		 + "\t<refModel>" + this.oUploader.refModel + "</refModel>\n"
		 + "\t<refUID>" + this.oUploader.refUID + "</refUID>\n"
		 + "\t<name>" + kutils.base64_encode(this.name) + "</name>\n"
		 + "\t<hash>" + this.hash + "</hash>\n"
		 + "\t<size>" + this.size + "</size>\n"
		 + "\t<count>" + this.count + "</count>\n"
		 + "\t<type>" + this.oUploader.fileType + "</type>\n"
		 + "\t<extension>" + this.extension + "</extension>\n"
		 + "\t<partsize>" + this.partSize + "</partsize>\n"
		 + "\t<parts>\n"
		 + parts
		 + "\t</parts>\n"
		 + "</upload>\n";

		return xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	send manifest to /live/uploadmanifest/
	//----------------------------------------------------------------------------------------------
	//;	server returns bitmap, eg <b>0110110101101</b>

	this.sendManifest = function() {
		//------------------------------------------------------------------------------------------
		//	call the action on the server
		//------------------------------------------------------------------------------------------
		var that = this;
		var manifest = this.getManifest();
		var url = jsServerPath + 'live/uploadmanifest/';

		//this.oUploader.log('<b>Manifest</b>:<br/><textarea rows=10 cols=80>' + manifest + '</textarea><br>');
		//this.oUploader.log('<b>Manifest64</b>:<br/>' + kutils.base64_encode(manifest) + '<br>');

		var params = ''
		 + 'action=addManifest'
		 + '&hash=' + this.hash
		 + '&refModule=' + this.oUploader.refModule
		 + '&refModel=' + this.oUploader.refModel
		 + '&refUID=' + this.oUploader.refUID
		 + '&manifest64=' + kutils.base64_encode(manifest);

		cbFn = function(responseText, status) { 
			if (200 == status) {
				that.status = 'sending';
				that.oUploader.log('/live/uploadmanifest/:<br/>' + kutils.htmlEntities(responseText));
				that.oUploader.uploading = false;
				that.readBitmap(responseText);
			} else {
				that.status = 'error';
				that.oUploader.log('WARNING:<br/>' + status + "\n" + responseText);
				that.oUploader.uploading = false;
			}
		}

		this.oUploader.uploading = true;
		kutils.httpPost(url, params, cbFn);
	}

	//----------------------------------------------------------------------------------------------
	//.	process file bitmap
	//----------------------------------------------------------------------------------------------
	//TODO: implement as an actual bitmap
	//arg: bitmap - curresponding to complete (1) and incomplete (0) parts on server [string]

	this.readBitmap = function(bmp) {
		var complete = true;
		bmp = bmp.replace(/<b>/, '');
		bmp = bmp.replace(/<\/b>/, '');
		this.oUploader.log('bitmap: ' + bmp);

		for (var i = 0; i < bmp.length; i++) {
			var bit = bmp.substr(i, 1);
			//this.oUploader.log('bitmap(' + i + '): ' + bit);
			if ('1' == bit) { this.parts[i].status = 'done'; }
			if ('0' == bit) { complete = false; }
		}

		if (true == complete) {
			this.status = 'sent';
			this.oUploader.renderFiles();
		}
		
	}

	//----------------------------------------------------------------------------------------------
	//.	send next part to server
	//----------------------------------------------------------------------------------------------

	this.sendNextPart = function() {
		this.oUploader.log('Sending next part of: ' + this.oFile.name);
		for (var i in this.parts) {
			if ('ready' == this.parts[i].status) {
				this.parts[i].send();
				return;
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	we're done sending parts, stitch pieces together and tell owner module
	//----------------------------------------------------------------------------------------------

	this.sendUploadComplete = function() {
		var that = this;
		var url = jsServerPath + 'live/uploadcomplete/';

		this.oUploader.log('<b>Upload complete:</b>' + this.oFile.name);

		var params = ''
		 + 'action=uploadComplete'
		 + '&filehash=' + this.hash
		 + '&refModule=' + this.oUploader.refModule
		 + '&refModel=' + this.oUploader.refModel
		 + '&refUID=' + this.oUploader.refUID;

		cbFn = function(responseText, status) { 
			if (200 == status) {
				that.oUploader.log('Finished uploading file: ' + that.oFile.name + '<br/>');
				that.status = 'complete';
				that.oUploader.log('/live/uploadcomplete/:<br/>' + kutils.htmlEntities(responseText));
				that.oUploader.uploading = false;
				that.oUploader.renderFiles();
				that.oUploader.checkAllDone();
				//TODO: deactiviate oUploader if all files have been uploaded

			} else {
				that.status = 'sent';
				that.oUploader.log('WARNING:<br/>' + status + "\n" + responseText);
				that.oUploader.uploading = false;
				that.oUploader.checkAllDone();
			}
		}

		this.oUploader.uploading = true;
		kutils.httpPost(url, params, cbFn);
	}

	//----------------------------------------------------------------------------------------------
	//.	make HTML view of this object
	//----------------------------------------------------------------------------------------------

	this.render = function() {
		var invalidMsg = '';				//%	for when users select files of wrong type [string]
		var complete = 0;					//%	number of complete parts [int]
		var fileImgUrl = jsServerPath + 'themes/clockface/icons/file.document.png';

		//------------------------------------------------------------------------------------------
		//	check progress of all parts
		//------------------------------------------------------------------------------------------
		if ('hashing' == this.status) {
			for (var i in this.parts) {	
				if ('ready' != this.parts[i].status) { complete = complete + 1; }
			}
			this.oProgress.set(complete);
			//this.oUploader.log('Progress ' + this.oProgress.progress + ' of ' + this.oProgress.max);
		}

		if ('sending' == this.status) {
			this.oProgress.fgcolor = '#99ccff';
			for (var i in this.parts) {	
				if ('done' != this.parts[i].status) { complete = complete + 1; }
			}
			this.oProgress.set(complete);
		}

		if ('sent' == this.status) {
			this.oProgress.fgcolor = '#99ff99';
			for (var i in this.parts) {	
				if ('done' != this.parts[i].status) { complete = complete + 1; }
			}
			this.oProgress.set(complete);
		}

		if ('invalid' == this.status) {
			this.oProgress.fgcolor = '#ffffff';
			this.oProgress.set(complete);
			invalidMsg = ''
			 + "<b>('" + this.extension + "' files are not supported by this module.)</b>";
		}

		//------------------------------------------------------------------------------------------
		//	make the block
		//------------------------------------------------------------------------------------------
		var pBar = this.oProgress.render();
		var html = ''
		 + "<div id='divStatus" + this.UID + "' class='inlinequote'>\n"
		 + "<table noborder width='100%'>\n"
		 + "  <tr>\n"
		 + "    <td width='50px'><img src='" + fileImgUrl + "'></td>\n"
		 + "    <td>\n"
		 + "      " + pBar + "\n"
		 + "      <b>Name: </b> " + this.name + "<br/>"
		 + "      <small><b>Size: </b> " + this.size + " "
		 + "      <b>Progress: </b> " + this.oProgress.percent + "% " + this.status + ' '
			 + invalidMsg + "</small><br/>"
		 + "    </td>\n"
		 + "  </tr>\n"
		 + "</table>\n"
		 + "</div>\n";
		//this.oUploader.log('Progress ' + this.oProgress.progress + ' of ' + this.oProgress.max + '<br/>' + html);
		return html;
	}

	//----------------------------------------------------------------------------------------------
	//	finish init
	//----------------------------------------------------------------------------------------------
	oUploader.log('Creating parts array.');
	this.populate();
}

//----------------------------------------------------------------------------------------------
//	file upload part metadata object
//----------------------------------------------------------------------------------------------

function Live_UploadPart(oUpload, index, partSize) {
	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	this.oUpload = oUpload;						//_	link to Live_Upload (parent) [object]

	this.index = index;							//_	index of this object in the parent array [int]
	this.start = (oUpload.partSize * index);	//_	starting offset in file [string]
	this.size = 0;								//_	raw part size [string]
	this.status = 'new';						//_	state of this part (hashing|ready|done) [string]
	this.hash = '';								//_	sha1 hash of raw part [string]

	//this.oUpload.oUploader.log('Adding part: ' + index + ' of ' + this.oUpload.oFile.name);

	//----------------------------------------------------------------------------------------------
	//	work out the size of this slice
	//----------------------------------------------------------------------------------------------
	this.size = this.oUpload.oFile.size - this.start;
	if (this.size > this.oUpload.partSize) { this.size = this.oUpload.partSize; }
	//this.oUpload.oUploader.log('Start byte: ' + this.start + ' Length: ' + this.size);

	//----------------------------------------------------------------------------------------------
	//.	set the hash
	//----------------------------------------------------------------------------------------------

	this.calcHash = function() {
		var oReader = new FileReader();
		var that = this;

		this.oUpload.oUploader.hashing = true;					//	only hash one at a time

    	// If we use onloadend, we need to check the readyState.
   		oReader.onloadend = function(evt) {
			if (evt.target.readyState == FileReader.DONE) { 	// DONE == 2
   		 		var textContent = evt.target.result;			//% file chunk [string]

				that.hash = kutils.sha1Hash(textContent);
				that.status = 'ready';

				//var data64 = base64.encode(textContent);
				//var data = kutils.base64_decode(data64);
				//var dataHash = kutils.sha1Hash(data);
				/*
				var msg = ''
				 + 'part' + that.index + 'hash: ' + that.hash + '<br>' 
				 + 'part' + that.index + 'data hash: ' + dataHash + '<br>' 
				 + 'textContent length: ' + textContent.length + '<br>'
				 + 'start: ' + that.start + ' size: ' + that.size + '<br>'
				 + "data64 (" + data64.length + "):"
				 + "<br><textarea rows='10' style='width: 100%'>" + data64 + "</textarea><br>"
				 + "data (" + data.length + "):"
				 + "<br><textarea rows='10' style='width: 100%'>" + data + "</textarea><br>";
				*/
				that.oUpload.oUploader.hashing = false;
				//that.oUpload.oUploader.log(msg);
				that.oUpload.oUploader.renderFiles();						//	redraw UI
			}
		};

		var blob = null;										//%	binary blob [object]
		var oFile = this.oUpload.oFile;							//% shortcut [object]

		if (oFile.slice) 		{ var blob = oFile.slice(this.start, this.size); }
		if (oFile.mozSlice) 	{ var blob = oFile.mozSlice(this.start, this.start + this.size); }
		if (oFile.webkitSlice)	{ var blob = oFile.webkitSlice(this.start, this.start + this.size); }

		if (blob) { oReader.readAsBinaryString(blob); }			//	NB: aynchronous
		else { this.oUpload.oUploader.log('<b>ERROR: blob is null.</b>'); }
	}


	//----------------------------------------------------------------------------------------------
	//.	read file chunk for sending to Live module, base64_encode it and pass to sendPart64
	//----------------------------------------------------------------------------------------------

	this.send = function() {
		var oReader = new FileReader();
		var that = this;

		this.oUpload.oUploader.uploading = true;						//	only send one at a time

    	// If we use onloadend, we need to check the readyState.
   		oReader.onloadend = function(evt) {
			if (evt.target.readyState == FileReader.DONE) { 			// DONE == 2
   		 		var textContent = evt.target.result;					//% file chunk [string]

				// convert string object into a binary object
                var byteArray = new Uint8Array(textContent.length);
                for (var i = 0; i < textContent.length; i++) {
                    byteArray[i] = textContent.charCodeAt(i) & 0xff;
                }

				var data64 = base64.encode(byteArray);	//%	b64 [string]

				var msg = ''
				 + 'part ' + that.index + ' data length: ' + byteArray.length + '<br/>' 
				 + 'b64 length length: ' + data64.length + '<br/>'
				 + 'start: ' + that.start + ' size: ' + that.size + '<br/>';

				that.oUpload.oUploader.log('b64 encoded part ' + that.index);
				that.sendPart64(data64);
			}
		};

		var blob = null;								//%	binary blob [object]
		var oFile = this.oUpload.oFile;					//% shortcut [object]
	
		//NOTE: slice takes *length*, mozSlice takes final offset

		if (oFile.slice) 		{ var blob = oFile.slice(this.start, this.size); }
		if (oFile.mozSlice) 	{ var blob = oFile.mozSlice(this.start, this.start + this.size); }
		if (oFile.webkitSlice)	{ var blob = oFile.webkitSlice(this.start, this.start + this.size); }

		if (blob) { oReader.readAsBinaryString(blob); }			//	NB: aynchronous

	}

	//----------------------------------------------------------------------------------------------
	//.	send base64_encoded part
	//----------------------------------------------------------------------------------------------
	//arg: data64 - base64 encoded file chunk [string]

	this.sendPart64 = function(data64) {
		var url = jsServerPath + 'live/uploadpart/';
		var that = this;

		this.oUpload.oUploader.log('<b>sending part</b>: ' + this.index + '<br>');

		var params = ''
			 + 'action=storePart'
			 + '&filehash=' + this.oUpload.hash
			 + '&parthash=' + this.hash
			 + '&index=' + this.index
			 + '&length=' + this.size
			 + '&part64=' + data64;

		var cbFn = function(responseText, status) { 
			if (200 == status) {
					that.oUpload.oUploader.log(that.oUpload.status + ' --- ' + responseText);
					that.oUpload.oUploader.log('/live/uploadpart/:<br/>' + kutils.htmlEntities(responseText));
					that.oUpload.oUploader.uploading = false;
					that.oUpload.readBitmap(responseText);
					that.oUpload.oUploader.renderFiles();		//	redraw UI
					//that.status = 'done';  					// <-- oUpload.readBitmap does this
				

				} else {
					alert(that.oUpload.status + ' --- ' + responseText);
					that.status = 'ready';	// retry this part
					that.status = 'done';	// to stop it hammering for now
					var x = 10 / 0;			// crash here
					alert('pause');
					that.oUpload.oUploader.log('WARNING:<br/>' + status + "\n" + kutils.htmlEntities(responseText));
					that.oUpload.oUploader.uploading = false;
			}
		}

		this.oUpload.oUploader.uploading = true;
		//this.oUpload.oUploader.log('sending: ' + params);
		kutils.httpPost(url, params, cbFn);
	}

}

//----------------------------------------------------------------------------------------------
//	progress bar
//----------------------------------------------------------------------------------------------

function Live_ProgressBar(min, max) {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	this.min = min;
	this.max = max;
	this.progress = min;
	this.percent = 0;
	this.bgcolor = '#aaaaaa';
	this.fgcolor = '#cc99ff';

	//----------------------------------------------------------------------------------------------
	//.	set the current progress
	//----------------------------------------------------------------------------------------------

	this.set = function(current) {
		this.progress = current;
		var dist = this.max - this.min;
		var done = this.progress - this.min;
		this.percent = Math.floor((done / dist) * 100);
	}

	//----------------------------------------------------------------------------------------------
	//.	render as HTML table
	//----------------------------------------------------------------------------------------------
	//returns: html table displaying current progress [string]

	this.render = function() {
		var html = ''
		 + "<table noborder width='100%'>"
		 + "<tr>"
		 + "<td width='" + this.percent + "%' height='10px' BGCOLOR='" + this.fgcolor + "'></td>"
		 + "<td width='" + (100 - this.percent) + "%' height='10px' BGCOLOR='" + this.bgcolor + "'></td>"
		 + "</tr>"
		 + "</table>";
	
		return html;
	}
}









//==================================================================================================
//	EXPERIMENTING WITH DIFFERENT BASE64 LIBRARY
//==================================================================================================
//
///
// This file implements base64 encoding and decoding.
// Encoding is done by the function base64Encode(), decoding
// by base64Decode(). The naming mimics closely the corresponding
// library functions found in PHP. However, this implementation allows
// for a more flexible use.
//
// This implementation follows RFC 3548 (http://www.faqs.org/rfcs/rfc3548.html),
// so the copyright formulated therein applies.
//
// Dr.Heller Information Management, 2005 (http://www.hellerim.de).
//
///



var base64 = function(){};

// provide for class information
base64.classID = function() {
  return 'system.utility.base64';
};

//disallow subclassing
base64.isFinal = function() {
  return true;
};

// original base64 encoding
base64.encString = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
// URL and file name safe encoding
base64.encStringS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';

/// BEGIN_DOC(base64).METHOD(encode)
///
// method String base64.encode(INPUTTYPE inp [, bool uc [, bool safe]])
//
// Encode input data into a base64 character string.
//
// Function arguments:
//     INPUTTYPE inp:        data to be encoded. INPUTTYPE may be String or Array.
//                           Any other INPUTTYPE results in an output value of null.
//                           If INPUTTYPE is String each character is converted into 
//                           two bytes each of which is encoded separately.
//     bool uc               Optional. If this parameter has a value of 'true' which is
//                           the default, code of each character is treated as a 16-bit
//                           entity (UniCode), i.e. as two bytes. Otherwise, the codes
//                           are truncated to one byte (8-bit character set) which
//                           may result in information loss. If INPUTTYPE is Array,
//                           the value of this parameter has no effect.
//     bool safe:            Optioanal. If this parameter is set to true, the standard base64 
//                           character set is replaced with a modified version where
//                           the characters '+' and '/' are replace with '-' and '_',
//                           repectively, in order to avoid problems with file system
//                           namings which otherwise could occur on some systems.
//                           By default, the value of this argument is assumed to be
//                           false.
// Return value:             The function returns a character string consisting of
//                           the base64 representaion of the input. Its length is a
//                           multiple of 4. If the encoding yields less than this
//                           the string is stuffed with the '=' character. In each case, 
//                           the string maybe empty but not null if no error occurred.
// Errors:                   Whenever an error occurs, null is returned. Parameter values
//                           not defined above are considered errors.
// Remarks:                  If the input array contains something different from
//                           a byte at some position the first 8 bits only of this entity are
//                           processed silently without returning an error, which probably
//                           results in garbage converted to base64.
//
/// END_DOC
base64.encode = function(inp, uc, safe) {
  // do some argument checking
  if (arguments.length < 1) return null;
  var readBuf = new Array();    // read buffer
  if (arguments.length >= 3 && safe != true && safe != false) return null;
  var enc = (arguments.length >= 3 && safe) ? this.encStringS : this.encString; // character set used
  var b = (typeof inp == 'string'); // how input is to be processed
  if (!b && (typeof inp != 'object') && !(inp instanceof Array)) return null; // bad input
  if (arguments.length < 2) {
    uc = true;                  // set default
  } // otherwise its value is passed from the caller
  if (uc != true && uc != false) return null;
  var n = (!b || !uc) ? 1 : 2;  // length of read buffer
  var out = '';                 // output string
  var c = 0;                    // holds character code (maybe 16 bit or 8 bit)
  var j = 1;                    // sextett counter
  var l = 0;                    // work buffer
  var s = 0;                    // holds sextett
  
  // convert  
  for (var i = 0; i < inp.length; i++) {  // read input
    c = (b) ? inp.charCodeAt(i) : inp[i]; // fill read buffer
    for (var k = n - 1; k >= 0; k--) {
      readBuf[k] = c & 0xff;
      c >>= 8;
    }
    for (var m = 0; m < n; m++) {         // run through read buffer
      // process bytes from read buffer
      l = ((l<<8)&0xff00) | readBuf[m];   // shift remaining bits one byte to the left and append next byte
      s = (0x3f<<(2*j)) & l;              // extract sextett from buffer
      l -=s;                              // remove those bits from buffer;
      out += enc.charAt(s>>(2*j));        // convert leftmost sextett and append it to output
      j++;
      if (j==4) {                         // another sextett is complete
        out += enc.charAt(l&0x3f);        // convert and append it
        j = 1;
      }
    }        
  }
  switch (j) {                            // handle left-over sextetts
    case 2:
      s = 0x3f & (16 * l);                // extract sextett from buffer
      out += enc.charAt(s);               // convert leftmost sextett and append it to output
      out += '==';                        // stuff
      break;
    case 3:
      s = 0x3f & (4 * l);                 // extract sextett from buffer
      out += enc.charAt(s);               // convert leftmost sextett and append it to output
      out += '=';                         // stuff
      break;
    default:
      break;
  }

  return out;
  
}

/// BEGIN_DOC(base64).METHOD(decode)
///
// method RETURNTYPE base64.decode(String inp [, enum outType [, bool safe [, bool lax]]])
//
// Encode input data into a base64 character string.
//
// Function arguments:
//     String inp:           base64 encoded data string to be decoded.
//     enum outType          Optional. This parameter specifies the type of the output and determines
//                           how the input data is to be interpreted.:
//                             0  - binary data; create a byte array (default)
//                             1  - 8-bit character string, assuming 1-byte characters encoded in inp
//                             2  - 16-bit (UniCode) character string, assuming 2-byte 
//                                  characters encoded in inp
//                           If 2 is passed to the function, but the number of base64 characters
//                           is odd, a value of null is returned.
//     bool safe             Optional. If this parameter is set to true, the standard base64 
//                           character set is replaced with a modified version where
//                           the characters '+' and '/' are replaced with '-' and '_',
//                           repectively, in order to avoid problems with file system
//                           namings which otherwise could occur on some systems.
//                           By default, the value of this argument is assumed to be
//                           false.
//     bool lax              Optional. If set to true, the function skips all input characters which
//                           cannot be processed. This includes the character '=', too, if
//                           it is followed by at least one different character before the string
//                           ends. However, if skipping infeasible characters amounts to a number
//                           of allowed base64 characters which is not amultiple of 4,
//                           this is considered an error and null is returned.
//                           If lax is set to false (the default), null is returned
//                           whenever an infeasible character is found.
//                           The purpose of this parameter is to give support in cases
//                           where data has been base64 encoded and later on was folded by
//                           some other software, e.g. CRLFs have been inserted in email.
//                           exchange.
// Return value:             The function's processing result value is stored in a string or in
//                           a byte array before it is returned, depending on the value 
//                           assigned to the type parameter. In each case, the value
//                           maybe empty but not null if no error occurred.
// Errors:                   Whenever an error occurs, null is returned. Parameter values
//                           not defined above are considered errors.
//
/// END_DOC

base64.decode = function(inp, outType, safe, lax) {

  // do some argument checking
  if (arguments.length < 1) return null;
  if (arguments.length < 2) outType = 0 ;// produce character array by default
  if (outType != 0 && outType != 1 && outType != 2) return null;
  if (arguments.length >= 3 && safe != true && safe != false) return null;
  var sEnc = (arguments.length >= 3 && safe) ? this.encStringS : this.encString;  // select encoding character set
  if (arguments.length >= 4 && lax != true && lax != false) return null;
  var aDec = new Object();                // create an associative array for decoding
  for (var p = 0; p < sEnc.length; p++) { // populate array
    aDec[sEnc.charAt(p)] = p;
  }
  var out = (outType == 0) ? new Array() : '';
  lax = (arguments.length == 4 && lax); // ignore non-base64 characters
  var l = 0;               // work area
  var i = 0;               // index into input
  var j = 0;               // sextett counter
  var c = 0;               // input buffer
  var k = 0;               // index into work area
  var end = inp.length;    // one position past the last character to be processed
  var C = '';
  // check input
  if (lax) {
    var inpS = '';         // shadow input
    var ignore = false;    // determines wether '=' must be counted
    var cnt = 0;
    for (var p = 1; p <= inp.length; p++) {    // check and cleanup string before trying to decode
      c = inp.charAt(end - p);
      if (c == '=') {
        if (!ignore) {
          if (++cnt > 1) ignore = true;
        } else {
          continue;
        }
      } else if (undefined != aDec[c]) { // the character is base64, hence feasible
        if (!ignore) ignore = true;      // no more '=' allowed
        inpS = c + inpS;                 // prepend c to shadow input
      }
    }
    for (var p = 0; p <= cnt; p++) {     // at most cnt '=''s were garbage, a number in 
      if (p == 2) return null;           // [inpS.length, inpS.length + cnt] must be a
      if ((inpS.length + cnt)%4 == 0) break;  // multiple of 4
    }
    if (inpS.length%4==1) return null;   // must be 0, 2, or 3 for inpS to contain correctly base64 encoded data
    inp = inpS;                          // inp now contains feasible characters only
    end = inp.length;
  } else {
    if (inp.length%4 > 0) return null;   // invalid length
    for (var p = 0; p < 2; p++) {        // search for trailing '=''s
      if (inp.charAt(end - 1) == '=') {
        end--;
      } else {
        break;
      }
    }
  }
  // convert
  for (i = 0; i < end; i++) {
    l <<= 6;                             // clear space for next sextett
    if (undefined == (c = aDec[inp.charAt(i)])) return null; // lax must be false at this place!
    l |= (c&0x3f);    // append it
    if (j == 0) {
      j++;    
      continue;                          // work area contains incomplete byte only
    }
    if (outType == 2) {
      if (k == 1) {                      // work area contains complete double byte
        out += String.fromCharCode(l>>(2*(3-j)));  // convert leftmost 16 bits and append them to string
        l &= ~(0xffff<<(2*(3-j)));       // clear the 16 processed bits
      }
      k = ++k%2;
    } else {                             // work area contains complete byte
      if (outType == 0) {
        out.push(l>>(2*(3-j)));          // append byte to array
      } else {
        out += String.fromCharCode(l>>(2*(3-j))); // convert leftmost 8 bits and append them to String
      }
      l &= ~(0xff<<(2*(3-j)));           // clear the 8 processed bits
    }
    j = ++j%4;                           // increment sextett counter cyclically
  }
  if (outType == 2 && k == 1) return null;  // incomplete double byte in work area

  return out;
}



