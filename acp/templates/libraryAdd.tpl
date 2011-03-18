{include file='header'}

{if $action == 'add'}{assign var=dataPermission value="admin.library.canAddLibrary"}
{else}{assign var=dataPermission value="admin.library.canEditLibrary"}
{/if}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	var tabMenu = new TabMenu();
	onloadEvents.push(function() { tabMenu.showSubTabMenu("{$activeTabMenuItem}") });
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_SLS_DIR}icon/library{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}sls.acp.library.{@$action}{/lang}</h2>
		{if $libraryID|isset}<p>{lang}{$library->title}{/lang}</p>{/if}
	</div>
</div>

{if $errorField}
<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
<p class="success">{if $action == 'add'}{lang}sls.acp.library.add.success{/lang}{else}{lang}sls.acp.library.edit.success{/lang}{/if}</p>	
{/if}

<script type="text/javascript" src="{@RELATIVE_SLS_DIR}acp/js/PermissionList.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	var language = new Object();
	
	{literal}
	// static
	language['sls.acp.library.permissions.permissionsFor'] = '{lang}sls.acp.library.permissions.permissionsFor{/lang}';
	language['sls.acp.library.permissions.fullControl'] = '{lang}sls.acp.library.permissions.fullControl{/lang}';
	{/literal}
	
	// dynamic
	{foreach from=$moderatorSettings item=moderatorSetting}
	language['sls.acp.library.permissions.{@$moderatorSetting}'] = '{lang}sls.acp.library.permissions.{@$moderatorSetting}{/lang}';
	{/foreach}
	{foreach from=$permissionSettings item=permissionSetting}
	language['sls.acp.library.permissions.{@$permissionSetting}'] = '{lang}sls.acp.library.permissions.{@$permissionSetting}{/lang}';
	{/foreach}
	
	function setLibraryType(newType) {
		switch (newType) {
			case 0:
				showOptions('filter', 'style', 'settings');
				hideOptions('externalURLDiv');
				break;
			case 1:
				showOptions('style');
				hideOptions('externalURLDiv', 'filter', 'settings');
				break;
			case 2:
				showOptions('externalURLDiv');
				hideOptions('filter', 'style', 'settings');
				break;
		}
	}
	onloadEvents.push(function() { setLibraryType({@$libraryType}); });
	
	var permissions = new Array();
	{assign var=i value=0}
	{foreach from=$permissions item=permission}
	permissions[{@$i}] = new Object();
	permissions[{@$i}]['name'] = '{@$permission.name|encodeJS}';
	permissions[{@$i}]['type'] = '{@$permission.type}';
	permissions[{@$i}]['id'] = '{@$permission.id}';
	permissions[{@$i}]['settings'] = new Object();
	permissions[{@$i}]['settings']['fullControl'] = -1;
		
	{foreach from=$permission.settings key=setting item=value}
	{if $setting != 'name' && $setting != 'type' && $setting != 'id'}
	permissions[{@$i}]['settings']['{@$setting}'] = {@$value};
	{/if}
	{/foreach}
	{assign var=i value=$i+1}
	{/foreach}
	
	var moderators = new Array();
	{assign var=i value=0}
	{foreach from=$moderators item=moderator}
	moderators[{@$i}] = new Object();
	moderators[{@$i}]['name'] = '{@$moderator.name|encodeJS}';
	moderators[{@$i}]['type'] = '{@$moderator.type}';
	moderators[{@$i}]['id'] = '{@$moderator.id}';
	moderators[{@$i}]['settings'] = new Object();
	moderators[{@$i}]['settings']['fullControl'] = -1;
		
	{foreach from=$moderator.settings key=setting item=value}
	{if $setting != 'name' && $setting != 'type' && $setting != 'id'}
	moderators[{@$i}]['settings']['{@$setting}'] = {@$value};
	{/if}
	{/foreach}
	{assign var=i value=$i+1}
	{/foreach}
	
	var moderatorSettings = new Array({implode from=$moderatorSettings item=moderatorSetting}'{@$moderatorSetting}'{/implode});
	var permissionSettings = new Array({implode from=$permissionSettings item=permissionSetting}'{@$permissionSetting}'{/implode});
	
	
	onloadEvents.push(function() {
		// moderators
		var list1 = new PermissionList('moderator', moderators, moderatorSettings);
		// user/group permissions
		var list2 = new PermissionList('permission', permissions, permissionSettings);
	
		// add onsubmit event
		document.getElementById('libraryAddForm').onsubmit = function() {
			if (suggestion.selectedIndex != -1) return false;
			if (list1.inputHasFocus || list2.inputHasFocus) return false;
			list1.submit(this); list2.submit(this);
		};
	});
	
	//]]>
