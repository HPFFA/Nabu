{if $libraries|count > 0}
	<script type="text/javascript" src="{@RELATIVE_SLS_DIR}js/LibraryMarkAsRead.class.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		var libraries = new Hash();
		document.observe("dom:loaded", function() {
			new LibraryMarkAsRead(libraries);
		});
	//]]>
	</script>

	{cycle name='librarylistCycle' values='1,2' advance=false print=false}
	<ul id="librarylist">
		{foreach from=$libraries item=child}
			{* define *}
			{assign var="depth" value=$child.depth}
			{assign var="open" value=$child.open}
			{assign var="hasChildren" value=$child.hasChildren}
			{assign var="openParents" value=$child.openParents}
			{assign var="library" value=$child.library}
			{assign var="libraryID" value=$library->libraryID}
			{counter assign=libraryNo print=false}
			{if $library->isLibrary()}
				{* library *}

				<li{if $depth == 1} class="library border"{/if}>
					<div class="librarylistInner container-{cycle name='librarylistCycle'} library{@$libraryID}"{if $library->imageShowAsBackground}{if $library->image || $newChapters.$libraryID && $library->imageNew} style="background-image: url({if $newChapters.$libraryID && $library->imageNew}{$library->imageNew}{else}{$library->image}{/if}); background-repeat: {$library->imageBackgroundRepeat}"{/if}{/if}>
						<div class="librarylistTitle{if LIBRARY_LIST_ENABLE_LAST_CHAPTER && LIBRARY_LIST_ENABLE_STATS} librarylistCols-3{else}{if LIBRARY_LIST_ENABLE_LAST_CHAPTER || LIBRARY_LIST_ENABLE_STATS} librarylistCols-2{/if}{/if}">
							<div class="containerIcon">
								<img id="libraryIcon{@$libraryNo}" src="{if $newChapters.$libraryID && $library->imageNew && !$library->imageShowAsBackground}{$library->imageNew}{elseif $library->image && !$library->imageShowAsBackground}{$library->image}{else}{icon}{@$library->getIconName()}{if $newChapters.$libraryID}New{/if}M.png{/icon}{/if}" alt="" {if $newChapters.$libraryID}title="{lang}sls.library.markAsReadByDoubleClick{/lang}" {/if}/>
							</div>

							<div class="containerContent">
								{if $depth > 3}<h6 class="libraryTitle">{else}<h{@$depth+2} class="libraryTitle">{/if}
									<a id="libraryLink{@$libraryNo}" {if $newChapters.$libraryID}class="new" {/if}href="index.php?page=Library&amp;libraryID={@$libraryID}{@SID_ARG_2ND}">{lang}{$library->title}{/lang}{if $unreadStoriesCount.$libraryID|isset}<span>&nbsp;({#$unreadStoriesCount.$libraryID})</span>{/if}</a>
								{if $depth > 3}</h6>{else}</h{@$depth+2}>{/if}
								{if $newChapters.$libraryID}
									<script type="text/javascript">
										//<![CDATA[
										libraries.set({@$libraryNo}, {
											'libraryNo': {@$libraryNo},
											'libraryID': {@$libraryID},
											'icon': '{if $library->image && !$library->imageShowAsBackground}{$library->image}{else}{icon}{@$library->getIconName()}M.png{/icon}{/if}'
										});
										//]]>
									</script>
								{/if}

								{if $library->description}
									<p class="librarylistDescription">
										{lang}{if $library->allowDescriptionHtml}{@$library->description}{else}{$library->description}{/if}{/lang}
									</p>
								{/if}

								{if $subLibraries.$libraryID|isset}
									<div class="librarylistSublibraries">
										<ul>{foreach name='subLibraries' from=$subLibraries.$libraryID item=subLibrary}{assign var="subLibraryID" value=$subLibrary->libraryID}{counter assign=libraryNo print=false}<li{if $tpl.foreach.subLibraries.last} class="last"{/if}>{if $depth > 4}<h6>{else}<h{@$depth+3}>{/if}<img id="libraryIcon{@$libraryNo}" src="{icon}{if $subLibrary->isLibrary()}library{if $newChapters.$subLibraryID}New{/if}{elseif $subLibrary->isCategory()}category{else}libraryRedirect{/if}S.png{/icon}" alt="" {if $subLibrary->isLibrary() && $newChapters.$subLibraryID}title="{lang}sls.library.markAsReadByDoubleClick{/lang}" {/if}/>{*
														*}&nbsp;<a id="libraryLink{@$libraryNo}" {if $newChapters.$subLibraryID}class="new" {/if}{if $subLibrary->isExternalLink()}class="externalURL" {/if}href="index.php?page=Library&amp;libraryID={@$subLibraryID}{@SID_ARG_2ND}">{lang}{$subLibrary->title}{/lang}{if $unreadStoriesCount.$subLibraryID|isset} <span>({#$unreadStoriesCount.$subLibraryID})</span>{/if}</a>{if $depth > 4}</h6>{else}</h{@$depth+3}>{/if}{*
													*}{if $newChapters.$subLibraryID}<script type="text/javascript">
														//<![CDATA[
														libraries.set({@$libraryNo}, {
															'libraryNo': {@$libraryNo},
															'libraryID': {@$subLibraryID},
															'icon': '{icon}{@$subLibrary->getIconName()}S.png{/icon}'
														});
														//]]>
													</script>{/if}</li>{/foreach}</ul>
									</div>
								{/if}

								{if $libraryUsersOnline.$libraryID.users|isset || $libraryUsersOnline.$libraryID.guests|isset}
									<p class="librarylistUsersOnline">
										<img src="{icon}usersS.png{/icon}" alt="" />
										{if $libraryUsersOnline.$libraryID.users|isset}
											{implode from=$libraryUsersOnline.$libraryID.users item=userOnline}<a href="index.php?page=User&amp;userID={@$userOnline.userID}{@SID_ARG_2ND}">{@$userOnline.username}</a>{/implode}
										{/if}
										{if $libraryUsersOnline.$libraryID.guests|isset}
											{lang}sls.index.libraryUsersOnline.guests{/lang}
										{/if}
									</p>
								{/if}

								{if $moderators.$libraryID|isset}
									<p class="moderators">
										<img src="{icon}moderatorS.png{/icon}" alt="" />
										{implode from=$moderators.$libraryID item=moderator}{if $moderator->userID}<a href="index.php?page=User&amp;userID={@$moderator->userID}{@SID_ARG_2ND}">{$moderator}</a>{else}{$moderator}{/if}{/implode}
									</p>
								{/if}

								{if $child.additionalBoxes|isset}{@$child.additionalBoxes}{/if}
							</div>
						</div>

						{if $lastChapters.$libraryID|isset}
							<div class="librarylistLastChapter">
								<div class="containerIconSmall"><a href="index.php?page=Story&amp;storyID={@$lastChapters.$libraryID->storyID}&amp;action=firstNew{@SID_ARG_2ND}"><img src="{icon}goToFirstNewChapterS.png{/icon}" alt="" title="{lang}sls.index.gotoFirstNewChapter{/lang}" /></a></div>
								<div class="containerContentSmall">
									<p>
										<span class="prefix"><strong>{lang}{$lastChapters.$libraryID->prefix}{/lang}</strong></span>
										<a href="index.php?page=Story&amp;storyID={@$lastChapters.$libraryID->storyID}&amp;action=firstNew{@SID_ARG_2ND}">{$lastChapters.$libraryID->topic}</a>
									</p>
									<p>{lang}sls.library.stories.chapterBy{/lang}
										{if $lastChapters.$libraryID->lastChaptererID != 0}
											<a href="index.php?page=User&amp;userID={@$lastChapters.$libraryID->lastChaptererID}{@SID_ARG_2ND}">{$lastChapters.$libraryID->lastChapterer}</a>
										{else}
											{$lastChapters.$libraryID->lastChapterer}
										{/if}
										<span class="light">({@$lastChapters.$libraryID->lastChapterTime|shorttime})</span>
									</p>
								</div>
							</div>
						{/if}

						{if $libraryStats.$libraryID|isset}
							<div class="librarylistStats">
								<dl>
									<dt>{lang}sls.library.stats.stories{/lang}</dt>
									<dd>{#$libraryStats[$libraryID]['stories']}</dd>
									<dt>{lang}sls.library.stats.chapters{/lang}</dt>
									<dd>{#$libraryStats[$libraryID]['chapters']}</dd>
								</dl>
							</div>
						{/if}
						<!--[if IE 7]><span> </span><![endif]-->
					</div>
			{/if}

			{if $library->isCategory()}
				{* category *}
				{cycle name='librarylistCycle' advance=false print=false reset=true}
				<li{if $depth == 1} class="category border"{/if}>
					<div class="containerHead librarylistInner library{@$libraryID}"{if $library->imageShowAsBackground}{if $library->image || $newChapters.$libraryID && $library->imageNew} style="background-image: url({if $newChapters.$libraryID && $library->imageNew}{$library->imageNew}{else}{$library->image}{/if}); background-repeat: {$library->imageBackgroundRepeat}"{/if}{/if}>
						<div class="librarylistTitle">
							<div class="containerIcon">
								{if $open}
									{capture assign=showCategoryTitle}{lang}sls.index.showCat{/lang}{/capture}
									{capture assign=hideCategoryTitle}{lang}sls.index.hideCat{/lang}{/capture}
									<a href="{$selfLink}&amp;closeCategory={@$libraryID}{@SID_ARG_2ND}#libraryLink{@$libraryNo}" onclick="return !openList('category{@$libraryID}', { save: true, openTitle: '{@$showCategoryTitle|encodeJS}', closeTitle: '{@$hideCategoryTitle|encodeJS}' })"><img id="category{@$libraryID}Image" src="{icon}minusS.png{/icon}" alt="" title="{lang}sls.index.hideCat{/lang}" /></a>
								{else}
									<a href="{$selfLink}&amp;openCategory={@$libraryID}{@SID_ARG_2ND}#libraryLink{@$libraryNo}"><img src="{icon}plusS.png{/icon}" alt="" title="{lang}sls.index.showCat{/lang}" /></a>
								{/if}
							</div>
							<div class="containerContent">
								{if $depth > 3}<h6 class="libraryTitle">{else}<h{@$depth+2} class="libraryTitle">{/if}
									<a id="libraryLink{@$libraryNo}" {if $newChapters.$libraryID}class="new" {/if}href="index.php?page=Library&amp;libraryID={@$libraryID}{@SID_ARG_2ND}">{lang}{$library->title}{/lang}{if $unreadStoriesCount.$libraryID|isset} ({#$unreadStoriesCount.$libraryID}){/if}</a>
								{if $depth > 3}</h6>{else}</h{@$depth+2}>{/if}
								{if $library->description}
									<p class="librarylistDescription">
										{lang}{if $library->allowDescriptionHtml}{@$library->description}{else}{$library->description}{/if}{/lang}
									</p>
								{/if}

								{if $subLibraries.$libraryID|isset}
									<div class="librarylistSublibraries">
										<ul>{foreach name='subLibraries' from=$subLibraries.$libraryID item=subLibrary}{assign var="subLibraryID" value=$subLibrary->libraryID}{counter assign=libraryNo print=false}<li{if $tpl.foreach.subLibraries.last} class="last"{/if}>{if $depth > 4}<h6>{else}<h{@$depth+3}>{/if}<img id="libraryIcon{@$libraryNo}" src="{icon}{if $subLibrary->isLibrary()}library{if $newChapters.$subLibraryID}New{/if}{elseif $subLibrary->isCategory()}category{else}libraryRedirect{/if}S.png{/icon}" alt="" {if $subLibrary->isLibrary() && $newChapters.$subLibraryID}title="{lang}sls.library.markAsReadByDoubleClick{/lang}" {/if}/>{*
															*}&nbsp;<a id="libraryLink{@$libraryNo}" {if $newChapters.$subLibraryID}class="new" {/if}{if $subLibrary->isExternalLink()}class="externalURL" {/if}href="index.php?page=Library&amp;libraryID={@$subLibraryID}{@SID_ARG_2ND}">{lang}{$subLibrary->title}{/lang}{if $unreadStoriesCount.$subLibraryID|isset} <span>({#$unreadStoriesCount.$subLibraryID})</span>{/if}</a>{if $depth > 4}</h6>{else}</h{@$depth+3}>{/if}{*
														*}{if $newChapters.$subLibraryID}<script type="text/javascript">
															//<![CDATA[
															libraries.set({@$libraryNo}, {
																'libraryNo': {@$libraryNo},
																'libraryID': {@$subLibraryID},
																'icon': '{icon}{@$subLibrary->getIconName()}S.png{/icon}'
															});
															//]]>
														</script>{/if}</li>{/foreach}</ul>
									</div>
								{/if}

								{if $child.additionalBoxes|isset}{@$child.additionalBoxes}{/if}
							</div>
						</div>
					</div>
			{/if}

			{if $library->isExternalLink()}
				{* external url *}
				<li{if $depth == 1} class="link border"{/if}>
					<div class="container-{cycle name='librarylistCycle'} librarylistInner library{@$libraryID}"{if $library->imageShowAsBackground && $library->image} style="background-image: url({$library->image}); background-repeat: {$library->imageBackgroundRepeat}"{/if}>
						<div class="librarylistTitle{if LIBRARY_LIST_ENABLE_LAST_CHAPTER && LIBRARY_LIST_ENABLE_STATS} librarylistCols-3{else}{if LIBRARY_LIST_ENABLE_LAST_CHAPTER || LIBRARY_LIST_ENABLE_STATS} librarylistCols-2{/if}{/if}">
							<div class="containerIcon">
								<img src="{if $library->image && !$library->imageShowAsBackground}{$library->image}{else}{icon}libraryRedirectM.png{/icon}{/if}" alt="" />
							</div>
							<div class="containerContent">
								{if $depth > 3}<h6 class="libraryTitle">{else}<h{@$depth+2} class="libraryTitle">{/if}
									<a href="index.php?page=Library&amp;libraryID={@$libraryID}{@SID_ARG_2ND}" class="externalURL">{lang}{$library->title}{/lang}</a>
								{if $depth > 3}</h6>{else}</h{@$depth+2}>{/if}

								{if $library->description}
									<p class="librarylistDescription">
										{lang}{if $library->allowDescriptionHtml}{@$library->description}{else}{$library->description}{/if}{/lang}
									</p>
								{/if}

								{if $child.additionalBoxes|isset}{@$child.additionalBoxes}{/if}
							</div>
						</div>

						{if $libraryStats.$libraryID|isset}
							<div class="librarylistStats">
								<dl>
									<dt>{lang}sls.library.clicks{/lang}</dt>
									<dd>{#$library->getClicks()}</dd>
								</dl>
							</div>
						{/if}
					</div>
			{/if}

			{if $hasChildren}<ul id="category{@$libraryID}">{else}</li>{/if}
			{if $openParents > 0}{@"</ul></li>"|str_repeat:$openParents}{/if}
		{/foreach}
	</ul>
{/if}