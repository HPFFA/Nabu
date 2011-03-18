{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ItemListEditor.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function init() {
		{if $classifications|count > 0 && $classifications|count < 100 && $this->user->getPermission('admin.library.canEditClassification')}
		new ItemListEditor('classificationList', { itemTitleEdit: true, itemTitleEditURL: 'index.php?action=ClassificationRename&ClassificationID=', tree: true, treeTag: 'ol' });
		{/if}
	}
	
	// when the dom is fully loaded, execute these scripts
	document.observe("dom:loaded", init);
	
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_SLS_DIR}icon/classificationL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}sls.acp.classification.list{/lang}</h2>
	</div>
</div>

{if $deletedClassificationID}
<p class="success">{lang}sls.acp.classification.delete.success{/lang}</p>	
{/if} 

{if $this->user->getPermission('admin.library.canAddClassification')}
<div class="contentHeader">
	<div class="largeButtons">
		<ul><li><a href="index.php?form=ClassificationAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.classification.add{/lang}"><img src="{@RELATIVE_SLS_DIR}icon/classificationAddM.png" alt="" /> <span>{lang}sls.acp.classification.add{/lang}</span></a></li></ul>
	</div>
</div>
{/if}

{if $classifications|count > 0}
	{if $this->user->getPermission('admin.library.canEditClassification')}
<form method="post" action="index.php?action=ClassificationSort">
	{/if}
	<div class="border content">
		<div class="container-1">
			<ol class="itemList" id="classificationList">
					{foreach from=$classifications item=classification}
						{* define *}
						

				<li id="item_{@$classification->classificationID}" class="deletable">
					<div class="buttons">
								{if $this->user->getPermission('admin.library.canEditClassification')}
						<a href="index.php?form=ClassificationEdit&amp;classificationID={@$classification->classificationID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}sls.acp.classification.edit{/lang}" /></a>
								{else}
						<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}sls.acp.classification.edit{/lang}" />
								{/if}
								{if $this->user->getPermission('admin.library.canAddClassification')}
						<a href="index.php?form=ClassificationAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.classification.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/addS.png" alt="" /></a>
								{else}
						<img src="{@RELATIVE_WCF_DIR}icon/addDisabledS.png" alt="" title="{lang}sls.acp.classification.add{/lang}" />
								{/if}								
								{if $this->user->getPermission('admin.library.canDeleteClassification')}
						<a href="index.php?action=ClassificationDelete&amp;classificationID={@$classification->classificationID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.classification.delete{/lang}" class="deleteButton"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" longdesc="{lang}sls.acp.classification.delete.sure{/lang}"  /></a>
								{else}
						<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}sls.acp.classification.delete{/lang}" />
								{/if}

								
					</div>

					<h3 class="itemListTitle">
						<img src="{@RELATIVE_SLS_DIR}icon/classificationS.png" alt="" title="{lang}sls.acp.classification.title{/lang}" />

								

								ID-{@$classification->classificationID} <a href="index.php?form=ClassificationEdit&amp;classificationID={@$classification->classificationID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" class="title">{lang}{$classification->name}{/lang}</a>
					</h3>

					{/foreach}
			</ol>
		</div>
	</div>
	{if $this->user->getPermission('admin.library.canEditClassification')}
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
