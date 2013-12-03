<? /*
<page>
	<template>window.template.php</template>
	<content>
		<div id='divImgContainer' style='background-color: #000; position: absolute;'>
		<img id='imgMain' src='%%serverPath%%/images/width570/%%raUID%%' /><br/>
		</div>
		<div id='divEditForm' style='position: absolute;'>
		[[:images::editform::raUID=%%raUID%%::return=%%return%%:]]
		</div>
	</content>
	<title>edit an image</title>
	<script>
		kwnd.onResize = function() {
			var wH = $(window).height();
			var wW = $(window).width();
			var efTop = (wH - $('#divEditForm').height() + 30);
			var containerAspect = wW / efTop;

			$('#divImgContainer').width(wW);
			$('#divImgContainer').height(efTop);

			$('#divEditForm').width(wW - 8);
			$('#divEditForm').css('top', efTop);
			$('#divEditForm').css('background-color', $(document.body).css('background-color'));

			if (containerAspect > kwnd.imgAspect) {
				//	pad on sides
				$('#imgMain').css('margin-top', 0);
				$('#imgMain').height(efTop);

				var scaleWidth = $('#imgMain').height() * kwnd.imgAspect;

				$('#imgMain').width(scaleWidth);
				$('#imgMain').css('margin-left', (wW - scaleWidth) / 2);
			} else {
				//	width is greater than window, scale vertically
				var scaleHeight = (wW / kwnd.imgAspect);

				$('#imgMain').css('margin-left', 0);
				$('#imgMain').css('width', wW);
				$('#imgMain').css('height', scaleHeight);
				$('#imgMain').css('margin-top', (efTop - scaleHeight) / 2);

				//kwnd.kwm.windows[kwnd.hWnd].setStatus('sh: ' + scaleHeight);
			}
		}

		kwnd.onLoad = function() {
			kwnd.imgAspect = $('#imgMain').width() / $('#imgMain').height();
			//alert('loaded: ' + $(document).height() + ' aspect: ' + kwnd.imgAspect);
			this.onResize();
		}

		if ((window.parent) && (window.parent.Live_ReloadAttachments)) {
				window.parent.Live_ReloadAttachments();
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
