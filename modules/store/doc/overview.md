
    KINDRED

Kindred is a set of tools for creating, maintaining and viewing serverless websites backed by an abstract distributed data store (DDS).

These websites allow control by one or more trusted publishers, known as moderators, and accept user-generated content, maintained by one or more bot process operating anywhere on the internet, known as a sitebot.  Kindred websites take the form of indexes of files signed by a trusted publisher which may be rendered by a javascript client application running in a browser, or viewed through a gateway application operating over HTTP, Tor, I2P, Freenet, mobile app or any other publishing platform which supports HTML5.

New content maybe submitted to sites directly via the distributed data store, or by posting anywhere on the internet visible to a search engine, that may be found by the site bot.  For ease of display, indexing, structuring and versioning Kindred sites, content added to such sites will be one of the JSON formats described in these documents.  Kindred documents are composed in markdown format, not HTML.

The identity of kindred sites takes the form of the public key of the sitebot, and is the key to a key-value store such as Kademelia, where the value associated with this key will be the location of a Kindred site's main index.  This index will list additional documents and indexes which comprise the site, all of which will be signed by the sitebot, and can be downloaded by clients for consumption, search, navigation and to provide the metadata for further submissions.

    Creating and updating documents on a Kindred site

Documents are added to a Kindred site by inclusion one of the site indexes.  This is performed by the sitebot on discovering a document encrypted with the sitebot's public key and signed by a trusted contributor to the site.  Messages not signed by a trusted party may be discarded, or added to a moderation queue to be signed or ignored by the site's moderators.  The mechanism for discovery is left open, but for censorship resistance it will be assumed that every site Kindred site may specify a unique string of english words that be queried on any internet search engine, to discover new pages containing encrypted messages for the sitebot.

These messages may be posted anywhere on the internet, eg, forums, blogs, free hosting sites or specialized mixnet services.

On encountering a message signed by a moderator, the sitebot will store a new document or index to the distributed data store, update any existing documents or indexes as required, and update the value in the DHT pointing to any indexes which have changed.  Stored documents will additionally be symmetrically encrypted with a portion of the site's key before storage in the DDS.

User-generated data, such as forum posts, blog comments, etc may be addressed to the sitebot, but will not be included in the site unless signed by a moderator.  On discovering such a submission, the sitebot may add such content to a moderation queue (an index), or message site moderators through other means.

Since the bot deals only in BASE64 messages encrypted with its own public key, it may receive messages by any communications channel or combination thereof - one built into the data store, Kademelia, Tor services, Freenet NTTP, email, instant messenger, IRC, hushmail, redphone, etc.

    Viewing documents on a Kindred site

In order to browse a kindred site, a user opens a minimal javascript application kept a web page stored on their computer or mobile device, or loaded from an internet site.  Into this application they may open a bookmark (HTML5 offline storage) or copy-paste a site identifier.  Either way, the javascript application will have a key to look up in the DHT.

The javascript application will then make a CORS request to a RESTful API, running either on the user's local machine as a browser plugin or HTTPS server (for improved security and anonymity), or via HTTPS (for portability and ease of adoption).  This request will return the latest pointer to the site index, a file in the DDS which may be downloaded by the client application through the same RESTful API.

This index is then decrypted using a portion of the site's identifier.  Site compnents such as CSS files, images and documents are then downloaded from the DDS, decrypted and displayed.

    Indexes

Indexes used to arrange a Kindred site are JSON formatted arrays of pointers to other documents in the DDS.  They take the form of series of plain text/json documents, linked to one another chronologically (future versions may allow a skip-list format).  The HEAD of the index has a constant key in the DHT, and the value will be updated periodically to point to new blocks in the DDS.

    HEAD
      [
        Link to document z > DDS identifier,
        Link to document y > DDS identifier,
        Link to document x > DDS identifier
        ...n...
        Link to next segment of index > DDS identifier
      ]

    NEXT SEGMENT (previos HEAD) 

      [
        Link to document w > DDS identifier
        Link to next segment of index > DDS identifier
      ]

    ... ETC ...

The index is thus updated and read chronologically, with new entries superceding older ones. Entries in the index may point to documents, revoke documents, or replace documents with a new version.  In addition, index segments refer to other index segments which are read sequentially, latest values first.  For efficiency of the client, important information may be brought to the head of the index periodically, repetitions are allowed.

    Documents

