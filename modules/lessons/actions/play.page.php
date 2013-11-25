<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>onecol.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - Resources</title>
	<content>

		[[:theme::navtitlebox::label=Video Lessons:]]
		<h1>%%document_title%%</h1>


		<div class='block' style='position: absolute;' id='playerHover'>

			<script src='%%serverPath%%modules/videos/js/flowplayer-3.2.6.min.js'></script>

			<a
				href="%%serverPath%%%%fileName%%"
				style="display:block;width:%%width%%px;height:%%height%%px;"
				id="player%%UID%%r%%rand%%">
			</a>

			<script language="JavaScript">
			flowplayer(
				"player%%UID%%r%%rand%%", 
				{
					src:"%%serverPath%%modules/videos/flash/flowplayer-3.2.7.swf", 
					wmode:"opaque"
				},
				{
					plugins: {
					},

					clip: { 
					   autoPlay: false,
					   url: '%%serverPath%%%%fileName%%'
					},

					canvas:  { backgroundImage: 'url(%%coverImage%%)' }
				}
			);
			</script>

		</div>

		<script language='Javascript'>
			function lessons_center() {
				var cmLeft = ($(window).width() - 1000) / 2;
				$('#playerHover').css('left', cmLeft + 'px');
			}
			lessons_center();
		</script>
	
		<div id='divCMBacking' style='height: 620px;'>
		</div>

		<table noborder width='100%'>
		  <tr>
			<td width='50%' valign='top'>
				<div class='block'>
				<a href='%%serverPath%%%%fileName%%'>Download this video</a> to your computer or mobile device.
				</div>
				<div class='spacer'></div>
				<div class='block'>
				[[:theme::navtitlebox::label=Comment on this video:]]
				<div class='spacer'></div>
				[[:comments::addcommentformjs::refModule=lessons::refModel=lessons_stub::refUID=%%documentUID%%::bind=divVideoComments:]]
				</div>
				<div class='spacer'></div>
				<div class='block' id='divVideoComments'>
				[[:comments::list::refModule=lessons::refModel=lessons_stub::refUID=%%documentUID%%:]]
				</div>
			</td>
			<td width='50%' valign'top'>
				[[:lessons::videossamecourse::UID=%%courseUID%%:]]
			</td>
		  </tr>
		</table>


		<div id='divAttrib'>
			<h2>About</h2>
			<small>
				This video was produced by
				<a href='%%attriburl%%'>%%attribname%%</a>
				and is made availabe under the
				<a href='%%licenceurl%%'>%%licencename%%</a> licence.
			</small>
		</div>


	</content>
	<nav1>
	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:lessons::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb></breadcrumb>
</page>

*/ ?>
