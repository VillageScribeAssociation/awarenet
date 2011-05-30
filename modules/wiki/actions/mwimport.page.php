<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - wiki - import from mediawiki</title>
	<content>[[:theme::navtitlebox::width=570::label=Admin:]]

[`|lt]h1[`|gt]Import from MediaWiki[`|lt]/h2[`|gt]

[`|lt]p[`|gt]The following set of scripts allow wholesale import of a MediaWiki into Kapenta Wiki.[`|lt]/p[`|gt]

[`|lt]h2[`|gt]Step 1: Scan for Articles[`|lt]/h2[`|gt]

[`|lt]p[`|gt]This will scan the target wiki to [`|create] a list of all articles via the default API.[`|lt]/p[`|gt]

[`|lt]iframe name=[`|sq]mwScanIf[`|sq] id=[`|sq]mwScanFrame[`|sq]
    src=[`|sq][`|pc][`|pc]serverPath[`|pc][`|pc]wiki/mwscan/[`|sq]
    width=[`|sq]570[`|sq]  height=[`|sq]10[`|sq] frameborder=[`|sq]no[`|sq][`|gt]
[`|lt]/iframe[`|gt]

[`|lt]hr/[`|gt]

[`|lt]h2[`|gt]Step 2: Download Articles[`|lt]/h2[`|gt]

[`|lt]p[`|gt]Once we have a list of articles we download them one at a time.

[`|lt]h2[`|gt]Step 3: Extract Categories and Assets[`|lt]/h2[`|gt]
[`|lt]br/[`|gt][`|lt]br/[`|gt]</content>
	<nav1>
[[:theme::navtitlebox::label=About:]]
[`|lt]br/[`|gt]</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:wiki::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Wiki - ::link=/wiki/:]]
[[:theme::breadcrumb::label=Edit Talk Page::link=/wiki/edittalk/[`|pc][`|pc]raUID[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>