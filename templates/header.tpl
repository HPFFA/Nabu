{if !$this->user->userID && !LOGIN_USE_CAPTCHA}
	{counter name='tabindex' start=4 print=false}
{else}
	{counter name='tabindex' start=0 print=false}
{/if}

<div id="headerContainer">
	<a id="top"></a>
	<div id="userPanel" class="userPanel">
		<div class="userPanelInner">
			<p style="display: none;" id="userAvatar">
				{if $this->user->userID && $this->user->getAvatar()}<a href="index.php?page=User&amp;userID={@$this->user->userID}{@SID_ARG_2ND}">{@$this->user->getAvatar()}</a>{else}<img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt="" />{/if}
			</p>
			<p id="userNote">
				{if $this->user->userID != 0}{lang}sls.header.userNote.user{/lang}{else}{lang}sls.header.userNote.guest{/lang}{/if}
			</p>
			<div id="userMenu">
				<ul>
					{if $this->user->userID != 0}
						<li id="userMenuLogout"><a href="index.php?action=UserLogout&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}"><img src="{icon}logoutS.png{/icon}" alt="" /> <span>{lang}sls.header.userMenu.logout{/lang}</span></a></li>
						<li id="userMenuProfileEdit"><a href="index.php?form=UserProfileEdit{@SID_ARG_2ND}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}sls.header.userMenu.profile{/lang}</span></a></li>
						
						{if $this->user->getPermission('admin.general.canUseAcp')}
							<li id="userMenuACP"><a href="acp/index.php?packageID={@PACKAGE_ID}"><img src="{icon}acpS.png{/icon}" alt="" /> <span>{lang}sls.header.userMenu.acp{/lang}</span></a></li>
						{/if}
					{else}
						<li id="userMenuLogin" class="options"><a href="index.php?form=UserLogin{@SID_ARG_2ND}" id="loginButton"><img src="{icon}loginS.png{/icon}" alt="" id="loginButtonImage" /> <span>{lang}sls.header.userMenu.login{/lang}</span></a></li>

						
					{/if}
				</ul>
			</div>
		</div>
	</div>

	{if !$this->user->userID && !LOGIN_USE_CAPTCHA}
		<script type="text/javascript">
			//<![CDATA[
			document.observe("dom:loaded", function() {
				var loginFormVisible = false;

				var loginBox = $('quickLoginBox');
				var loginButton = $('loginButton');

				if (loginButton && loginBox) {
					function showLoginForm(evt) {
						if (loginBox.hasClassName('hidden')) {
							loginBox.setStyle('display: none');
							loginBox.removeClassName('hidden');
						}

						var top = (loginButton.cumulativeOffset()[1] + loginButton.getHeight() + 5);
						var left = loginButton.cumulativeOffset()[0] > $$('body')[0].getWidth()/2 ? loginButton.cumulativeOffset()[0] - loginBox.getWidth() + loginButton.getWidth() : loginButton.cumulativeOffset()[0];
						loginBox.setStyle('left: ' + left + 'px; top: ' + top + 'px;');
						if (loginBox.visible()) {
							new Effect.Parallel([
								new Effect.BlindUp(loginBox),
								new Effect.Fade(loginBox)
							], { duration: 0.3 });
							loginFormVisible = false;
						}
						else {
							new Effect.Parallel([
								new Effect.BlindDown(loginBox),
								new Effect.Appear(loginBox)
							], { duration: 0.3 });
							loginFormVisible = true;
						}
						evt.stop();
					}

					loginButton.observe('click', showLoginForm);
					loginButton.observe('dblclick', function() { document.location.href = fixURL('index.php?form=UserLogin{@SID_ARG_2ND_NOT_ENCODED}'); });

					document.getElementById('quickLoginUsername').onfocus = function() { if (this.value == '{lang}wcf.user.username{/lang}') this.value=''; };
					document.getElementById('quickLoginUsername').onblur = function() { if (this.value == '') this.value = '{lang}wcf.user.username{/lang}'; };
					$('loginButtonImage').src = $('loginButtonImage').src.gsub('loginS.png', 'loginOptionsS.png');
				}
			});
			//]]>
		</script>
	{/if}

	<div id="header">

		{* --- quick search controls ---
		 * $searchScript=search script; default=index.php?form=Search
		 * $searchFieldName=name of the search input field; default=q
		 * $searchFieldValue=default value of the search input field; default=content of $query
		 * $searchFieldTitle=title of search input field; default=language variable sls.header.search.query
		 * $searchFieldOptions=special search options for popup menu; default=empty
		 * $searchExtendedLink=link to extended search form; default=index.php?form=Search{@SID_ARG_2ND}
		 * $searchHiddenFields=optional hidden fields; default=empty
		 * $searchShowExtendedLink=set to false to disable extended search link; default=true
		 *}

		{if !$searchScript|isset}{assign var='searchScript' value='index.php?form=Search'}{/if}
		{if !$searchFieldName|isset}{assign var='searchFieldName' value='q'}{/if}
		{if !$searchFieldValue|isset && $query|isset}{assign var='searchFieldValue' value=$query}{/if}
		{if !$searchFieldTitle|isset}{assign var='searchFieldTitle' value='{lang}sls.header.search.query{/lang}'}{/if}
		{if !$searchFieldOptions|isset}
			{capture assign=searchFieldOptions}
				<li><a href="index.php?form=Search&amp;action=unread{@SID_ARG_2ND}">{lang}sls.search.unreadPosts{/lang}</a></li>
				<li><a href="index.php?form=Search&amp;action=unreplied{@SID_ARG_2ND}">{lang}sls.search.unrepliedThreads{/lang}</a></li>
				<li><a href="index.php?form=Search&amp;action=24h{@SID_ARG_2ND}">{lang}sls.search.storiesOfTheLast24Hours{/lang}</a></li>
				{if $this->user->userID}<li><a href="index.php?form=Search&amp;action=newPostsSince&amp;since={@$this->user->boardLastVisitTime}{@SID_ARG_2ND}">{lang}sls.search.newPostsSinceLastVisit{/lang}</a></li>{/if}
			{/capture}
		{/if}
		{if !$searchExtendedLink|isset}{assign var='searchExtendedLink' value='index.php?form=Search'|concat:SID_ARG_2ND}{/if}
		{if !$searchShowExtendedLink|isset}{assign var='searchShowExtendedLink' value=true}{/if}

		<div id="search">
			<form method="post" action="{@$searchScript}">

				<div class="searchContainer">
					<input type="text" tabindex="{counter name='tabindex'}" id="searchInput" class="inputText" name="{@$searchFieldName}" value="{if !$searchFieldValue|empty}{$searchFieldValue}{else}{@$searchFieldTitle}{/if}" />
					<input type="image" tabindex="{counter name='tabindex'}" id="searchSubmit" class="searchSubmit inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
					{@SID_INPUT_TAG}
					{if $searchHiddenFields|isset}{@$searchHiddenFields}{else}<input type="hidden" name="types[]" value="post" />{/if}

					<script type="text/javascript">
						//<![CDATA[
						document.getElementById('searchInput').setAttribute('autocomplete', 'off');
						document.getElementById('searchInput').onfocus = function() { if (this.value == '{@$searchFieldTitle}') this.value=''; };
						document.getElementById('searchInput').onblur = function() { if (this.value == '') this.value = '{@$searchFieldTitle}'; };
						document.getElementById('searchSubmit').ondblclick = function() { window.location = 'index.php?form=Search{@SID_ARG_2ND_NOT_ENCODED}'; };
						{if $searchFieldOptions || $searchShowExtendedLink}
							popupMenuList.register("searchInput");
							document.getElementById('searchInput').className += " searchOptions";
						{/if}
						//]]>
					</script>
					{if $searchFieldOptions || $searchShowExtendedLink}
						<div class="searchInputMenu">
							<div class="hidden" id="searchInputMenu">
								<div class="pageMenu smallFont">
									<ul>
										{@$searchFieldOptions}
										{if $searchShowExtendedLink}<li><a href="{@$searchExtendedLink}{if !$searchFieldValue|empty}&amp;defaultQuery={$searchFieldValue|rawurlencode}{/if}">{lang}sls.header.search.extended{/lang}</a></li>{/if}
									</ul>
								</div>
							</div>
						</div>
					{/if}

					{if $searchShowExtendedLink}
						<noscript>
							<p><a href="{@$searchExtendedLink}">{lang}sls.header.search.extended{/lang}</a></p>
						</noscript>
					{/if}
				</div>
			</form>
		</div>
		<div id="logo">
			<div class="logoInner">
				<h1 class="pageTitle"><a href="index.php?page=Index{@SID_ARG_2ND}">{lang}{PAGE_TITLE}{/lang}</a></h1>
				{if $this->getStyle()->getVariable('page.logo.image')}
					<a href="index.php?page=Index{@SID_ARG_2ND}" class="pageLogo">
						<img src="{$this->getStyle()->getVariable('page.logo.image')}" title="{lang}{PAGE_TITLE}{/lang}" alt="" />
					</a>
				{elseif $this->getStyle()->getVariable('page.logo.image.application.use') == 1}
					<a href="index.php?page=Index{@SID_ARG_2ND}" class="pageLogo">
						<img src="{@RELATIVE_SLS_DIR}images/sls3-header-logo.png" title="{lang}{PAGE_TITLE}{/lang}" alt="" />
					</a>
				{/if}
			</div>
		</div>
	</div>

	{include file=headerMenu}

