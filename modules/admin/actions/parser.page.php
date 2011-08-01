<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>awareNet - HTML Parser(admin)</title>
	<content>[[:theme::navtitlebox::label=Settings:]]
[`|lt]h2[`|gt]Settings[`|lt]/h2[`|gt]
[`|lt]form name=[`|sq]frmParserSettings[`|sq] method=[`|sq]POST[`|sq] action=[`|sq][`|pc][`|pc]serverPath[`|pc][`|pc]admin/parser/[`|sq][`|gt]
  [`|lt]input type=[`|sq]hidden[`|sq] name=[`|sq]action[`|sq] value=[`|sq]setAllowedTags[`|sq] /[`|gt]
  [`|lt]b[`|gt]Allowed Tags:[`|lt]/b[`|gt] (please separate entity names with a pipe)[`|lt]br/[`|gt]
  [`|lt]textarea rows=[`|sq]5[`|sq] cols=[`|sq]80[`|sq] name=[`|sq]tags[`|sq][`|gt][`|pc][`|pc]allowedTags[`|pc][`|pc][`|lt]/textarea[`|gt][`|lt]br/[`|gt]
  [`|lt]input type=[`|sq]submit[`|sq] value=[`|sq]Set allowed Tags [`|gt][`|gt][`|sq] /[`|gt]
[`|lt]/form[`|gt]
[`|lt]hr/[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Test:]]
[`|lt]h2[`|gt]Test HTML Parser[`|lt]/h2[`|gt]
[`|lt]form name=[`|sq]frmParserTest[`|sq] method=[`|sq]POST[`|sq] action=[`|sq][`|pc][`|pc]serverPath[`|pc][`|pc]admin/parser/[`|sq][`|gt]
  [`|lt]input type=[`|sq]hidden[`|sq] name=[`|sq]action[`|sq] value=[`|sq]testParser[`|sq] /[`|gt]
  [`|lt]b[`|gt]Paste sample HTML here:[`|lt]/b[`|gt][`|lt]br/[`|gt]
  [`|lt]textarea rows=[`|sq]20[`|sq] cols=[`|sq]80[`|sq] name=[`|sq]raw[`|sq][`|gt][`|pc][`|pc]sampleHtml[`|pc][`|pc][`|lt]/textarea[`|gt][`|lt]br/[`|gt]
  [`|lt]input type=[`|sq]submit[`|sq] value=[`|sq]clean [`|gt][`|gt][`|sq] /[`|gt]
[`|lt]/form[`|gt]
[`|lt]hr/[`|gt]
[`|lt]br/[`|gt]

%%testResult%%

</content>
	<nav1>[[:admin::subnav:]]</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:admin::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Administration - ::link=/admin/:]]
[[:theme::breadcrumb::label=Console::link=/admin/:]]</breadcrumb>
</page>

*/ ?>
