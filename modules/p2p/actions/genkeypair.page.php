<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>awareNet - peer servers</title>
	<content>
		[[:theme::navtitlebox::width=570::label=Generate An RSA Key Pair (4096 bit openSSH):]]

		[`|lt]h2[`|gt]Warning[`|lt]/h2[`|gt]
		[`|lt]p[`|gt]This key pair is now in your browser[`|sq]s cache.  This is intended for testing and development purposes only.[`|lt]/p[`|gt]

		[`|lt]h3[`|gt]New Key, PEM formatted[`|lt]/h3[`|gt]
		[`|lt]form name=[`|sq]setRSAKey[`|sq] method=[`|sq]POST[`|sq] action=[`|sq][`|pc][`|pc]serverPath[`|pc][`|pc]p2p/settings/[`|sq][`|gt]
		[`|lt]input type=[`|sq]hidden[`|sq] name=[`|sq]action[`|sq] value=[`|sq]changeSettings[`|sq] /[`|gt]
		[`|lt]textarea rows=[`|sq]10[`|sq] cols=[`|sq]60[`|sq] name=[`|sq]p2p[`|us]server[`|us]pubkey[`|sq] style=[`|sq]width: 100[`|pc][`|sc][`|sq][`|gt][`|pc][`|pc]publicKeyTxt[`|pc][`|pc][`|lt]/textarea[`|gt]
		[`|lt]br/[`|gt]
		[`|lt]textarea rows=[`|sq]10[`|sq] cols=[`|sq]60[`|sq] name=[`|sq]p2p[`|us]server[`|us]prvkey[`|sq] style=[`|sq]width: 100[`|pc][`|sc][`|sq][`|gt][`|pc][`|pc]privateKeyTxt[`|pc][`|pc][`|lt]/textarea[`|gt]
		[`|lt]input type=[`|sq]submit[`|sq] value=[`|sq]Apply to this peer [`|gt][`|gt][`|sq] /[`|gt]
		[`|lt]/form[`|gt]
		[`|lt]br/[`|gt][`|lt]hr/[`|gt]

		<form name='frmGenKey' method='POST' action='%%serverPath%%p2p/genkeypair/generate_yes/'>
			<input type='submit' value='Make A New Key' />
			<p><small><b>Warning:</b> This may take awhile on old systems.</small></p>
		</form>
	</content>
	<nav1>
		[[:sync::addserverform:]]
		[[:admin::subnav:]]
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
