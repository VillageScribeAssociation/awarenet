<? /*
<page>
	<template>window.template.php</template>
	<title>search tags</title>
	<content>
		<script language='Javascript'>
			//--------------------------------------------------------------------------------------
			//	perform a live search
			//--------------------------------------------------------------------------------------

			function Tags_Search() {
				var txtSearch = document.getElementById('txtSearch');
				var q = txtSearch.value;

				if ('' == q) {
					klive.bindDivToBlock('divSearchResults', '[' + '[:tags::searchcloud::num=100:]]');
					return;
				}

				if (q.length < 2) { return; }

				var block = ''
				 + '[' + '[:tags::searchinsert'
				 + '::q64=' + kutils.base64_encode(q)
				 + '::hta=%%hta%%'
				 + ':]]';

				klive.removeBlock(block);
				klive.bindDivToBlock('divSearchResults', block);
			}

			//--------------------------------------------------------------------------------------
			//	fill the search box
			//--------------------------------------------------------------------------------------

			function Tags_Set(tagName) {
				var txtSearch = document.getElementById('txtSearch');
				txtSearch.value = tagName;
				Tags_Search();
			}

			//--------------------------------------------------------------------------------------
			//	KHTA stub to relay selections to parent document
			//--------------------------------------------------------------------------------------

			function KHyperTextAreaStub() {
				this.inject = function(htaName, block) {
					if ((window.parent) && (window.parent.khta)) {
						window.parent.khta.inject(htaName, block);
						kwnd.closeWindow();
					}
				}
			}

			khta = new KHyperTextAreaStub();

			//--------------------------------------------------------------------------------------
			//	resize result div to match window size
			//--------------------------------------------------------------------------------------

			kwnd.onResize = function() {
				$('#divSearchResults').height($(window).height() - $('#divSearchBox').height() - 5);
			}

		</script>
	
		<div id='divSearchBox'>
		<!-- [[:theme::navtitlebox::label=Find media by tag:]] -->
		<input type='text' name='q' id='txtSearch' style='width: 100%;'>
		</div>

		<script language='Javascript'>
			$('#txtSearch').keyup( function() { Tags_Search(); } );
			$('#txtSearch').focus();
		</script>

		<div id='divSearchResults' style='width: 100%; overflow: scroll;'>
		[[:tags::searchcloud::num=100:]]
		</div>
	</content>
	<script>
	</script>
	<nav1></nav1>
	<nav2></nav2>
	<banner></banner>
	<head></head>
	<menu1></menu1>
	<menu2></menu2>
	<section></section>
	<subsection></subsection>
</page>
*/ ?>
