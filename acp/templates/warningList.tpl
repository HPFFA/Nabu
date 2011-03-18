{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ItemListEditor.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function init() {
		{if $warnings|count > 0 && $warnings|count < 100 && $this->user->getPermission('admin.library.canEditWarning')}
			new ItemListEditor('warningList', { itemTitleEdit: true, itemTitleEditURL: 'index.php?action=WarningRename&WarningID=', tree: true, treeTag: 'ol' });
		{/if}
	}
	
	// when the dom is fully loaded, execute these scripts
	document.observe("dom:loaded", init);	
	
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_SLS_DIR}icon/warningL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}sls.acp.warning.list{/lang}</h2>
	</div>
</div>

{if $deletedWarningID}
	<p class="success">{lang}sls.acp.warning.delete.success{/lang}</p>	
{/if} 

{if $this->user->getPermission('admin.library.canAddWarning')}
	<div class="contentHeader">
		<div class="largeButtons">
			<ul><li><a href="index.php?form=WarningAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.warning.add{/lang}"><img src="{@RELATIVE_SLS_DIR}icon/warningAddM.png" alt="" /> <span>{lang}sls.acp.warning.add{/lang}</span></a></li></ul>
		</div>
	</div>
{/if}

{if $warnings|count > 0}
	{if $this->user->getPermission('admin.library.canEditWarning')}
	<form method="post" action="index.php?action=WarningSort">
	{/if}
		<div class="border content">
			<div class="container-1">
				<ol class="itemList" id="warningList">
					{foreach from=$warnings item=warning}
						{* define *}
						
						<li id="item_{@$warning->warningID}" class="deletable">
							<div class="buttons">
								{if $this->user->getPermission('admin.library.canEditWarning')}
									<a href="index.php?form=WarningEdit&amp;warningID={@$warning->warningID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}sls.acp.warning.edit{/lang}" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}sls.acp.warning.edit{/lang}" />
								{/if}
								{if $this->user->getPermission('admin.library.canAddWarning')}
									<a href="index.php?form=WarningAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.warning.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/addS.png" alt="" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/addDisabledS.png" alt="" title="{lang}sls.acp.warning.add{/lang}" />
								{/if}								
								{if $this->user->getPermission('admin.library.canDeleteWarning')}
									<a href="index.php?action=WarningDelete&amp;warningID={@$warning->warningID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.warning.delete{/lang}" class="deleteButton"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" longdesc="{lang}sls.acp.warning.delete.sure{/lang}"  /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}sls.acp.warning.delete{/lang}" />
								{/if}
								
								
							</div>
							
							<h3 class="itemListTitle">
									<img src="{@RELATIVE_SLS_DIR}icon/warningS.png" alt="" title="{lang}sls.acp.warning.title{/lang}" />
							
								
								
								ID-{@$warning->warningID} <a href="index.php?form=WarningEdit&amp;warningID={@$warning->warningID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" class="title">{lang}{$warning->name}{/lang}</a>
							</h3>
						
					{/foreach}
				</ol>
			</div>
		</div>
	{if $this->user->getPermission('admin.library.canEditWarning')}
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
