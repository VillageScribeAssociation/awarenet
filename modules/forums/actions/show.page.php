<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]forumTitle[`|pc][`|pc] (forum)</title>
	<content>
		<div class='block'>
		[[:theme::navtitlebox::width=570::label=Forum:]]
		[[:forums::show::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		</div>
		<br/>

		<div class='block'>
		[[:theme::navtitlebox::width=570::label=Discussions:]]
		[[:live::river::mod=forums::view=showthreads::pv=pageno::allow=forumUID|num::forumUID=%%UID%%::num=20:]]
		</div>
		[`|lt]br/[`|gt]

		[[:forums::newthreadform::forumUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]

	</content>
	<nav1>
		<div class='block'>
		[[:theme::navtitlebox::label=Moderators::toggle=divModerators::hidden=yes:]]
		[`|lt]div id=[`|sq]divModerators[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
Teachers act as moderators for now, we[`|sq]ll see how that works.  Student moderators may be enabled in future.
		[`|lt]/div[`|gt]
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Busiest Discussions::toggle=divBusiestDiscussions:]]
		[`|lt]div id=[`|sq]divBusiestDiscussions[`|sq][`|gt]
		[[:forums::busiestthreads:]]
		[`|lt]/div[`|gt]
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=At This School:]]
		[[:forums::summarylistnav::school=[`|pc][`|pc]school[`|pc][`|pc]:]]
		</div>
		[`|lt]br/[`|gt]
	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:forums::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Forums - ::link=/forums/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]forumTitle[`|pc][`|pc]::link=/forums/[`|pc][`|pc]raUID[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>
