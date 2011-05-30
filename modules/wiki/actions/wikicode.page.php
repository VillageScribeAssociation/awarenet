<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - wikicode help</title>
	<content>[[:theme::navtitlebox::width=570::label=Wikicode Help:]]
[`|lt]div class=[`|sq]indent[`|sq][`|gt]
[`|lt]h1[`|gt]How to Use Wikicode[`|lt]/h1[`|gt]

[`|lt]p[`|gt]The following items apply to lines of text.  There should be no whitespace before any of them.[`|lt]/b[`|gt]

[`|lt][`|table] class=[`|sq]wireframe[`|sq][`|gt]
  [`|lt]tr[`|gt]
    [`|lt]td[`|gt][`|lt]pre[`|gt]==Section Heading==[`|lt]/pre[`|gt] [`|lt]/td[`|gt]
    [`|lt]td[`|gt]Use this to divide documents into sections.[`|lt]/td[`|gt]
  [`|lt]/tr[`|gt]
  [`|lt]tr[`|gt]
    [`|lt]td[`|gt][`|lt]pre[`|gt]===Subsection===[`|lt]/pre[`|gt] [`|lt]/td[`|gt]
    [`|lt]td[`|gt]Use this to order large sections of a document.[`|lt]/td[`|gt]
  [`|lt]/tr[`|gt]
  [`|lt]tr[`|gt]
    [`|lt]td[`|gt][`|lt]pre[`|gt]=(=Anything=)=[`|lt]/pre[`|gt] [`|lt]/td[`|gt]
    [`|lt]td[`|gt]Adds this line to the infobox, rather than the main content of the page.  Does not work for talk pages.[`|lt]/td[`|gt]
  [`|lt]/tr[`|gt]
  [`|lt]tr[`|gt]
    [`|lt]td[`|gt][`|lt]pre[`|gt]*list item[`|lt]/pre[`|gt] [`|lt]/td[`|gt]
    [`|lt]td[`|gt]For making bulleted lists (first order).[`|lt]/td[`|gt]
  [`|lt]/tr[`|gt]
  [`|lt]tr[`|gt]
    [`|lt]td[`|gt][`|lt]pre[`|gt]**list item[`|lt]/pre[`|gt] [`|lt]/td[`|gt]
    [`|lt]td[`|gt]For making bulleted lists (second order).[`|lt]/td[`|gt]
  [`|lt]/tr[`|gt]
  [`|lt]tr[`|gt]
    [`|lt]td[`|gt][`|lt]pre[`|gt]***list item[`|lt]/pre[`|gt] [`|lt]/td[`|gt]
    [`|lt]td[`|gt]For making bulleted lists (third order).[`|lt]/td[`|gt]
  [`|lt]/tr[`|gt]
[`|lt]/[`|table][`|gt]

[`|lt]p[`|gt]The following phpBBCode markup can appear anywhere in the text.  Remember to close tags.[`|lt]/p[`|gt]

[`|lt][`|table] class=[`|sq]wireframe[`|sq][`|gt]
  [`|lt]tr[`|gt]
    [`|lt]td[`|gt][`|lt]pre[`|gt][i]italic text[/i][`|lt]/pre[`|gt] [`|lt]/td[`|gt]
    [`|lt]td[`|gt]Italic (phpBB format).[`|lt]/td[`|gt]
  [`|lt]/tr[`|gt]
  [`|lt]tr[`|gt]
    [`|lt]td[`|gt][`|lt]pre[`|gt][b]bold[/b][`|lt]/pre[`|gt] [`|lt]/td[`|gt]
    [`|lt]td[`|gt]Boldface text (phpBB format).[`|lt]/td[`|gt]
  [`|lt]/tr[`|gt]
[`|lt]/[`|table][`|gt]

[`|lt]p[`|gt]The following are not standard phpBBCode, but for consistency take a the same form.[`|lt]/p[`|gt]

