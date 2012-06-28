<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - blogs - [`|pc][`|pc]userName[`|pc][`|pc] ::</title>
	<content>
		[[:theme::navtitlebox::label=Aggregated Blogs ([`|pc][`|pc]userName[`|pc][`|pc])::width=570:]]

		[[:live::river::rivermodule=moblog::riverview=summarylist::riverpagevar=page::allow=num|userUID|pagination::num=5::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]::pagination=no:]]

	</content>
	<nav1>
		%%newPostForm%%
		[[:theme::navtitlebox::label=Author:]]
		[[:images::default::refModule=users::refUID=[`|pc][`|pc]userUID[`|pc][`|pc]::size=width300:]]
		[[:users::summarynav::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
		[`|lt]br/[`|gt]

		[[:theme::navtitlebox::label=Blogs By School:]]
		[[:moblog::schoolstatsnav:]]
		[`|lt]br/[`|gt]

		[[:theme::navtitlebox::label=Most Popular Posts:]]
		[[:moblog::listpopularnav::num=10:]]

		<br/>
	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head>
	[`|lt]link
		rel="alternate"
		type="application/rss+xml"
		title="RSS (%%userName%%)"
		href="%%serverPath%%moblog/rss/%%userRa%%" 
	/[`|gt]
	</head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:users::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=People - ::link=/users/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]userName[`|pc][`|pc] - ::link=/users/profile/[`|pc][`|pc]userRa[`|pc][`|pc]:]]
[[:theme::breadcrumb::label=Blog::link=/moblog/blog/[`|pc][`|pc]userRa[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>
