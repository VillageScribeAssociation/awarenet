<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]forumTitle[`|pc][`|pc] (forum)</title>
	<content>
		[[:theme::navtitlebox::width=570::label=Forum:]]
		[[:forums::show::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		[[:theme::navtitlebox::width=570::label=Discussions:]]

		[[:live::river::mod=forums::view=showthreads::pv=pageno::allow=forumUID|num::forumUID=%%UID%%::num=20:]]
		[`|lt]br/[`|gt]

[[:theme::navtitlebox::width=570::label=Start a New Discussion::toggle=divNewDiscussion:]]
[`|lt]div id=[`|sq]divNewDiscussion[`|sq][`|gt]
[`|lt]br/[`|gt]
[[:forums::newthreadform::forumUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

</content>
	<nav1>[[:theme::navtitlebox::label=Moderators::toggle=divModerators::hidden=yes:]]
[`|lt]div id=[`|sq]divModerators[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
Teachers act as moderators for now, we[`|sq]ll see how that works.  Student moderators may be enabled in future.
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Busiest Discussions::toggle=divBusiestDiscussions:]]
[`|lt]div id=[`|sq]divBusiestDiscussions[`|sq][`|gt]
[[:forums::busiestthreads:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=At This School:]]
[[:forums::summarylistnav::school=[`|pc][`|pc]school[`|pc][`|pc]:]]
[`|lt]br/[`|gt]</nav1>
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
