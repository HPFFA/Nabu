{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ItemListEditor.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function init() {
		{if $genres|count > 0 && $genres|count < 100 && $this->user->getPermission('admin.library.canEditGenre')}
			new ItemListEditor('genreList', { itemTitleEdit: true, itemTitleEditURL: 'index.php?action=GenreRename&GenreID=', tree: true, treeTag: 'ol' });
		{/if}
	}
	
	// when the dom is fully loaded, execute these scripts
	document.observe("dom:loaded", init);	
	
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_SLS_DIR}icon/genreL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}sls.acp.genre.list{/lang}</h2>
	</div>
</div>

{if $deletedGenreID}
	<p class="success">{lang}sls.acp.genre.delete.success{/lang}</p>	
{/if} 

{if $this->user->getPermission('admin.library.canAddGenre')}
	<div class="contentHeader">
		<div class="largeButtons">
			<ul><li><a href="index.php?form=GenreAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.genre.add{/lang}"><img src="{@RELATIVE_SLS_DIR}icon/genreAddM.png" alt="" /> <span>{lang}sls.acp.genre.add{/lang}</span></a></li></ul>
		</div>
	</div>
{/if}

{if $genres|count > 0}
	{if $this->user->getPermission('admin.library.canEditGenre')}
	<form method="post" action="index.php?action=GenreSort">
	{/if}
		<div class="border content">
			<div class="container-1">
				<ol class="itemList" id="genreList">
					{foreach from=$genres item=child}
						{* define *}
						{assign var="genre" value=$child.genre}
						
						<li id="item_{@$genre->genreID}" class="deletable">
							<div class="buttons">
								{if $this->user->getPermission('admin.library.canEditGenre')}
									<a href="index.php?form=GenreEdit&amp;genreID={@$genre->genreID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}sls.acp.genre.edit{/lang}" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}sls.acp.genre.edit{/lang}" />
								{/if}
								{if $this->user->getPermission('admin.library.canAddGenre')}
									<a href="index.php?form=GenreAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.genre.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/addS.png" alt="" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/addDisabledS.png" alt="" title="{lang}sls.acp.genre.add{/lang}" />
								{/if}								
								{if $this->user->getPermission('admin.library.canDeleteGenre')}
									<a href="index.php?action=GenreDelete&amp;genreID={@$genre->genreID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.genre.delete{/lang}" class="deleteButton"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" longdesc="{lang}sls.acp.genre.delete.sure{/lang}"  /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}sls.acp.genre.delete{/lang}" />
								{/if}
								
								{if $child.additionalButtons|isset}{@$child.additionalButtons}{/if}
							</div>
							
							<h3 class="itemListTitle">
									<img src="{@RELATIVE_SLS_DIR}icon/genreS.png" alt="" title="{lang}sls.acp.genre.title{/lang}" />
							
								{if $this->user->getPermission('admin.library.canEditGenre')}
									<select name="genreListPositions[{@$genre->genreID}]">
										{section name='positions' loop=$child.maxPosition}
											<option value="{@$positions+1}"{if $positions+1 == $child.position} selected="selected"{/if}>{@$positions+1}</option>
										{/section}
									</select>
								{/if}
								
								ID-{@$genre->genreID} <a href="index.php?form=GenreEdit&amp;genreID={@$genre->genreID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" class="title">{lang}{$genre->title}{/lang}</a>
							</h3>
						
					{/foreach}
				</ol>
			</div>
		</div>
	{if $this->user->getPermission('admin.library.canEditGenre')}
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
