<?

//--------------------------------------------------------------------------------------------------
//*	object to implement / express RSS feeds (alternative to page object for actions)
//--------------------------------------------------------------------------------------------------
//+	Add items from most to least recent using the add() method.

class KRSS {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $title = '';			//_	RSS channel title [string]
	var $description = '';		//_	RSS channel description [string]
	var $link = '';				//_	RSS channel attrib link [string]
	var $items;					//_	items in feed [array]
	var $updated = 0;			//_	timestamp representing the most recent update [int]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: title - RSS channel title [string]
	//opt: description - RSS channel description [string]
	//opt: link - channel source link [string]

	function KRSS($title = '', $description = '', $link = '') {
		global $kapenta;

		$this->updated = '';
		$this->items = array();

		if ('' == $title) { $title = $kapenta->websiteName . ' (rss)'; }
		if ('' == $description) { $description = $kapenta->websiteName . 'Site Feed'; }
		if ('' == $link) { $link = $kapenta->serverPath; }

		$this->title = $title;
		$this->description = $description;
		$this->link = $link;
	}

	//----------------------------------------------------------------------------------------------
	//.	add an item to the feed
	//----------------------------------------------------------------------------------------------
	//arg: guid - globally unique string identifying this content item [string]
	//arg: title - name of content item [string]
	//arg: author - name of user whocreated this item [string]
	//arg: content - text/html [string]
	//arg: link - canonical link to this item on this site [string]
	//arg: updated - editedOn field of content item [string]

	function add($title, $author, $content, $link, $updated) {
		global $kapenta;
		global $theme;

		$content = str_replace('%%serverPath%%', $kapenta->serverPath, $content);

		$search = array('<br/>', '<br>', '</p>');
		$replace = array("<br/>\n", "<br>\n", "</p>\n\n");
		$description = strip_tags(str_replace($search, $replace, $content));

		$summary = $theme->makeSummary($description, 1000);
		$summary = str_replace("\n", ' ', $summary);
		$summary = str_replace("\r", ' ', $summary);
		$summary = str_replace("&nbsp;", ' ', $summary);
		$summary = str_replace("&apos;", "'", $summary);

		$updateTime = $kapenta->strtotime($updated);

		$item = array(
			'guid' => $link,
			'title' => $title,
			'author' => $author,
			'content' => $content,
			'description' => $description,
			'summary' => $summary,
			'link' => $link,
			'pubDate' => $this->rssDate($updateTime),
			'timestamp' => $kapenta->strtotime($updated)
		);

		if ($updateTime > $this->updated) { $this->updated = $updateTime; }

		$this->items[] = $item;
	}

	//----------------------------------------------------------------------------------------------
	//.	convert a timestamp to an RSS 2.0 date
	//----------------------------------------------------------------------------------------------
	//arg: time - timestamp (UTC) [int]
	//returns: date [string]

	function rssDate($time) {
		return date("D, d M Y H:i:s +0000", $time);	
	}

	//----------------------------------------------------------------------------------------------
	//.	convert a timestamp to an ATOM date
	//----------------------------------------------------------------------------------------------

	function atomDate($time) {
		return date("Y-m-d", $time) . 'T' . date("H:i:s", $time) . 'Z';	
	}

	//----------------------------------------------------------------------------------------------
	//.	render as RSS 2.0 feed
	//----------------------------------------------------------------------------------------------

	function toRSS2() {

		$xml = ''
		 . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
		 . "<rss version=\"2.0\"\n"
		 . "\txmlns:content=\"http://purl.org/rss/1.0/modules/content/\"\n"
		 . "\txmlns:wfw=\"http://wellformedweb.org/CommentAPI/\"\n"
		 . "\txmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n"
		 . "\txmlns:atom=\"http://www.w3.org/2005/Atom\"\n"
		 . "\txmlns:sy=\"http://purl.org/rss/1.0/modules/syndication/\"\n"
		 . "\txmlns:slash=\"http://purl.org/rss/1.0/modules/slash/\"\n"
		 . ">\n"
		 . "\t<channel>\n"
		 . "\t<title>" . $this->title . "</title>\n"
		 . "\t<link>" . $this->link . "</link>\n"
		 . "\t<description>" . $this->description . "</description>\n";

		foreach($this->items as $item) {
			$xml .= ''
			 . "\t<item>\n"
			 . "\t\t<title>" . $item['title'] . "</title>\n"
			 . "\t\t<link>" . $item['link'] . "</link>\n"
			 . "\t\t<pubDate>" . $item['pubDate'] . "</pubDate>\n"
			 . "\t\t<dc:creator>" . $item['author'] . "</dc:creator>\n"
			 . "\t\t<guid isPermaLink=\"true\">" . $item['link'] . "</guid>\n"
			 . "\t\t<description><![CDATA[" . $item['summary'] . "]]></description>\n"
			 . "\t\t<content:encoded><![CDATA[" . $item['content'] . "]]></content:encoded>\n"
			 . "\t</item>\n";
		}

		$xml .= "</channel>\n";
		$xml .= "</rss>\n";

		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	render as Atom Feed
	//----------------------------------------------------------------------------------------------

	function toAtom() {

		$xml = ''
		 . "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
		 . "<feed xmlns=\"http://www.w3.org/2005/Atom\">\n"
		 . "\t<title>" . $this->title . "</title>\n"
		 . "\t<link href=\"" . $this->link . "\"/>\n"
		 . "\t<updated>" . $this->atomDate($this->updated) . "</updated>\n"
		 . "\t<id>" . $this->link . "</id>\n";

		foreach($this->items as $item) {

			$item['content'] = str_replace('<', '&lt;', $item['content']);
			$item['content'] = str_replace('&nbsp;', ' ', $item['content']);
			$item['content'] = str_replace('&apos;', "'", $item['content']);

			$xml .= ''
			 . "\t<entry>\n"
			 . "\t\t<title>" . $item['title'] . "</title>\n"
			 . "\t\t<link rel=\"self\" href=\"" . $item['link'] . "\"/>\n"
			 . "\t\t<author><name>" . $item['author'] . "</name></author>\n"
			 . "\t\t<id>" . $item['link'] . "</id>\n"
			 . "\t\t<updated>" . $this->atomDate($item['timestamp']) . "</updated>\n"
			 . "\t\t<summary>" . $item['summary'] . ".</summary>\n"
			 . "\t\t<content type=\"html\"><div>" . $item['content'] . "</div></content>\n"
			 . "\t</entry>\n";
		}

		$xml .= "</feed>\n";

		return $xml;
	}

}

?>
