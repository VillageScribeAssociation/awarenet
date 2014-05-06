<? /*
<page>
	<template>window.template.php</template>
	<content>

		[[:theme::navtitlebox::label=Preview:]]
		[[:videos::player::videoUID=%%UID%%::like=no:]]
		<br/>

		[[:theme::navtitlebox::label=Edit Video Details::toggle=divEditForm:]]
		<div id='divEditForm' class='indent'>
		[[:videos::editvideoform::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]::return=editmodal:]]
		</div>
		<div class='footer'></div>
		<br/>

		[[:theme::navtitlebox::label=Video Thumbnail::toggle=divVideoThumbnail::hidden=yes:]]
		<div id='divVideoThumbnail' style='display: none;'>
		<iframe name='videoThumb' class='consoleif' id='ifVideoThumb'
		  src='%%serverPath%%/images/uploadsingle/refModule_videos/refModel_videos_video/refUID_%%UID%%/category_thumb/'
		  width='100%' height='400px' frameborder='0' ></iframe>
		</div>
		<div class='footer'></div>
        <br/>

		[[:theme::navtitlebox::label=Move to Gallery::toggle=divChangeGallery::hidden=yes:]]
		<div id='divChangeGallery' class='indent' style='display: none;'>
		[[:videos::changegalleryform::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		</div>
		<div class='footer'></div>
		<br/>

	</content>
	<title>edit an image</title>
	<script>
		if ((window.parent) && (window.parent.Live_ReloadAttachments)) {
			window.parent.Live_ReloadAttachments();
		}

		kwnd.onLoad = function() { kutils.resizeIFrame(); }
	</script>
	<nav1></nav1>
	<nav2></nav2>
	<banner></banner>
	<head></head>
	<menu1></menu1>
	<menu2></menu2>
	<section></section>
	<subsection></subsection>
</page>
*/ ?>
