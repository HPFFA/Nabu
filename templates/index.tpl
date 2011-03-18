{include file="documentHeader"}
<head>
	<title>{lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
	<link rel="alternate" type="application/rss+xml" href="index.php?page=StoriesFeed&amp;format=rss2" title="{lang}sls.index.feed{/lang} (RSS2)" />
	<link rel="alternate" type="application/atom+xml" href="index.php?page=StoriesFeed&amp;format=atom" title="{lang}sls.index.feed{/lang} (Atom)" />
</head>
<body>
{include file='header' sandbox=false}

<div id="main">
		{include file="libraryList"}
	
    </div>

{include file='footer' sandbox=false}

</body>
</html>