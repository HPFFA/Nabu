{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ItemListEditor.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function init() {
		{if $libraries|count > 0 && $libraries|count < 100 && $this->user->getPermission('admin.library.canEditLibrary')}
			new ItemListEditor('libraryList', { itemTitleEdit: true, itemTitleEditURL: 'index.php?action=LibraryRename&libraryID=', tree: true, treeTag: 'ol' });
		{/if}
	}
	
	// when the dom is fully loaded, execute these scripts
	document.observe("dom:loaded", init);	
	
	function openCategory(libraryID) {
		var element = $('parentItem_' + libraryID);
		var close = 0;
		if (element.visible()) {
			// close list
			Effect.BlindUp(element, { duration: 0.2 });
			close = 1;
			var image = $('category' + libraryID + 'Image');
			if (image) {
				image.src = image.src.replace(/minus/, 'plus');
			}
		}
		else {
			// open list
			Effect.BlindDown(element, { duration: 0.2 });
			var image = $('category' + libraryID + 'Image');
			if (image) {
				image.src = image.src.replace(/plus/, 'minus');
			}
		}
		
		// save status
		var ajaxRequest = new AjaxRequest();
		ajaxRequest.openPost('index.php?action=LibraryCategoryClose' + SID_ARG_2ND, 'libraryID=' + encodeURIComponent(libraryID) + '&close=' + encodeURIComponent(close));
	}
	
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_SLS_DIR}icon/libraryL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}sls.acp.library.list{/lang}</h2>
	</div>
</div>

{if $deletedLibraryID}
	<p class="success">{lang}sls.acp.library.delete.success{/lang}</p>	
{/if}

{if $successfulSorting}
	<p class="success">{lang}sls.acp.library.sort.success{/lang}</p>	
{/if}

{if $this->user->getPermission('admin.library.canAddLibrary')}
	<div class="contentHeader">
		<div class="largeButtons">
			<ul><li><a href="index.php?form=LibraryAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.library.add{/lang}"><img src="{@RELATIVE_SLS_DIR}icon/libraryAddM.png" alt="" /> <span>{lang}sls.acp.library.add{/lang}</span></a></li></ul>
		</div>
	</div>
{/if}

{if $libraries|count > 0}
	{if $this->user->getPermission('admin.library.canEditLibrary')}
	<form method="post" action="index.php?action=LibrarySort">
	{/if}
		<div class="border content">
			<div class="container-1">
				<ol class="itemList" id="libraryList">
					{foreach from=$libraries item=child}
						{* define *}
						{assign var="library" value=$child.library}
						
						<li id="item_{@$library->libraryID}" class="deletable">
							<div class="buttons">
								{if $this->user->getPermission('admin.library.canEditLibrary') || $this->user->getPermission('admin.library.canEditPermissions') || $this->user->getPermission('admin.library.canEditModerators')}
									<a href="index.php?form=LibraryEdit&amp;libraryID={@$library->libraryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}sls.acp.library.edit{/lang}" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}sls.acp.library.edit{/lang}" />
								{/if}
								{if $this->user->getPermission('admin.library.canAddLibrary')}
									<a href="index.php?form=LibraryAdd&amp;parentID={@$library->libraryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.library.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/addS.png" alt="" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/addDisabledS.png" alt="" title="{lang}sls.acp.library.add{/lang}" />
								{/if}								
								{if $this->user->getPermission('admin.library.canDeleteLibrary')}
									<a href="index.php?action=LibraryDelete&amp;libraryID={@$library->libraryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.library.delete{/lang}" class="deleteButton"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" longdesc="{lang}sls.acp.library.delete.sure{/lang}"  /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}sls.acp.library.delete{/lang}" />
								{/if}
								
								{if $child.additionalButtons|isset}{@$child.additionalButtons}{/if}
							</div>
							
							<h3 class="itemListTitle{if $library->isCategory()} itemListCategory{/if}">
								{if $library->isCategory()}
									{if $child.open}
										<a onclick="openCategory({@$library->libraryID})"><img id="category{@$library->libraryID}Image" src="{@RELATIVE_WCF_DIR}icon/minusS.png" alt="" title="" /></a>
									{else}
										<a onclick="openCategory({@$library->libraryID})"><img id="category{@$library->libraryID}Image" src="{@RELATIVE_WCF_DIR}icon/plusS.png" alt="" title="" /></a>
									{/if}
								{else}
									<img src="{@RELATIVE_SLS_DIR}icon/{if $library->isLibrary()}library{else}libraryRedirect{/if}S.png" alt="" title="{lang}sls.acp.library.libraryType.{@$library->libraryType}{/lang}" />
								{/if}
								
								{if $this->user->getPermission('admin.library.canEditLibrary')}
									<select name="libraryListPositions[{@$library->libraryID}][{@$child.parentID}]">
										{section name='positions' loop=$child.maxPosition}
											<option value="{@$positions+1}"{if $positions+1 == $child.position} selected="selected"{/if}>{@$positions+1}</option>
										{/section}
									</select>
								{/if}
								
								ID-{@$library->libraryID} <a href="index.php?form=LibraryEdit&amp;libraryID={@$library->libraryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" class="title">{lang}{$library->title}{/lang}</a>
							</h3>
						
						{if $child.hasChildren}<ol id="parentItem_{@$library->libraryID}"{if !$child.open} style="display: none"{/if}>{else}<ol id="parentItem_{@$library->libraryID}"></ol></li>{/if}
						{if $child.openParents > 0}{@"</ol></li>"|str_repeat:$child.openParents}{/if}
					{/foreach}
				</ol>
			</div>
		</div>
	{if $this->user->getPermission('admin.library.canEditLibrary')}
		<div class="formSubmit">
			<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
			<input type="reset" accesskey="r" id="reset" value="{lang}wcf.global.button.reset{/lang}" />
			<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
	 		{@SID_INPUT_TAG}
	 	</div>
	</form>
	{/if}
{else}
	<div class="border content">
		<div class="container-1">
			<p>{lang}sls.acp.library.count.noEntries{/lang}</p>
		</div>
	</div>
{/if}

{include file='footer'}