</script>
<div class="contentHeader">

	<div class="largeButtons">
		<ul><li><a href="index.php?page=LibraryList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.menu.link.content.library.view{/lang}"><img src="{@RELATIVE_SLS_DIR}icon/libraryM.png" alt="" /> <span>{lang}sls.acp.menu.link.content.library.view{/lang}</span></a></li></ul>
	</div>
</div>

<form method="post" action="index.php?form=Library{@$action|ucfirst}" id="libraryAddForm">
	{if $libraryID|isset && $libraryQuickJumpOptions|count > 1}
	<fieldset>
		<legend>{lang}sls.acp.library.edit{/lang}</legend>
		<div class="formElement">
			<div class="formFieldLabel">
				<label for="libraryChange">{lang}sls.acp.library.edit{/lang}</label>
			</div>
			<div class="formField">
				<select id="libraryChange" onchange="document.location.href=fixURL('index.php?form=LibraryEdit&amp;libraryID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
						{htmloptions options=$libraryQuickJumpOptions selected=$libraryID disableEncoding=true}
				</select>
			</div>
		</div>
	</fieldset>
	{/if}

	<div class="tabMenu">
		<ul>
			{if $this->user->getPermission($dataPermission)}<li id="data"><a onclick="tabMenu.showSubTabMenu('data');"><span>{lang}sls.acp.library.data{/lang}</span></a></li>{/if}
			{if $this->user->getPermission('admin.library.canEditPermissions')}<li id="permissions"><a onclick="tabMenu.showSubTabMenu('permissions');"><span>{lang}sls.acp.library.permissions{/lang}</span></a></li>{/if}
			{if $this->user->getPermission('admin.library.canEditModerators')}<li id="moderators"><a onclick="tabMenu.showSubTabMenu('moderators');"><span>{lang}sls.acp.library.moderators{/lang}</span></a></li>{/if}
			{if $this->user->getPermission('admin.library.canEditWarning')}<li id="warnings"><a onclick="tabMenu.showSubTabMenu('warnings');"><span>{lang}sls.acp.library.warnings{/lang}</span></a></li>{/if}
			{if $this->user->getPermission('admin.library.canEditGenre')}<li id="gernes"><a onclick="tabMenu.showSubTabMenu('genres');"><span>{lang}sls.acp.library.gernes{/lang}</span></a></li>{/if}
			{if $this->user->getPermission('admin.library.canEditCharacter')}<li id="characters"><a onclick="tabMenu.showSubTabMenu('characters');"><span>{lang}sls.acp.library.characters{/lang}</span></a></li>{/if}
			{if $this->user->getPermission('admin.library.canEditClassification')}<li id="classifications"><a onclick="tabMenu.showSubTabMenu('classifications');"><span>{lang}sls.acp.library.classifications{/lang}</span></a></li>{/if}
			{if $additionalTabs|isset}{@$additionalTabs}{/if}
		</ul>
	</div>
	<div class="subTabMenu">
		<div class="containerHead"><div> </div></div>
	</div>

	{if $this->user->getPermission($dataPermission)}
	<div class="border tabMenuContent hidden" id="data-content">
		<div class="container-1">
			<h3 class="subHeadline">{lang}sls.acp.library.data{/lang}</h3>

			<fieldset>
				<legend>{lang}sls.acp.library.libraryType{/lang}</legend>
				<div class="formElement{if $errorField == 'libraryType'} formError{/if}">
					<ul class="formOptions">
						<li><label><input onclick="if (IS_SAFARI) setLibraryType(0)" onfocus="setLibraryType(0)" type="radio" name="libraryType" value="0" {if $libraryType == 0}checked="checked" {/if}/> {lang}sls.acp.library.libraryType.0{/lang}</label></li>
						<li><label><input onclick="if (IS_SAFARI) setLibraryType(1)" onfocus="setLibraryType(1)" type="radio" name="libraryType" value="1" {if $libraryType == 1}checked="checked" {/if}/> {lang}sls.acp.library.libraryType.1{/lang}</label></li>
						<li><label><input onclick="if (IS_SAFARI) setLibraryType(2)" onfocus="setLibraryType(2)" type="radio" name="libraryType" value="2" {if $libraryType == 2}checked="checked" {/if}/> {lang}sls.acp.library.libraryType.2{/lang}</label></li>
					</ul>
						{if $errorField == 'libraryType'}
					<p class="innerError">
								{if $errorType == 'invalid'}{lang}sls.acp.library.error.libraryType.invalid{/lang}{/if}
					</p>
						{/if}
				</div>
			</fieldset>

			<fieldset>
				<legend>{lang}sls.acp.library.data.general{/lang}</legend>

				<div class="formElement{if $errorField == 'title'} formError{/if}">
					<div class="formFieldLabel">
						<label for="title">{lang}sls.acp.library.title{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="title" name="title" value="{$title}" />
							{if $errorField == 'title'}
						<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
						</p>
							{/if}
					</div>
				</div>

				<div id="descriptionDiv" class="formElement">
					<div class="formFieldLabel">
						<label for="description">{lang}sls.acp.library.description{/lang}</label>
					</div>
					<div class="formField">
						<textarea id="description" name="description" cols="40" rows="10">{$description}</textarea>
						<label><input type="checkbox" name="allowDescriptionHtml" value="1" {if $allowDescriptionHtml}checked="checked" {/if}/> {lang}sls.acp.library.allowDescriptionHtml{/lang}</label>
					</div>
				</div>

				<div id="externalURLDiv" class="formElement{if $errorField == 'externalURL'} formError{/if}">
					<div class="formFieldLabel">
						<label for="externalURL">{lang}sls.acp.library.externalURL{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="externalURL" name="externalURL" value="{$externalURL}" />
							{if $errorField == 'externalURL'}
						<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
						</p>
							{/if}
					</div>
				</div>

					{if $additionalGeneralFields|isset}{@$additionalGeneralFields}{/if}
			</fieldset>

			<fieldset>
				<legend>{lang}sls.acp.library.data.position{/lang}</legend>

					{if $libraryOptions|count > 0}
				<div class="formElement{if $errorField == 'parentID'} formError{/if}" id="parentIDDiv">
					<div class="formFieldLabel">
						<label for="parentID">{lang}sls.acp.library.parentID{/lang}</label>
					</div>
					<div class="formField">
						<select name="parentID" id="parentID">
							<option value="0"></option>
									{htmlOptions options=$libraryOptions disableEncoding=true selected=$parentID}
						</select>
								{if $errorField == 'parentID'}
						<p class="innerError">
										{if $errorType == 'invalid'}{lang}sls.acp.library.error.parentID.invalid{/lang}{/if}
						</p>
								{/if}
					</div>
					<div class="formFieldDesc hidden" id="parentIDHelpMessage">
						<p>{lang}sls.acp.library.parentID.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('parentID');
					//]]></script>
					{/if}

				<div class="formElement{if $errorField == 'position'} formError{/if}" id="positionDiv">
					<div class="formFieldLabel">
						<label for="position">{lang}sls.acp.library.position{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="position" name="position" value="{@$position}" />
							{if $errorField == 'position'}
						<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
						</p>
							{/if}
					</div>
					<div class="formFieldDesc hidden" id="positionHelpMessage">
						<p>{lang}sls.acp.library.position.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('position');
					//]]></script>

				<div class="formElement" id="invisibleDiv">
					<div class="formField">
						<label id="invisible"><input type="checkbox" name="invisible" value="1" {if $invisible}checked="checked" {/if}/> {lang}sls.acp.library.invisible{/lang}</label>
					</div>
					<div class="formFieldDesc hidden" id="invisibleHelpMessage">
						<p>{lang}sls.acp.library.invisible.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('invisible');
					//]]></script>

					{if $additionalPositionFields|isset}{@$additionalPositionFields}{/if}
			</fieldset>

			<fieldset id="settings">
				<legend>{lang}sls.acp.library.data.settings{/lang}</legend>

				<div class="formElement">
					<div class="formField">
						<label id="closed"><input type="checkbox" name="closed" value="1" {if $closed}checked="checked" {/if}/> {lang}sls.acp.library.closed{/lang}</label>
					</div>
				</div>

				<div class="formElement">
					<div class="formField">
						<label id="countUserPosts"><input type="checkbox" name="countUserChapters" value="1" {if $countUserChapters}checked="checked" {/if}/> {lang}sls.acp.library.countUserChapters{/lang}</label>
					</div>
				</div>

				<div class="formElement">
					<div class="formField">
						<label id="showSubLibraries"><input type="checkbox" name="showSubLibraries" value="1" {if $showSubLibraries}checked="checked" {/if}/> {lang}sls.acp.library.showSubLibraries{/lang}</label>
					</div>
				</div>

				<div class="formElement">
					<div class="formField">
						<label id="searchable"><input type="checkbox" name="searchable" value="1" {if $searchable}checked="checked" {/if}/> {lang}sls.acp.library.searchable{/lang}</label>
					</div>
				</div>

				<div class="formElement">
					<div class="formField">
						<label id="searchableForSimilarStories"><input type="checkbox" name="searchableForSimilarStories" value="1" {if $searchableForSimilarStories}checked="checked" {/if}/> {lang}sls.acp.library.searchableForSimilarStories{/lang}</label>
					</div>
				</div>

				<div class="formElement">
					<div class="formField">
						<label id="ignorable"><input type="checkbox" name="ignorable" value="1" {if $ignorable}checked="checked" {/if}/> {lang}sls.acp.library.ignorable{/lang}</label>
					</div>
				</div>

					{if MODULE_STORY_MARKING_AS_DONE}
				<div class="formElement">
					<div class="formField">
						<label><input type="checkbox" name="enableMarkingAsDone" value="1" {if $enableMarkingAsDone == 1}checked="checked" {/if}/> {lang}sls.acp.library.enableMarkingAsDone{/lang}</label>
					</div>
				</div>
					{/if}

				<div class="formElement">
					<div class="formFieldLabel">
						<label for="enableRating">{lang}sls.acp.library.rating{/lang}</label>
					</div>
					<div class="formField">
						<select name="enableRating" id="enableRating">
							<option value="-1"></option>
							<option value="1"{if $enableRating == 1} selected="selected"{/if}>{lang}sls.acp.library.rating.enable{/lang}</option>
							<option value="0"{if $enableRating == 0} selected="selected"{/if}>{lang}sls.acp.library.rating.disable{/lang}</option>
						</select>
					</div>
				</div>

					{if $additionalSettings|isset}{@$additionalSettings}{/if}
			</fieldset>

			<fieldset id="filter">
				<legend>{lang}sls.acp.library.data.filter{/lang}</legend>

				<div class="formElement{if $errorField == 'daysPrune'} formError{/if}">
					<div class="formFieldLabel">
						<label for="daysPrune">{lang}sls.acp.library.daysPrune{/lang}</label>
					</div>
					<div class="formField">
						<select name="daysPrune" id="daysPrune">
							<option value=""></option>
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
				</div>

				<div class="formElement{if $errorField == 'sortField'} formError{/if}">
					<div class="formFieldLabel">
						<label for="sortField">{lang}sls.acp.library.sortField{/lang}</label>
					</div>
					<div class="formField">
						<select name="sortField" id="sortField">
							<option value=""></option>
							<option value="topic"{if $sortField == 'topic'} selected="selected"{/if}>{lang}sls.library.sortBy.topic{/lang}</option>
							<option value="username"{if $sortField == 'username'} selected="selected"{/if}>{lang}sls.library.sortBy.starter{/lang}</option>
							<option value="time"{if $sortField == 'time'} selected="selected"{/if}>{lang}sls.library.sortBy.startTime{/lang}</option>
							<option value="ratingResult"{if $sortField == 'ratingResult'} selected="selected"{/if}>{lang}sls.library.sortBy.rating{/lang}</option>
							<option value="replies"{if $sortField == 'replies'} selected="selected"{/if}>{lang}sls.library.sortBy.replies{/lang}</option>
							<option value="views"{if $sortField == 'views'} selected="selected"{/if}>{lang}sls.library.sortBy.views{/lang}</option>
							<option value="lastPostTime"{if $sortField == 'lastPostTime'} selected="selected"{/if}>{lang}sls.library.sortBy.lastPostTime{/lang}</option>
						</select>
							{if $errorField == 'sortField'}
						<p class="innerError">
									{if $errorType == 'invalid'}{lang}wcf.global.error.empty{/lang}{/if}
						</p>
							{/if}
					</div>
				</div>

				<div class="formElement{if $errorField == 'sortOrder'} formError{/if}">
					<div class="formFieldLabel">
						<label for="sortOrder">{lang}sls.acp.library.sortOrder{/lang}</label>
					</div>
					<div class="formField">
						<select name="sortOrder" id="sortOrder">
							<option value=""></option>
							<option value="ASC"{if $sortOrder == 'ASC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
							<option value="DESC"{if $sortOrder == 'DESC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
						</select>
							{if $errorField == 'sortOrder'}
						<p class="innerError">
									{if $errorType == 'invalid'}{lang}wcf.global.error.empty{/lang}{/if}
						</p>
							{/if}
					</div>
				</div>

				<div class="formElement{if $errorField == 'chapterSortOrder'} formError{/if}">
					<div class="formFieldLabel">
						<label for="chapterSortOrder">{lang}sls.acp.library.chapterSortOrder{/lang}</label>
					</div>
					<div class="formField">
						<select name="chapterSortOrder" id="chapterSortOrder">
							<option value=""></option>
							<option value="ASC"{if $chapterSortOrder == 'ASC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
							<option value="DESC"{if $chapterSortOrder == 'DESC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
						</select>
							{if $errorField == 'chapterSortOrder'}
						<p class="innerError">
									{if $errorType == 'invalid'}{lang}wcf.global.error.empty{/lang}{/if}
						</p>
							{/if}
					</div>
				</div>

				<div class="formElement">
					<div class="formFieldLabel">
						<label for="storiesPerPage">{lang}sls.acp.library.storiesPerPage{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="storiesPerPage" name="storiesPerPage" value="{@$storiesPerPage}" />
					</div>
				</div>

					{if $additionalFilterFields|isset}{@$additionalFilterFields}{/if}
			</fieldset>

			<fieldset id="style">
				<legend>{lang}sls.acp.library.data.style{/lang}</legend>

					{if $availableStyles|count > 1}
				<div class="formElement">
					<div class="formFieldLabel">
						<label for="styleID">{lang}sls.acp.library.styleID{/lang}</label>
					</div>
					<div class="formField">
						<select name="styleID" id="styleID">
							<option value="0"></option>
									{htmlOptions options=$availableStyles selected=$styleID}
						</select>
						<label><input type="checkbox" name="enforceStyle" value="1" {if $enforceStyle}checked="checked" {/if}/> {lang}sls.acp.library.enforceStyle{/lang}</label>
					</div>
				</div>
					{/if}

				<div class="formElement" id="imageDiv">
					<div class="formFieldLabel">
						<label for="image">{lang}sls.acp.library.image{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="image" name="image" value="{$image}" />
					</div>
					<div class="formFieldDesc hidden" id="imageHelpMessage">
						<p>{lang}sls.acp.library.image.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">
					//<![CDATA[
					inlineHelp.register('image');
					//]]>
				</script>

				<div class="formElement" id="imageNewDiv">
					<div class="formFieldLabel">
						<label for="imageNew">{lang}sls.acp.library.imageNew{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="imageNew" name="imageNew" value="{$imageNew}" />
					</div>
					<div class="formFieldDesc hidden" id="imageNewHelpMessage">
						<p>{lang}sls.acp.library.imageNew.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">
					//<![CDATA[
					inlineHelp.register('imageNew');
					//]]>
				</script>

				<div class="formElement" id="imageShowAsBackgroundDiv">
					<div class="formField">
						<label><input type="checkbox" name="imageShowAsBackground" value="1" {if $imageShowAsBackground == 1}checked="checked" {/if}/> {lang}sls.acp.library.imageShowAsBackground{/lang}</label>
					</div>
					<div class="formFieldDesc hidden" id="imageShowAsBackgroundHelpMessage">
						<p>{lang}sls.acp.library.imageShowAsBackground.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">
					//<![CDATA[
					inlineHelp.register('imageShowAsBackground');
					//]]>
				</script>

				<div class="formElement" id="imageBackgroundRepeatDiv">
					<div class="formFieldLabel">
						<label for="imageBackgroundRepeat">{lang}sls.acp.library.imageBackgroundRepeat{/lang}</label>
					</div>
					<div class="formField">
						<select name="imageBackgroundRepeat" id="imageBackgroundRepeat">
							<option value="no-repeat"{if $imageBackgroundRepeat == 'no-repeat'} selected="selected"{/if}>{lang}sls.acp.library.imageBackgroundRepeat.no{/lang}</option>
							<option value="repeat-y"{if $imageBackgroundRepeat == 'repeat-y'} selected="selected"{/if}>{lang}sls.acp.library.imageBackgroundRepeat.vertical{/lang}</option>
							<option value="repeat-x"{if $imageBackgroundRepeat == 'repeat-x'} selected="selected"{/if}>{lang}sls.acp.library.imageBackgroundRepeat.horizontal{/lang}</option>
							<option value="repeat"{if $imageBackgroundRepeat == 'repeat'} selected="selected"{/if}>{lang}sls.acp.library.imageBackgroundRepeat.both{/lang}</option>
						</select>
					</div>
					<div class="formFieldDesc hidden" id="imageBackgroundRepeatHelpMessage">
						<p>{lang}sls.acp.library.imageBackgroundRepeat.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">
					//<![CDATA[
					inlineHelp.register('imageBackgroundRepeat');
					//]]>
				</script>

					{if $additionalStyleFields|isset}{@$additionalStyleFields}{/if}
			</fieldset>

				{if $additionalFields|isset}{@$additionalFields}{/if}
		</div>
	</div>
	{/if}

	{if $this->user->getPermission('admin.library.canEditPermissions')}
	<div class="border tabMenuContent hidden" id="permissions-content">
		<div class="container-1">
			<h3 class="subHeadline">{lang}sls.acp.library.permissions{/lang}</h3>

			<div class="formElement">
				<div class="formFieldLabel" id="permissionTitle">
						{lang}sls.acp.library.permissions.title{/lang}
				</div>
				<div class="formField"><div id="permission" class="accessRights"></div></div>
			</div>
			<div class="formElement">
				<div class="formField">
					<input id="permissionAddInput" type="text" name="" value="" class="inputText accessRightsInput" />
					<script type="text/javascript">
						//<![CDATA[
						suggestion.setSource('index.php?page=LibraryPermissionsObjectsSuggest{@SID_ARG_2ND_NOT_ENCODED}');
						suggestion.enableIcon(true);
						suggestion.init('permissionAddInput');
						//]]>
					</script>
					<input id="permissionAddButton" type="button" value="{lang}sls.acp.library.permissions.add{/lang}" />
				</div>
			</div>

			<div class="formElement" style="display: none;">
				<div class="formFieldLabel">
					<div id="permissionSettingsTitle" class="accessRightsTitle"></div>
				</div>
				<div class="formField">
					<div id="permissionHeader" class="accessRightsHeader">
						<span class="deny">{lang}sls.acp.library.permissions.deny{/lang}</span>
						<span class="allow">{lang}sls.acp.library.permissions.allow{/lang}</span>
					</div>
					<div id="permissionSettings" class="accessRights"></div>
				</div>
			</div>
		</div>
	</div>
	{/if}

	{if $this->user->getPermission('admin.library.canEditModerators')}
	<div class="border tabMenuContent hidden" id="moderators-content">
		<div class="container-1">
			<h3 class="subHeadline">{lang}sls.acp.library.moderators{/lang}</h3>

			<div class="formElement">
				<div class="formFieldLabel" id="moderatorTitle">
						{lang}sls.acp.library.permissions.title{/lang}
				</div>
				<div class="formField"><div id="moderator" class="accessRights"></div></div>
			</div>
			<div class="formElement">
				<div class="formField">
					<input id="moderatorAddInput" type="text" name="" value="" class="inputText accessRightsInput" />
					<script type="text/javascript">
						//<![CDATA[
						suggestion.init('moderatorAddInput');
						//]]>
					</script>
					<input id="moderatorAddButton" type="button" value="{lang}sls.acp.library.permissions.add{/lang}" />
				</div>
			</div>

			<div class="formElement" style="display: none;">
				<div class="formFieldLabel">
					<div id="moderatorSettingsTitle" class="accessRightsTitle"></div>
				</div>
				<div class="formField">
					<div id="moderatorHeader" class="accessRightsHeader">
						<span class="deny">{lang}sls.acp.library.permissions.deny{/lang}</span>
						<span class="allow">{lang}sls.acp.library.permissions.allow{/lang}</span>
					</div>
					<div id="moderatorSettings" class="accessRights"></div>
				</div>
			</div>
		</div>
	</div>
	{/if}
	{if $this->user->getPermission('admin.library.canEditWarning')}
	<div class="border tabMenuContent hidden" id="warnings-content">
		<div class="container-1">
			<h3 class="subHeadline">{lang}sls.acp.library.warnings{/lang}</h3>
			<div class="formElement">

				<div class="formFieldLabel" id="warningMode">
					<label id="warningMode">{lang}sls.acp.library.warningMode{/lang}</label>
				</div>
				<div class="formField">
					<input type="checkbox" name="warningMode" value="1" {if $warningMode}checked="checked" {/if}/>
				</div>
			</div>
			{if $warnings|count}
					<div class="formGroup" >
						<div class="formGroupLabel">
							<label>{lang}sls.acp.library.warnings.title{/lang}</label>
						</div>
						<div class="formGroupField">
							<fieldset>
								<legend>{lang}sls.acp.library.warnings.title{/lang}</legend>
								<div class="formField">
									{foreach from=$warnings item=warning}
									<label id="warnings"><input type="checkbox" name="staticParameters[warningIDs][]" value="{$warning->warningID}" {if $warning->isMarked()}checked="checked" {/if} /> {$warning->name}</label>
									{/foreach}
								</div>
							</fieldset>
						</div>
					</div>
				{/if}

		</div>
	</div>
	{/if}
	{if $this->user->getPermission('admin.library.canEditCharacter')}
	<div class="border tabMenuContent hidden" id="characters-content">
		<div class="container-1">
			<h3 class="subHeadline">{lang}sls.acp.library.characters{/lang}</h3>
			<div class="formElement">

				<div class="formFieldLabel" id="characterMode">
					<label id="characterMode">{lang}sls.acp.library.characterMode{/lang}</label>
				</div>
				<div class="formField">
					<input type="checkbox" name="characterMode" value="1" {if $characterMode}checked="checked" {/if}/>
				</div>
			</div>
			{if $characters|count}
					<div class="formGroup" >
						<div class="formGroupLabel">
							<label>{lang}sls.acp.library.characters.title{/lang}</label>
						</div>
						<div class="formGroupField">
							<fieldset>
								<legend>{lang}sls.acp.library.characters.title{/lang}</legend>
								<div class="formField">
									{foreach from=$characters item=character}
									<label id="characters"><input type="checkbox" name="staticParameters[characterIDs][]" value="{$character->characterID}" {if $character->isMarked()}checked="checked" {/if} /> {$character->name}</label>
									{/foreach}
								</div>
							</fieldset>
						</div>
					</div>
				{/if}

		</div>
	</div>
	{/if}
	{if $this->user->getPermission('admin.library.canEditClassification')}
	<div class="border tabMenuContent hidden" id="classifications-content">
		<div class="container-1">
			<h3 class="subHeadline">{lang}sls.acp.library.classifications{/lang}</h3>
			<div class="formElement">

				<div class="formFieldLabel" id="classificationMode">
					<label id="classificationMode">{lang}sls.acp.library.classificationMode{/lang}</label>
				</div>
				<div class="formField">
					<input type="checkbox" name="classificationMode" value="1" {if $classificationMode}checked="checked" {/if}/>
				</div>
			</div>
			{if $classifications|count}
					<div class="formGroup" >
						<div class="formGroupLabel">
							<label>{lang}sls.acp.library.classifications.title{/lang}</label>
						</div>
						<div class="formGroupField">
							<fieldset>
								<legend>{lang}sls.acp.library.classifications.title{/lang}</legend>
								<div class="formField">
									{foreach from=$classifications item=classification}
									<label id="classifications"><input type="checkbox" name="staticParameters[classificationIDs][]" value="{$classification->classificationID}" {if $classification->isMarked()}checked="checked" {/if} /> {$classification->name}</label>
									{/foreach}
								</div>
							</fieldset>
						</div>
					</div>
				{/if}

		</div>
	</div>
	{/if}
	{if $this->user->getPermission('admin.library.canEditGenre')}
	<div class="border tabMenuContent hidden" id="genres-content">
		<div class="container-1">
			<h3 class="subHeadline">{lang}sls.acp.library.genres{/lang}</h3>
			<div class="formElement">

				<div class="formFieldLabel" id="genreMode">
					<label id="genreMode">{lang}sls.acp.library.genreMode{/lang}</label>
				</div>
				<div class="formField">
					<input type="checkbox" name="genreMode" value="1" {if $genreMode}checked="checked" {/if}/>
				</div>
			</div>
			{if $genres|count}
					<div class="formGroup" >
						<div class="formGroupLabel">
							<label>{lang}sls.acp.library.genres.title{/lang}</label>
						</div>
						<div class="formGroupField">
							<fieldset>
								<legend>{lang}sls.acp.library.genres.title{/lang}</legend>
								<div class="formField">
									{foreach from=$genres item=genre}
									<label id="genres"><input type="checkbox" name="staticParameters[genreIDs][]" value="{$genre->genreID}" {if $genre->isMarked()}checked="checked" {/if} /> {$genre->name}</label>
									{/foreach}
								</div>
							</fieldset>
						</div>
					</div>
				{/if}

		</div>
	</div>
	{/if}
	{if $additionalTabContents|isset}{@$additionalTabContents}{/if}

	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		{if $libraryID|isset}<input type="hidden" name="libraryID" value="{@$libraryID}" />{/if}
		<input type="hidden" id="activeTabMenuItem" name="activeTabMenuItem" value="{$activeTabMenuItem}" />
	</div>
</form>

{include file='footer'}
