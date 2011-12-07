
//==================================================================================================
//*	Javascript for inline editing of projects
//==================================================================================================
//+	Sections may be independantly edited.  Clicking the 'edit' link causes sections to become
//+	'locked' to an individual user, this user sees the edit form while all other users see the
//+	section displayed without the edit form.
//+	
//+	Setting a lock on the edit form will cause the section to be replaced inline by the edit iframe
//+	for the user who set it.  'Save' and 'Cancel' both remove locks from sections.  Once the lock is
//+	set other users will not be able to edit this item.

//--------------------------------------------------------------------------------------------------
//|	object to assist with UI when editing projects
//--------------------------------------------------------------------------------------------------

function Projects_Editor(UID) {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	this.UID = UID;				//_	UID of a Projects_Project object [string]

	//----------------------------------------------------------------------------------------------
	//.	bump a section up in the order
	//----------------------------------------------------------------------------------------------
	//arg: sectionUID - UID of a Projects_Section object [string]	

	this.incrementSection = function(sectionUID) {
		var url = jsServerPath + 'projects/incsectionjs/';
		var params = 'action=incrementSection&UID=' + sectionUID;
		var projectUID = this.UID;		

		var cbFn = function(responseText, status) {
			var divId = 'divProject' + projectUID;
			var blockTag = '[[:projects::show::UID=' + projectUID + ':]]';
			klive.removeBlock(blockTag, false);						// clear block cache
			klive.bindDivToBlock(divId, blockTag, false);			// force reload
		}
		
		kutils.httpPost(url, params, cbFn);
	}


	//----------------------------------------------------------------------------------------------
	//.	bump a section down in the order
	//----------------------------------------------------------------------------------------------
	//arg: sectionUID - UID of a Projects_Section object [string]	

	this.decrementSection = function(sectionUID) {
		var url = jsServerPath + 'projects/decsectionjs/';
		var params = 'action=decrementSection&UID=' + sectionUID;
		var projectUID = this.UID;		

		var cbFn = function(responseText, status) {
			var divId = 'divProject' + projectUID;
			var blockTag = '[[:projects::show::UID=' + projectUID + ':]]';
			klive.removeBlock(blockTag, false);						// clear block cache
			klive.bindDivToBlock(divId, blockTag, false);			// force reload
		}
		
		kutils.httpPost(url, params, cbFn);
	}

	//----------------------------------------------------------------------------------------------
	//.	lock a section and show /editsection/ iframe
	//----------------------------------------------------------------------------------------------
	//arg: sectionUID - UID of a Projects_Section object [string]

	this.editSection = function(sectionUID) {
		var divId = 'divSection' + sectionUID;
		var blockTag = '[[:projects::editsectioninline::sectionUID=' + sectionUID + ':]]';
		klive.removeBlock(blockTag, false);						// clear block cache
		klive.bindDivToBlock(divId, blockTag, false);			// force reload		
	}

	//----------------------------------------------------------------------------------------------
	//.	delete a section
	//----------------------------------------------------------------------------------------------
	//arg: sectionUID - UID of a Projects_Section object [string]

	this.deleteSection = function(sectionUID, sectionTitle) {
		var check = confirm("Delete section: " + sectionTitle);
		if (check) {
			var url = kutils.serverPath + 'projects/deletesectionjs/';
			var params = "action=deleteSection&UID=" + sectionUID;
			var projectUID = this.UID;			

			var cbFn = function(responseText, status) {
				alert('Deleted section, reloading the page: ' + responseText);
				var divId = 'divProject' + projectUID;
				var blockTag = '[[:projects::show::UID=' + projectUID + ':]]';
				klive.removeBlock(blockTag, false);						// clear block cache
				klive.bindDivToBlock(divId, blockTag, false);			// force reload
			}

			kutils.httpPost(url, params, cbFn);

		} else {
			// nothing to do here
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	remove div height toggle from those with a content height less than 100px
	//----------------------------------------------------------------------------------------------
	//arg: sectionUID - UID of a Projects_Section object [string]

	this.checkToggle = function(sectionUID) {
		var theDiv = document.getElementById('divSC' + sectionUID);
		var thePara = document.getElementById('pSC' + sectionUID);
		var theSpan = document.getElementById('spanToggle' + sectionUID);
		if ((theDiv) && (theSpan)) {
			if (thePara.offsetHeight <= 100) {
				theDiv.style.height = '100%';
				theSpan.innerHTML = ''	
			}
		}		
	}

	//----------------------------------------------------------------------------------------------
	//.	toggle div height
	//----------------------------------------------------------------------------------------------
	//arg: sectionUID - UID of a Projects_Section object [string]

	this.toggle = function(sectionUID) {
		var theDiv = document.getElementById('divSC' + sectionUID);
		var theSpan = document.getElementById('spanToggle' + sectionUID);
		if ((theDiv) && (theSpan)) {
			if ('100px' == theDiv.style.height) {
				theDiv.style.height = '100%';
				theSpan.innerHTML = ''
				 + "<a href='javascript:void(0);'"
				 + " onClick=\"project.toggle('" + sectionUID + "')\""
				 + ">[show less]</a>";

			} else {
				theDiv.style.height = '100px';
				theSpan.innerHTML = ''
				 + "<a href='javascript:void(0);'"
				 + " onClick=\"project.toggle('" + sectionUID + "')\""
				 + ">[show more]</a>";
			}
		}
	}

}
