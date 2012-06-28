<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - my notifications</title>
	<content>
		[[:theme::navtitlebox::width=570::label=Notifications:]]
		[`|lt]h1[`|gt]Activity Feed: [`|pc][`|pc]feed[`|pc][`|pc][`|lt]/h1[`|gt]
		[`|lt]br/[`|gt]

		[[:live::river::rivermodule=notifications::riverview=list::riverpagevar=page::allow=num|pagination|userUID::num=10::pagination=no::userUID=%%feed%%:]]
		[`|lt]br/[`|gt]

	</content>
	<nav1>
		[[:users::showfriendrequests::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
		[[:users::showrequestedfriends::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]

		[[:abuse::listnav::num=4:]]
	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head>
		<script src="%%serverPath%%modules/videos/js/flowplayer-3.2.6.min.js"></script>
		<script src="%%serverPath%%modules/notifications/js/notifications.js"></script>
	</head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:notifications::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>
		[[:theme::breadcrumb::label=Notifications - ::link=/notifications/:]]
		[[:theme::breadcrumb::label=%%feed%%::link=/notifications/by/%%feed%%:]]
	</breadcrumb>
</page>

*/ ?>
