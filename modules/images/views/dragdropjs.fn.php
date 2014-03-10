<?

//--------------------------------------------------------------------------------------------------
//|	javascript for drag and drop image upload
//--------------------------------------------------------------------------------------------------


function images_dragdropjs($args) {
		global $kapenta;
		global $user;
		global $kapenta;
		global $theme;


	$html = '';							//%	return value [string]
	$refModule = '';					//%	name of a kapenta module [string]
	$refModel = '';						//%	type of object which will own any images [string]
	$refUID = '';						//%	UID of object which may own images [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//TODO: this	
	if ((true == array_key_exists('tags', $args)) && ('yes' == $args['tags'])) { $tags = 'yes'; }
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];
	
	if (false == $kapenta->moduleExists($refModule)) { return '(no such ref module)'; }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { return '(no such ref object)'; }

	if (false == $user->authHas($refModule, $refModel, 'images-add', $refUID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the form
	//----------------------------------------------------------------------------------------------

	$html .= "
	<div class='inlinequote' id='divDropTarget'><br/>
		<div id='divDropConsole'></div>
		<img 
			id='imgPreview'
			src='%%serverPath%%themes/%%defaultTheme%%/images/icons/no.png' 
			width='50' />Drop Files Here<br/>
		<div id='divProgress'></div>
	</div>
	<div id='divLog'></div>
	<script language='Javascript'>

	//----------------------------------------------------------------------------------------------
	//|	object for uploading files
	//----------------------------------------------------------------------------------------------

	function KSerialUploader() {

		//------------------------------------------------------------------------------------------
		//	member variables
		//------------------------------------------------------------------------------------------
		this.state = 'idle';
		this.files = new Array();
		this.console = document.getElementById('divDropConsole');
		this.currentFile = 0;

		this.sendUrl = '%%serverPath%%images/ajaxupload/';

		//------------------------------------------------------------------------------------------
		//.	add a file for uploading
		//------------------------------------------------------------------------------------------

		this.addFile = function(file) {
			log('adding file: ' + file.name);
			file.kstatus = 'queued';
			file.kprogress = 0;
			this.files[this.files.length] = file;
			this.printStatus();
			this.startNext();
		}

		//------------------------------------------------------------------------------------------
		//.	start next upload
		//------------------------------------------------------------------------------------------
		
		this.startNext = function() {
			if ('idle' != this.state) { return false; }
			log('starting next upload...');

			// hide upload and download forms
			divUpload = document.getElementById('divImgUpload');
			if (divUpload) { divUpload.style.visibility = 'hidden'; }
			divDownload = document.getElementById('divImgUpload');
			if (divDownload) { divDownload.style.visibility = 'hidden'; }

			for (var i = 0; i < this.files.length; i++) {
				if ('queued' == this.files[i].kstatus) {
					log('next queued file: ' + i);
					this.files[i].kstatus = 'loading';

					var reader = new FileReader();	

					// init the reader event handlers
					//reader.onprogress = handleReaderProgress;

					reader.onloadend = function(evt) {
						var img = document.getElementById('imgPreview');
						kuploader.addImage(evt.target.result);
						kuploader.upload(evt.target.result)
					}

					// begin the read operation
					reader.readAsDataURL(this.files[i]);

					this.currentFile = i;
					this.state = 'loading';
					return true;
				}
			}

			return false;
		}

		//------------------------------------------------------------------------------------------
		//.	upload a file
		//------------------------------------------------------------------------------------------

		this.upload = function(data) {
			log('uploading file data: ' + data.length + ' bytes');
			this.files[this.currentFile].kstatus = 'sending...';

			//--------------------------------------------------------------------------------------
			//	send to server via xmlHTTPRequest POST
			//--------------------------------------------------------------------------------------

			var params = ''
				+ 'refModule=" . $refModule . "'
				+ '&refModel=" . $refModel . "'
				+ '&refUID=" . $refUID . "'
				+ '&fileName=' + encodeURIComponent(this.files[this.currentFile].name)
				+ '&contents=' + data;

			var http = new XMLHttpRequest();
			http.open('POST', this.sendUrl, true);
			http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			http.setRequestHeader('Content-length', params.length);
			http.setRequestHeader('Connection', 'close');

			http.onprogress = function(e) {
				if (e.lengthComputable) {  
					//evt.loaded the bytes browser receive
					//evt.total the total bytes seted by the header
				    var percentComplete = (e.loaded / e.total)*100; 
					log('percent complete: ' + percentComplete);
					//alert('percent complete: ' + percentComplete);
				}
			}

			http.onreadystatechange = function() {
				if (4 == http.readyState) {
					if (200 == http.status) {
						log('server response: ' + http.responseText);
						kuploader.uploadComplete();
					} else {
						kuploader.uploadFailed();
					}
				}
			}

			http.send(params);
	    }

		//------------------------------------------------------------------------------------------
		//.	on upload complete
		//------------------------------------------------------------------------------------------
		
		this.uploadComplete = function() {
			log('upload complete');
			this.files[this.currentFile].kstatus = 'done';
			this.printStatus();
			this.state = 'idle';
			var stillWorking = this.startNext();
			if (false == stillWorking) { window.location.reload(); }
		}

		//------------------------------------------------------------------------------------------
		//.	on upload failure
		//------------------------------------------------------------------------------------------
		
		this.uploadFailed = function() {
			log('upload failed');
			this.files[this.currentFile].kstatus = 'upload failed';
			this.printStatus();
			this.state = 'idle';
			var stillWorking = this.startNext();
			if (false == stillWorking) { window.location.reload(); }
		}

		//------------------------------------------------------------------------------------------
		//.	add an image to the progress div
		//------------------------------------------------------------------------------------------

		this.addImage = function(data) {
			var divProgress = document.getElementById('divProgress');
			var imgUID = 'img' + kutils.createUID();
			divProgress.innerHTML = divProgress.innerHTML 
				+ \"<img src='\" + data + \"' height='100' />\";
		}

		//------------------------------------------------------------------------------------------
		//.	set progress bar of current upload
		//------------------------------------------------------------------------------------------

		this.setProgress = function(bytes) {
			//TODO: progress bars
		}

		//------------------------------------------------------------------------------------------
		//.	print current status to console
		//------------------------------------------------------------------------------------------
		
		this.printStatus = function() {
			var html = 'Status: ' + this.state + '<br>';		

			for (var i = 0; i < this.files.length; i++) {
				html = html + 'file: ' + this.files[i].name;
				html = html + ' (' + this.files[i].kprogress + ')' + '<br>';
				html = html + ' (' + this.files[i].kstatus + ')' + '<br>';
			}

			this.console.innerHTML = html;
			kutils.resizeIFrame();
		}

	}

	kuploader = new KSerialUploader();

	//----------------------------------------------------------------------------------------------
	//|	log messages to console
	//----------------------------------------------------------------------------------------------

	lastMsg = '';

	function log(msg) {
		var theDiv = document.getElementById('divLog');
		if (msg == lastMsg) { return; }
		//theDiv.innerHTML = theDiv.innerHTML + msg + \"<br>\";
		lastMsg = msg;
		kutils.resizeIFrame();
	}

	//----------------------------------------------------------------------------------------------
	//|	event handlers
	//----------------------------------------------------------------------------------------------

	function dragEnter(evt) {
		log('fired event: dragEnter');
		evt.stopPropagation();
		evt.preventDefault();
	}

	function dragExit(evt) {
		log('fired event: dragExit');
		evt.stopPropagation();
		evt.preventDefault();
	}

	function dragOver(evt) {
		log('fired event: dragOver');
		evt.stopPropagation();
		evt.preventDefault();
	}


	function drop(evt) {
		log('fired event: drop');
		evt.stopPropagation();
		evt.preventDefault();

		var files = evt.dataTransfer.files;
		var count = files.length;
		log('dropped ' + count + ' files...');

		// Only call the handler if 1 or more files was dropped.
		if (count > 0) { 
			for (var i = 0; i < files.length; i++) {
				var file = files[i];
				log('adding file: ' + file.name);
				kuploader.addFile(file);
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//|	set up the drag-drop target div
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function ddPageInit() {
		log('initializing page...');

		if ('undefined' === typeof FileReader) { 
			log('Browser does not support this feature.'); 
			var divDT = document.getElementById('divDropTarget');
			divDT.innerHTML = \"<b>Your browser does not support drag and drop for files.</b><br/>\"
				+ \"This feature is known to work with recent versions of Mozilla FireFox,  \";
				+ \"Chromium and Google Chrome, though necessary features are planned \";
				+ \"for upcoming versions of Microsoft Internet Explorer and Apple Safari.\";

			return false;
		} else {
			log('FileReader API present...');
		}

		kutils.resizeIFrame();

		var dropbox = document.getElementById('divDropTarget')

		// init event handlers
		log('adding event handlers...');
		dropbox.addEventListener('dragenter', dragEnter, false);
		dropbox.addEventListener('dragexit', dragExit, false);
		dropbox.addEventListener('dragover', dragOver, false);
		dropbox.addEventListener('drop', drop, false);

		log('init complete');
		return true;
	}
		
	ddPageInit();

	</script>
	";

	return $html;
}



?>
