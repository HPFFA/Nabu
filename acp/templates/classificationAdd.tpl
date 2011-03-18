{include file='header'}{assign var=dataPermission value="admin.library.canEditClassification"}

{if $action == 'add'}{assign var=dataPermission value="admin.library.canAddClassification"}
{else}{assign var=dataPermission value="admin.library.canEditClassification"}
{/if}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>

<div class="mainHeadline">
	<img src="{@RELATIVE_SLS_DIR}icon/classification{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}sls.acp.classification.{@$action}{/lang}</h2>
		{if $classificationID|isset}<p>{lang}{$classification->classification}{/lang}</p>{/if}
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{if $action == 'add'}{lang}sls.acp.classification.add.success{/lang}{else}{lang}sls.acp.classification.edit.success{/lang}{/if}</p>	
{/if}

<div class="contentHeader">
	
	<div class="largeButtons">
		<ul><li><a href="index.php?page=ClassificationList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.menu.link.content.classification.view{/lang}"><img src="{@RELATIVE_SLS_DIR}icon/classificationM.png" alt="" /> <span>{lang}sls.acp.menu.link.content.classification.view{/lang}</span></a></li></ul>
	</div>
</div>

<form method="post" action="index.php?form=Classification{@$action|ucfirst}" id="classificationAddForm">
	{if $classificationID|isset && $classificationQuickJumpOptions|count > 1}
		<fieldset>
			<legend>{lang}sls.acp.classification.edit{/lang}</legend>
			<div class="formElement">
				<div class="formFieldLabel">
					<label for="classificationChange">{lang}sls.acp.classification.edit{/lang}</label>
				</div>
				<div class="formField">
					<select id="classificationClassification&amp;libraryID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
						{htmloptions options=$classificationQuickJumpOptions selected=$libraryID disableEncoding=true}
					</select>
				</div>
			</div>
		</fieldset>
	{/if}
	

	
	{if $this->user->getPermission($dataPermission)}
		<div class="border content" id="data-content">
			<div class="container-1">
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
					
					{if $additionalGeneralFields|isset}{@$additionalGeneralFields}{/if}
				</fieldset>

				{if $additionalFields|isset}{@$additionalFields}{/if}
			</div>
		</div>
	{/if}
	
	
	
	{if $additionalTabContents|isset}{@$additionalTabContents}{/if}
	
	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{if $classificationID|isset}{lang}.sls.acp.saveclassification{/lang}{else}{lang}sls.acp.addclassification.submit{/lang}{/if}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		{if $classificationID|isset}<input type="hidden" name="classificationID" value="{@$classificationID}" />{/if}
 	</div>
</form>

{include file='footer'}
