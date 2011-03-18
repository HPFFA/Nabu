<ul class="breadCrumbs">
	{if !$hideRoot|isset}
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
	{/if}

	{foreach from=$library->getParentLibraries() item=parentLibrary}
		<li><a href="index.php?page=Library&amp;libraryID={@$parentLibrary->libraryID}{@SID_ARG_2ND}"><img src="{icon}{@$parentLibrary->getIconName()}S.png{/icon}" alt="" /> <span>{lang}{$parentLibrary->title}{/lang}</span></a> &raquo;</li>
	{/foreach}

        {if   $showLibrary|isset || $showStory|isset || $showChapter|isset}
		<li><a href="index.php?page=Library&amp;libraryID={@$library->libraryID}{@SID_ARG_2ND}"><img src="{icon}{@$library->getIconName()}S.png{/icon}" alt="" /> <span>{lang}{$library->title}{/lang}</span></a> &raquo;</li>
	{/if}
        {if   $showStory|isset || $showChapter|isset}
		<li><a href="index.php?page=Story&amp;storyID={@$story->storyID}{@SID_ARG_2ND}"><img src="{icon}storyS.png{/icon}" alt="" /> <span>{$story->title}</span></a> &raquo;</li>
	{/if}
        {if   $showStory|isset || $showChapter|isset}
		<li><a href="index.php?page=Chapter&amp;chapterID={@$chapter->chapterID}{@SID_ARG_2ND}"><img src="{icon}chapterS.png{/icon}" alt="" /> <span>{$chapter->title}</span></a> &raquo;</li>
	{/if}
</ul>