Documents are composed and displayed by the client as one of a small ontology of JSON formats, to be described in more detail.  The basic disposition is:

  {
    "Type": (
        "kindred/sitemap" | "kindred/index" | "kindred/template" | 
        "kindred/profile" | "kindred/post" | "kindred/file" 
    ),
    "Site": "[Identifier of the site this belongs to]"
    "Index": "[Index this belongs to]"
    "Template": "Name of a site template"
    "Title": [
        'en-US': 'Title of this item in english',
        'fr': 'Title of this item in French'
    ],

    "Text": [
        'en-US': 'Body of this item in English',
        'fr': 'Body of this item in French'
    ],
    "Attachments": [
        { ...meta, link to DDS... },
        { ...meta, link to DDS.. }
    ],
    "creator": [
        "pubkey": "... creator public key ...",
        "profile": "link to profile document in DDS",
        "signature": "of this document"
    ]
        
    ... for index types only ...

    "items": [
        { ... link to document, meta for search and display ... },    
        { ... link to document, meta for search and display ... },    
    ],
    "next": [ ... next section of index ... ],
    "options": [
        ... set of descriptors describing what types additions to the index will be accepted
        by sitebot ..., eg, 'allowcomment' (to note that commants allowed), 
        'allowfile' (files may be attached), 'searchbox', etc.
    ]

  }

All documents are inderectly addressed by a static identifer the DHT, which links to an index of versions of the document, which will link to the document blocks themselves.  Signatures of documents are kept in the index, rather than on the document itself, exccept for the signature of the original author of the document.  In this way the same document may be indexed by multiple sites, but revoked by the original author, so that ownership is maintained.  Copies of a document may be made by anyone, for archival purposes, but are not guaranteed to originate from the claimed author.

Attachments to a document may be comments, images, video, audio, etc.  Signatures of documents are calculated and verfified by replacing the signature field with a fixed length of 'xxxxxx' before computation, and checking the signed document against the user's public key.

    Layout of kindred sites

A kindred site is a document which specifies layout information for site pages (CSS, an optional background image, site banner image, menus, header, footer and sidebar text.  Pages in Kindred re not free-form HTML, but markdown plaed in pre-defined templates customizable by CSS.

    {
        "type": "kindred/template",
        "title": [ "en": "My Site" ],
        "css": "...literal CSS here...",
        "background": [
            "en": { ... DDS link and meta ... }
        ],
        "banner": [
            "en": "site banner image",
            "de": "sitebild"
        ],
        "menu": [ ... array of links ... ],
        "submenu": [ ... array of links ... ],
        "sidebar": [
            "en": 'content of sidebar (markdown)'
        ],
        "footer": [
            "en": 'content of footer (markdown)'
        ]
    }

Note sites may have multiple templates, the template in use will be determined by the index linking to a document.

    Identity on kindred sites

All documents, including comments and attached files must be signed by an identity - users may use a new identity each time, or persist a pseudonmyous identity across one or more Kindred sites.  This is achieved by adding a profile field to the document's 'author' section.  This profile is maintained by the author, displayed with their own template, and may list other sites or profiles, enabling basic pseudonmyous social networking functionality.  Each profile document must specify a template used to display it.

{
    "type": "kindred/profile",
    "title": "Pseudonym",
    "pubkeys" [ ... set of public keys / identities and signatures to prove ownership ... ],
    "template": "... link to template document index in DHT ... ",
    "text": {
        "en": "Profile text in user language...."
    },
    "linkcats": [
        ... set of user defined relationship categories ....
    ]
    "links": [
        { ... links to documents on other sites, including other profiles ... }
    ],
    "options": {
        ...  allow comments for behavior like facebook wall ...
    }
}

A profile may exist as the only page on a site, or be published as part of a lerger site, say for mod profiles with identities used for a particular role on a site.

    Participation

In addition to submitting content, identities recognised by the sitebot may be permitted to submit votes for or against content, or to file abuse reports.  This basic feature may allow sone degree of user moderation and features such as polling and moderator elections in a pseudonymous environment.

    Messages to site bots

Messages to sitebots are simple JSON documents encrypted with the bot's public key:

  {
    "type": "kindred/botmessage",
    "action": "requestinclusion",
    "site": "... site id ...",
    "index": "site index",
    "document": "... DHT link to document index ...",
    "version": "... version / revision number in document index ..."
  }

Documents are included at the requested version, subsequent revisions of the document (eg, comments) must be resubmitted with "requestupdate" action, which the site bot *may* be configured to automatically approve.  This is to prevent comments from turning to spam after inclusion.

It is hoped that with this simple but general functionality, all major site types (blogs, imageboards, forums, social networks, etc may be reproduced).

Example:  The Pirate Bay mirror

In this configuration all torrent pages on TPB are scraped to documents on a Kindred site, in a single index.  Each document is tagged with keywords and tags from TPB site.  Users must necessarily download the entire index of pages (~10MB), but not all pages themselves.  This index may then be searched, and result pages downloaded on request, along with comments, images, etc.