[`|lt][`|table] class=[`|sq]wireframe[`|sq][`|gt]
  [`|lt]tr[`|gt]
    [`|lt]td[`|gt][`|lt]pre[`|gt][box]multiple lines[/box][`|lt]/pre[`|gt] [`|lt]/td[`|gt]
    [`|lt]td[`|gt]An indented box, for blockquotes, code snippets, etc.[`|lt]/td[`|gt]
  [`|lt]/tr[`|gt]
  [`|lt]tr[`|gt]
    [`|lt]td[`|gt][`|lt]pre[`|gt][pre]multiple lines[/pre][`|lt]/pre[`|gt] [`|lt]/td[`|gt]
    [`|lt]td[`|gt]Preformatted text: linefeeds, tabs and spaces are preserved.[`|lt]/td[`|gt]
  [`|lt]/tr[`|gt]
  [`|lt]tr[`|gt]
    [`|lt]td[`|gt][`|lt]pre[`|gt][small]multiple lines[/small][`|lt]/pre[`|gt] [`|lt]/td[`|gt]
    [`|lt]td[`|gt]Makes text smaller.[`|lt]/td[`|gt]
  [`|lt]/tr[`|gt]
[`|lt]/[`|table][`|gt]

[`|lt]p[`|gt]All links are [`|create]d the same way, in double square brackets with a pipe separating ref/href from the link[`|sq]s caption.  If a link points to a non-existant wiki page it will turn red.[`|lt]/p[`|gt]

[`|lt][`|table] class=[`|sq]wireframe[`|sq][`|gt]
  [`|lt]tr[`|gt]
    [`|lt]td[`|gt][`|lt]pre[`|gt][[recordAlias|some label]][`|lt]/pre[`|gt] [`|lt]/td[`|gt]
    [`|lt]td[`|gt]A link to a wiki page.[`|lt]/td[`|gt]
  [`|lt]/tr[`|gt]
  [`|lt]tr[`|gt]
    [`|lt]td[`|gt][`|lt]pre[`|gt][[http://link.com/x|some label]][`|lt]/pre[`|gt] [`|lt]/td[`|gt]
    [`|lt]td[`|gt]A link to an off-wiki page.[`|lt]/td[`|gt]
  [`|lt]/tr[`|gt]
[`|lt]/[`|table][`|gt]

[`|lt]h2[`|gt]Block Tags[`|lt]/h2[`|gt]

[`|lt]p[`|gt]The wiki can make use of functionality and content transcluded from other Kapenta modules by adding [`|lt]i[`|gt]block tags[`|lt]/i[`|gt] to a module.  Images, files and video are all embedded this way, as are more complicated objects such slideshows.  Any module that is part of a Kapenta installation may provide blocks which can be used by the wiki, depending on the permissions set by an administrator.[`|lt]/p[`|gt]

[`|lt]h2[`|gt]Differences to MediaWiki[`|lt]/h2[`|gt]

[`|lt]ul[`|gt]
  [`|lt]li[`|gt]Internal and external links are [`|create]d exactly the same, in double brackets with a pipe separating [[ref|label]].[`|lt]/li[`|gt]
  [`|lt]li[`|gt]Kapenta Wiki does not use templates, in their place, we have the infobar, which is by default the secondary navigation bar on this sites template.[`|lt]/li[`|gt]
  [`|lt]li[`|gt]phpBB format is used to mark up bold and italic text.  This is because double single quotes ([`|sq][`|sq]italic[`|sq][`|sq] in MediaWiki) are also used to represent the null string in php, and I use that a lot.[`|lt]/li[`|gt]
[`|lt]/ul[`|gt]

[`|lt]h2[`|gt]FAQ[`|lt]/h2[`|gt]

[`|lt]b[`|gt]Q. Why not just use a WYSIWG editor, like other Kapenta modules?[`|lt]/b[`|gt][`|lt]br/[`|gt]
A. [`|lt]a href=[`|sq]http://www.i3g.hs-heilbronn.de/attach/Ver[`|pc]C3[`|pc]B6ffentlichungen/What+you+see+is+Wiki.pdf[`|sq][`|gt]Christoph Sauer explains.[`|lt]/a[`|gt][`|lt]br/[`|gt]
[`|lt]br/[`|gt]

[`|lt]/div[`|gt]</content>
	<nav1></nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:wiki::menu::raUID=no:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Wiki - ::link=/wiki/:]]
[[:theme::breadcrumb::label=WikiCode Reference::link=/wiki/wikicode/:]]</breadcrumb>
</page>

*/ ?>