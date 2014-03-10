<? /*
<page>
<template>onecol.template.php</template>
<content>[[:theme::navtitlebox::width=900::label=Code:]]
[`|lt]div class=[`|sq]indent[`|sq][`|gt]
[`|lt]h1[`|gt]Edit Code[`|lt]/h1[`|gt]
[[:code::editform::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]
[[:theme::navtitlebox::width=570::label=Attachments - Images:]]
[[:images::uploadmultiple::refModule=code::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]br/[`|gt][`|lt]br/[`|gt]
[[:theme::navtitlebox::width=570::label=Attachments - Files:]]
[[:files::uploadmultiple::refModule=code::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]br/[`|gt][`|lt]br/[`|gt]</content>
<title>:: kapenta :: code :: edit item ::</title>
<script>function cleanCode() {
  theTxt = document.getElementById([`|sq]txtContent[`|sq])[`|sc]
  temp = theTxt.value[`|sc]
  temp = temp.replace(/\\n/g, [`|sq]***HNEWLINE***[`|sq])[`|sc]
  temp = temp.replace(/\\r/g, [`|sq]***HCR***[`|sq])[`|sc]
  temp = temp.replace(/\\t/g, [`|sq]***HTAB***[`|sq])[`|sc]
  theTxt.value = temp[`|sc]
}</script>
<nav1></nav1>
<nav2></nav2>
<banner></banner>
<head></head>
<menu1>[[:home::menu:]]</menu1>
<menu2>[[:code::menu:]]</menu2>
<section></section>
<subsection></subsection>
</page>
*/ ?>