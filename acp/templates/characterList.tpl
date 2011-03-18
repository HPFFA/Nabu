{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ItemListEditor.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function init() {
		{if $characters|count > 0 && $characters|count < 100 && $this->user->getPermission('admin.library.canEditCharacter')}
			new ItemListEditor('characterList', { itemTitleEdit: true, itemTitleEditURL: 'index.php?action=CharacterRename&CharacterID=', tree: true, treeTag: 'ol' });
		{/if}
	}
	
	// when the dom is fully loaded, execute these scripts
	document.observe("dom:loaded", init);	
	
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_SLS_DIR}icon/characterL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}sls.acp.character.list{/lang}</h2>
	</div>
</div>

{if $deletedCharacterID}
	<p class="success">{lang}sls.acp.character.delete.success{/lang}</p>	
{/if} 

{if $this->user->getPermission('admin.library.canAddCharacter')}
	<div class="contentHeader">
		<div class="largeButtons">
			<ul><li><a href="index.php?form=CharacterAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.character.add{/lang}"><img src="{@RELATIVE_SLS_DIR}icon/characterAddM.png" alt="" /> <span>{lang}sls.acp.character.add{/lang}</span></a></li></ul>
		</div>
	</div>
{/if}

{if $characters|count > 0}
	{if $this->user->getPermission('admin.library.canEditCharacter')}
	<form method="post" action="index.php?action=CharacterSort">
	{/if}
		<div class="border content">
			<div class="container-1">
				<ol class="itemList" id="characterList">
					
					{foreach from=$characters item=character}
						{* define *}
												
						<li id="item_{@$character->characterID}" class="deletable">
							<div class="buttons">
								{if $this->user->getPermission('admin.library.canEditCharacter')}
									<a href="index.php?form=CharacterEdit&amp;characterID={@$character->characterID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}sls.acp.character.edit{/lang}" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}sls.acp.character.edit{/lang}" />
								{/if}
								{if $this->user->getPermission('admin.library.canAddCharacter')}
									<a href="index.php?form=CharacterAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.character.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/addS.png" alt="" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/addDisabledS.png" alt="" title="{lang}sls.acp.character.add{/lang}" />
								{/if}								
								{if $this->user->getPermission('admin.library.canDeleteCharacter')}
									<a href="index.php?action=CharacterDelete&amp;characterID={@$character->characterID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.character.delete{/lang}" class="deleteButton"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" longdesc="{lang}sls.acp.character.delete.sure{/lang}"  /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}sls.acp.character.delete{/lang}" />
								{/if}
								
							
							</div>
							
							<h3 class="itemListTitle">
									<img src="{@RELATIVE_SLS_DIR}icon/characterS.png" alt="" title="{lang}sls.acp.character.title{/lang}" />
							
								
								ID-{@$character->characterID} <a href="index.php?form=CharacterEdit&amp;characterID={@$character->characterID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" class="title">{lang}{$character->name}{/lang}</a>
							</h3>
						
					{/foreach}
				</ol>
			</div>
		</div>
	{if $this->user->getPermission('admin.library.canEditCharacter')}
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
