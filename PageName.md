# awareNet improvements #


This page is for collecting ideas and improvements to the awareNet software which are neither bugs nor feature requests, but which would be nice to have eventually.  These items are not prioritised at present, but have been suggested as improvements which we hope to get to as soon as time permits.

## Comments on Groups ##

Groups at present are simply profiles for clubs, sports teams, etc.  They can be used to send notifications to all group members via announcements, and show membership on member's user profiles, but that's about it.

Requests have been made to allow chat and interaction among regular group members on the group page via comments (like profile wall posts), and to allow group admins to add links to the sidebar for projects, blog posts, forum threads, etc related to the group.

These features are easily added.

## Block insertion in WYSWYG editor ##

This would allow images, videos, etc to be added from a control on the editor console, populated on use/query by AJAX request.  This would get arround all sorts of issues and untidiness with the current drag-drop system, which does not work so well on IE, and at present only for pictures (not videos or anything else).

This might also be applied to adding blocks to wiki pages, which most users do nto know how to do.

## Slideshow integration ##

awareNet currently supports both javascript and iframe slideshow views of image galleries, or of images attached to any other object.  Can be added via WYSWYG editor or block to embed a slideshow as one would an image or video.

## Video embed codes ##

These are confusing as they are, and there's not documentation or tips on the site explaining how to embed a video in a project, forum post, comment, blog post, etc.

Propose adding an 'embed' link below the player, similar to how YouTube does. This would bring up a hidden div with instructions and a block tag.  This block tag can then be rewritten contextually if the video must be scaled down (eg, comments in the nav).

## HTML Trim ##

Extend the HTML parser to remove break tags and empty paragraphs from the beginning and end of text submitted from the WYSWYG editor.  There are a few learners who mash the enter key and leave lots of empty lines (sometimes hundreds) after text they add (blog posts, forum posts).  This messes up list displays and notifications, pushing the next list entry way below the fold.  It may be interesting to discover why they are doing this, since this behavior is observed across time and at different schools.