{* user messages system*}
{capture append=userMessages}
	{if $this->user->userID}

		{if $this->user->activationCode && REGISTER_ACTIVATION_METHOD == 1}<p class="warning">{lang}wcf.user.register.needsActivation{/lang}</p>{/if}

		{if $this->session->isNew}<p class="info">{lang}sls.header.welcomeBack{/lang}</p>{/if}

		{if MODULE_PM == 1 && $this->user->showPmPopup && $this->user->pmOutstandingNotifications && $this->user->getOutstandingNotifications()|count > 0}
			<div class="info deletable" id="pmOutstandingNotifications">
				<a href="index.php?page=PM&amp;action=disableNotifications&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" class="close deleteButton"><img src="{icon}closeS.png{/icon}" alt="" title="{lang}wcf.pm.notification.cancel{/lang}" longdesc="" /></a>
				<p>{lang}wcf.pm.notification.report{/lang}</p>
				<ul>
					{foreach from=$this->user->getOutstandingNotifications() item=outstandingNotification}
						<li title="{$outstandingNotification->getMessagePreview()}">
							<a href="index.php?page=PMView&amp;pmID={@$outstandingNotification->pmID}{@SID_ARG_2ND}#pm{@$outstandingNotification->pmID}">{$outstandingNotification->subject}</a>
							{lang}wcf.pm.messageFrom{/lang}

							{if $outstandingNotification->userID}
								<a href="index.php?page=User&amp;userID={@$outstandingNotification->userID}{@SID_ARG_2ND}">{$outstandingNotification->username}</a>
							{elseif $outstandingNotification->username}
								{$outstandingNotification->username}
							{else}
								{lang}wcf.pm.author.system{/lang}
							{/if}
						</li>
					{/foreach}
				</ul>
			</div>
		{/if}

		{if $this->user->numberOfInvitations && $this->user->getInvitations()|count}
			<div class="info deletable" id="invitationContainer">
				<a href="index.php?action=WhiteListNotificationDisable&amp;ajax=1&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" class="close deleteButton"><img src="{icon}closeS.png{/icon}" alt="" title="{lang}wcf.user.whitelist.notification.cancel{/lang}" longdesc="" /></a>
				<p>{lang}wcf.user.whitelist.notification{/lang}</p>
				<ul class="itemList">
					{foreach from=$this->user->getInvitations() item=member}
						<li class="deletable">
							<div class="buttons">
								<a href="index.php?form=WhiteListEdit&amp;accept={@$member->userID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" class="deleteButton" title="{lang}wcf.user.whitelist.accept{/lang}"><img src="{icon}checkS.png{/icon}" alt="{lang}wcf.user.whitelist.accept{/lang}" longdesc="{lang}wcf.user.whitelist.accept.sure{/lang}" /></a>
								<a href="index.php?form=WhiteListEdit&amp;decline={@$member->userID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" class="deleteButton" title="{lang}wcf.user.whitelist.decline{/lang}"><img src="{icon}deleteS.png{/icon}" alt="{lang}wcf.user.whitelist.decline{/lang}" longdesc="{lang}wcf.user.whitelist.decline.sure{/lang}" /></a>
							</div>
							<p class="itemListTitle"><a href="index.php?page=User&amp;userID={@$member->userID}{@SID_ARG_2ND}">{$member->username}</a></p>
						</li>
					{/foreach}
				</ul>
			</div>
			<script type="text/javascript">
				//<![CDATA[
				document.observe('wcf:inlineDelete', function() {
					if ($('invitationContainer') && !$('invitationContainer').down('li')) {
						inlineDelete($('invitationContainer').down('.close'));
					}
				});
				//]]>
			</script>
		{/if}

	{elseif !$this->session->spiderID}

		{if $this->session->isNew}<p class="info">{lang}wcf.user.register.welcome{/lang}</p>{/if}

	{/if}
	{if OFFLINE == 1 && $this->user->getPermission('user.board.canViewBoardOffline')}
		<div class="warning">
			{lang}sls.global.offline{/lang}
			<p>{if OFFLINE_MESSAGE_ALLOW_HTML}{@OFFLINE_MESSAGE}{else}{@OFFLINE_MESSAGE|htmlspecialchars|nl2br}{/if}</p>
		</div>
	{/if}
{/capture}
</div>
<div id="mainContainer">
{if $additionalHeaderContents|isset}{@$additionalHeaderContents}{/if}
