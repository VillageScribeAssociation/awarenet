<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>:: awareNet :: admin :: p2p network ::</title>
	<content>
		[[:theme::navtitlebox::label=Peer Message Queues:]]
		<div class='spacer'></div>
		[[:p2p::listpeers:]]
		<br/>

		[[:theme::navtitlebox::label=File Downloads:]]
		[[:p2p::listdownloads:]]
		[[:p2p::cleardownloadsform:]]

	</content>
	<nav1>
		[[:p2p::autoconfigform:]]

		[[:p2p::addpeerform:]]

		[[:p2p::eventstats:]]

		[[:p2p::owndetails:]]

		<div class='block'>
		[[:theme::navtitlebox::label=Remote Shell:]]
		<input
			type='button'
			style='width: 100%;'
			value='Remote Shell &gt;&gt;'
			onClick="kwindowmanager.createWindow(
				'Remote Shell',
				'%%serverPath%%live/remoteshell/',
				800, 400,
				'%%serverPath%%modules/live/icons/console.png');
			"
		/>
		</div>
		<br/>

	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:admin::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb></breadcrumb>
</page>

*/ ?>
