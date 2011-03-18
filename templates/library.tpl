{include file="documentHeader"}
<head>
	<title>{lang}{$library->title}{/lang} {if $pageNo > 1}- {lang}wcf.page.pageNo{/lang} {/if}- {lang}{PAGE_TITLE}{/lang}</title>

	{include file='headInclude' sandbox=false}

	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<link rel="alternate" type="application/rss+xml" href="index.php?page=StoriesFeed&amp;format=rss2&amp;libraryID={@$libraryID}" title="{lang}sls.library.feed{/lang} (RSS2)" />
	<link rel="alternate" type="application/atom+xml" href="index.php?page=StoriesFeed&amp;format=atom&amp;libraryID={@$libraryID}" title="{lang}sls.library.feed{/lang} (Atom)" />
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{* --- quick search controls --- *}
{assign var='searchFieldTitle' value='{lang}sls.library.search.query{/lang}'}
{capture assign=searchHiddenFields}
	<input type="hidden" name="libraryIDs[]" value="{@$libraryID}" />
	<input type="hidden" name="types[]" value="chapter" />
{/capture}
{* --- end --- *}
{include file='header' sandbox=false}

<div id="main">

	{include file="navigation"}

	<div class="mainHeadline">
		<img src="{icon}{@$library->getIconName()}L.png{/icon}" alt="" {if $library->isLibrary()}ondblclick="document.location.href=fixURL('index.php?action=LibraryMarkAsRead&amp;libraryID={@$libraryID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}')" title="{lang}sls.library.markAsReadByDoubleClick{/lang}" {/if}/>
		<div class="headlineContainer">
			<h2><a href="index.php?page=Library&amp;libraryID={@$libraryID}{@SID_ARG_2ND}">{lang}{$library->title}{/lang}</a></h2>
			<p>{lang}{if $library->allowDescriptionHtml}{@$library->description}{else}{$library->description}{/if}{/lang}</p>
		</div>
	</div>

	{if $userMessages|isset}{@$userMessages}{/if}

	{include file="libraryList"}

	{if $library->isLibrary()}
		<div class="contentHeader">
			{assign var=multiplePagesLink value="index.php?page=Library&libraryID=$libraryID&pageNo=%d"}
			{if $sortField != $defaultSortField}{assign var=multiplePagesLink value=$multiplePagesLink|concat:'&sortField=':$sortField}{/if}
			{if $sortOrder != $defaultSortOrder}{assign var=multiplePagesLink value=$multiplePagesLink|concat:'&sortOrder=':$sortOrder}{/if}
			{if $daysPrune != $defaultDaysPrune}{assign var=multiplePagesLink value=$multiplePagesLink|concat:'&daysPrune=':$daysPrune}{/if}
			{if $status}{assign var=multiplePagesLink value=$multiplePagesLink|concat:'&status=':$status}{/if}
			{if $languageID}{assign var=multiplePagesLink value=$multiplePagesLink|concat:'&languageID=':$languageID}{/if}
			{if $tagID}{assign var=multiplePagesLink value=$multiplePagesLink|concat:'&tagID=':$tagID}{/if}
			{pages print=true assign=pagesOutput link=$multiplePagesLink|concat:SID_ARG_2ND_NOT_ENCODED}
			{if $library->canStartStory() || $additionalLargeButtons|isset}
				<div class="largeButtons">
					<ul>
						{if $library->canStartStory()}<li><a href="index.php?form=StoryAdd&amp;libraryID={@$libraryID}{@SID_ARG_2ND}" title="{lang}sls.library.button.newStory{/lang}"><img src="{icon}storyNewM.png{/icon}" alt="" /> <span>{lang}sls.library.button.newStory{/lang}</span></a></li>{/if}
						{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
					</ul>
				</div>
			{/if}
		</div>

		{if $permissions.canHandleStory || $permissions.canHandleChapter}
			<script type="text/javascript">
				//<![CDATA[
				var language = new Object();
				var chapterData = new Hash();
				var url = 'index.php?page=Library&libraryID={@$libraryID}&pageNo={@$pageNo}&sortField={@$sortField}&sortOrder={@$sortOrder}&daysPrune={@$daysPrune}&status={@$status}&languageID={@$languageID}{@SID_ARG_2ND_NOT_ENCODED}';
				//]]>
			</script>
			{include file='storyInlineEdit' pageType=library}
		{/if}

		{if $normalStories|count == 0}
			<div class="border content">
				<div class="container-1">
					<p>{lang}sls.library.noStories{/lang}</p>
				</div>
			</div>
		{else}
			<script type="text/javascript" src="{@RELATIVE_SLS_DIR}js/StoryMarkAsRead.class.js"></script>
			
			{if $normalStories|count > 0}
				{include file="libraryStories" title="{lang}sls.library.stories.normal{/lang}" stories=$normalStories listName=normalStoriesStatus listStatus=$normalStoriesStatus listHasNewStories=$newNormalStories}
			{/if}
		{/if}

		<div class="contentFooter">
			{@$pagesOutput}

			<div id="storyEditMarked" class="optionButtons"></div>
			<div id="chapterEditMarked" class="optionButtons"></div>

			{if $library->canStartStory() || $additionalLargeButtons|isset}
				<div class="largeButtons">
					<ul>
						{if $library->canStartStory()}<li><a href="index.php?form=StoryAdd&amp;libraryID={@$libraryID}{@SID_ARG_2ND}" title="{lang}sls.library.button.newStory{/lang}"><img src="{icon}storyNewM.png{/icon}" alt="" /> <span>{lang}sls.library.button.newStory{/lang}</span></a></li>{/if}
						{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
					</ul>
				</div>
			{/if}
		</div>
	{/if}

	{if $library->isLibrary() || $usersOnlineTotal|isset || $libraryModerators|count || $additionalBoxes|isset || $tags|count}
		{cycle values='container-1,container-2' print=false advance=false}
		<div class="border infoBox">
			{if $library->isLibrary()}
				<div class="{cycle}">
					<div class="containerIcon"><img src="{icon}sortM.png{/icon}" alt="" /> </div>
					<div class="containerContent">
						<h3>{lang}sls.library.sorting{/lang}</h3>
						<form method="get" action="index.php">
							<div class="storySort">
								<input type="hidden" name="page" value="Library" />
								<input type="hidden" name="libraryID" value="{@$libraryID}" />
								<input type="hidden" name="pageNo" value="{@$pageNo}" />
								<input type="hidden" name="tagID" value="{@$tagID}" />

								<div class="floatedElement">
									<label for="sortField">{lang}sls.library.sortBy{/lang}</label>
									<select name="sortField" id="sortField">
										<option value="topic"{if $sortField == 'topic'} selected="selected"{/if}>{lang}sls.library.sortBy.topic{/lang}</option>
										<option value="username"{if $sortField == 'username'} selected="selected"{/if}>{lang}sls.library.sortBy.starter{/lang}</option>
										<option value="time"{if $sortField == 'time'} selected="selected"{/if}>{lang}sls.library.sortBy.startTime{/lang}</option>
										{if $enableRating}<option value="ratingResult"{if $sortField == 'ratingResult'} selected="selected"{/if}>{lang}sls.library.sortBy.rating{/lang}</option>{/if}
										<option value="replies"{if $sortField == 'replies'} selected="selected"{/if}>{lang}sls.library.sortBy.replies{/lang}</option>
										<option value="views"{if $sortField == 'views'} selected="selected"{/if}>{lang}sls.library.sortBy.views{/lang}</option>
										<option value="lastChapterTime"{if $sortField == 'lastChapterTime'} selected="selected"{/if}>{lang}sls.library.sortBy.lastChapterTime{/lang}</option>
									</select>
									<select name="sortOrder">
										<option value="ASC"{if $sortOrder == 'ASC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
										<option value="DESC"{if $sortOrder == 'DESC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
									</select>
								</div>

								<div class="floatedElement">
									<label for="filterDate">{lang}sls.library.filterByDate{/lang}</label>
									<select name="daysPrune" id="filterDate">
										<option value="1"{if $daysPrune == 1} selected="selected"{/if}>{lang}sls.library.filterByDate.1{/lang}</option>
										<option value="3"{if $daysPrune == 3} selected="selected"{/if}>{lang}sls.library.filterByDate.3{/lang}</option>
										<option value="7"{if $daysPrune == 7} selected="selected"{/if}>{lang}sls.library.filterByDate.7{/lang}</option>
										<option value="14"{if $daysPrune == 14} selected="selected"{/if}>{lang}sls.library.filterByDate.14{/lang}</option>
										<option value="30"{if $daysPrune == 30} selected="selected"{/if}>{lang}sls.library.filterByDate.30{/lang}</option>
										<option value="60"{if $daysPrune == 60} selected="selected"{/if}>{lang}sls.library.filterByDate.60{/lang}</option>
										<option value="100"{if $daysPrune == 100} selected="selected"{/if}>{lang}sls.library.filterByDate.100{/lang}</option>
										<option value="365"{if $daysPrune == 365} selected="selected"{/if}>{lang}sls.library.filterByDate.365{/lang}</option>
										<option value="1000"{if $daysPrune == 1000} selected="selected"{/if}>{lang}sls.library.filterByDate.1000{/lang}</option>
									</select>
								</div>

								<div class="floatedElement">
									<label for="filterByStatus">{lang}sls.library.filterByStatus{/lang}</label>
									<select name="status" id="filterByStatus">
										<option value=""></option>
										{if $this->user->userID}
											<option value="read"{if $status == 'read'} selected="selected"{/if}>{lang}sls.library.filterByStatus.read{/lang}</option>
											<option value="unread"{if $status == 'unread'} selected="selected"{/if}>{lang}sls.library.filterByStatus.unread{/lang}</option>
										{/if}
										{if MODULE_STORY_MARKING_AS_DONE && $library->enableMarkingAsDone}
											<option value="done"{if $status == 'done'} selected="selected"{/if}>{lang}sls.library.filterByStatus.done{/lang}</option>
											<option value="undone"{if $status == 'undone'} selected="selected"{/if}>{lang}sls.library.filterByStatus.undone{/lang}</option>
										{/if}
										<option value="closed"{if $status == 'closed'} selected="selected"{/if}>{lang}sls.library.filterByStatus.closed{/lang}</option>
										<option value="open"{if $status == 'open'} selected="selected"{/if}>{lang}sls.library.filterByStatus.open{/lang}</option>
										{if $library->getModeratorPermission('canDeleteStoryCompletely')}<option value="deleted"{if $status == 'deleted'} selected="selected"{/if}>{lang}sls.library.filterByStatus.deleted{/lang}</option>{/if}
										{if $library->getModeratorPermission('canEnableStory')}<option value="hidden"{if $status == 'hidden'} selected="selected"{/if}>{lang}sls.library.filterByStatus.hidden{/lang}</option>{/if}
									</select>
								</div>

								{if $contentLanguages|count > 1}
									<div class="floatedElement">
										<label for="filterByLanguage">{lang}sls.library.filterByLanguage{/lang}</label>
										<select name="languageID" id="filterByLanguage">
											<option value="0"></option>
											{htmlOptions options=$contentLanguages selected=$languageID disableEncoding=true}
										</select>
									</div>
								{/if}

								<div class="floatedElement">
									<input type="image" class="inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
								</div>

								{@SID_INPUT_TAG}
							</div>
						</form>
					</div>
				</div>
			{/if}

			{if $libraryModerators|count}
				<div class="{cycle}">
					<div class="containerIcon"><img src="{icon}moderatorM.png{/icon}" alt="" /></div>
					<div class="containerContent">
						<h3>{lang}sls.library.moderators{/lang}</h3>
						<p class="smallFont">{implode from=$libraryModerators item=moderator}{if $moderator->userID}<a href="index.php?page=User&amp;userID={@$moderator->userID}{@SID_ARG_2ND}">{$moderator}</a>{else}{$moderator}{/if}{/implode}</p>
					</div>
				</div>
			{/if}

			{if $usersOnlineTotal|isset}
				<div class="{cycle}">
					<div class="containerIcon"><img src="{icon}membersM.png{/icon}" alt="" /></div>
					<div class="containerContent">
						<h3>{if $this->user->getPermission('user.usersOnline.canView')}<a href="index.php?page=UsersOnline&amp;libraryID={@$libraryID}{@SID_ARG_2ND}">{lang}sls.library.usersOnline{/lang}</a>{else}{lang}sls.library.usersOnline{/lang}{/if}</h3>
						<p class="smallFont">{lang}sls.index.usersOnline.detail{/lang}</p>
						{if $usersOnline|count}
							<p class="smallFont">{implode from=$usersOnline item=userOnline}<a href="index.php?page=User&amp;userID={@$userOnline.userID}{@SID_ARG_2ND}">{@$userOnline.username}</a>{/implode}</p>
							{if INDEX_ENABLE_USERS_ONLINE_LEGEND && $usersOnlineMarkings|count}
								<p class="smallFont">
								{lang}wcf.usersOnline.marking.legend{/lang} {implode from=$usersOnlineMarkings item=usersOnlineMarking}{@$usersOnlineMarking}{/implode}
								</p>
							{/if}
						{/if}
					</div>
				</div>
			{/if}

			{if $library->isLibrary() && LIBRARY_ENABLE_STATS}
				<div class="{cycle}">
					<div class="containerIcon"><img src="{icon}statisticsM.png{/icon}" alt="" /></div>
					<div class="containerContent">
						<h3>{lang}sls.index.stats{/lang}</h3>
						<p class="smallFont">{lang}sls.library.stats.detail{/lang}</p>
					</div>
				</div>
			{/if}

			{if $tags|count}
				<div class="{cycle}">
					<div class="containerIcon"><img src="{icon}tagM.png{/icon}" alt="" /></div>
					<div class="containerContent">
						<h3>{lang}wcf.tagging.filter{/lang}</h3>
						<ul class="tagCloud">
							{foreach from=$tags item=tag}
								<li><a href="index.php?page=Library&amp;libraryID={@$library->libraryID}&amp;pageNo={@$pageNo}&amp;sortField={@$sortField}&amp;sortOrder={@$sortOrder}&amp;daysPrune={@$daysPrune}&amp;status={@$status}&amp;&amp;languageID={@$languageID}&amp;tagID={@$tag->getID()}{@SID_ARG_2ND}" style="font-size: {@$tag->getSize()}%">{$tag->getName()}</a></li>
							{/foreach}
						</ul>
					</div>
				</div>
			{/if}

			{if $additionalBoxes|isset}{@$additionalBoxes}{/if}
		</div>
	{/if}

	<div class="pageOptions">
		{if $library->isLibrary()}

			{if $additionalPageOptions|isset}{@$additionalPageOptions}{/if}
			{if $this->user->userID}
				{if !$this->user->isLibrarySubscription($libraryID)}<a href="index.php?action=LibrarySubscribe&amp;libraryID={@$libraryID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}"><img src="{icon}subscribeS.png{/icon}" alt="" /> <span>{lang}sls.library.subscribe{/lang}</span></a>
				{else}<a href="index.php?action=LibraryUnsubscribe&amp;libraryID={@$libraryID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}"><img src="{icon}unsubscribeS.png{/icon}" alt="" /> <span>{lang}sls.library.unsubscribe{/lang}</span></a>
				{/if}
			{/if}
			<a href="index.php?action=LibraryMarkAsRead&amp;libraryID={@$libraryID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}"><img src="{icon}libraryMarkAsReadS.png{/icon}" alt="" /> <span>{lang}sls.library.markAsRead{/lang}</span></a>
		{/if}
	</div>

	{include file='libraryQuickJump'}
</div>

{include file='footer' sandbox=false}
</body>
</html>