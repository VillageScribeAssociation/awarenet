<? /*
<page>
	<template>window.template.php</template>
	<content>
		[[:theme::navtitlebox::label=This Item's Tags:]]
		[[:tags::listtags::refModule=[`|pc][`|pc]refModule[`|pc][`|pc]::refUID=[`|pc][`|pc]refUID[`|pc][`|pc]:]]
		[[:tags::addtagform::refModule=[`|pc][`|pc]refModule[`|pc][`|pc]::refModel=[`|pc][`|pc]refModel[`|pc][`|pc]::refUID=[`|pc][`|pc]refUID[`|pc][`|pc]:]]
		<br/>

		[[:theme::navtitlebox::label=Common Tags::toggle=divAddCommonTags::hidden=yes:]]
		<div id='divAddCommonTags' style='display: none;'>
		[[:tags::addcommontags:]]
		</div>
		<script>$('#txtAddTag').focus();</script>
	</content>
	<title>manage tags</title>
	<script>
		kwnd.onLoad = function() {
			//alert('resizing on load...');
			kutils.resizeIFrame();
			$('#txtAddTag').focus();
		}

		kwnd.onResize = function() {
			//alert('resizing');
			//kutils.resizeIFrame();
		}
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
