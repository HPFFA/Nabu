{include file='header'}{assign var=dataPermission value="admin.library.canEditWarning"}

{if $action == 'add'}{assign var=dataPermission value="admin.library.canAddWarning"}
{else}{assign var=dataPermission value="admin.library.canEditWarning"}
{/if}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>

<div class="mainHeadline">
	<img src="{@RELATIVE_SLS_DIR}icon/warning{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}sls.acp.warning.{@$action}{/lang}</h2>
		{if $warningID|isset}<p>{lang}{$warning->warning}{/lang}</p>{/if}
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{if $action == 'add'}{lang}sls.acp.warning.add.success{/lang}{else}{lang}sls.acp.warning.edit.success{/lang}{/if}</p>	
{/if}

<div class="contentHeader">
	
	<div class="largeButtons">
		<ul><li><a href="index.php?page=WarningList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.menu.link.content.warning.view{/lang}"><img src="{@RELATIVE_SLS_DIR}icon/warningM.png" alt="" /> <span>{lang}sls.acp.menu.link.content.warning.view{/lang}</span></a></li></ul>
	</div>
</div>

<form method="post" action="index.php?form=Warning{@$action|ucfirst}" id="warningAddForm">
	{if $warningID|isset && $warningQuickJumpOptions|count > 1}
		<fieldset>
			<legend>{lang}sls.acp.warning.edit{/lang}</legend>
			<div class="formElement">
				<div class="formFieldLabel">
					<label for="warningChange">{lang}sls.acp.warning.edit{/lang}</label>
				</div>
				<div class="formField">
					<select id="warningWarning&amp;libraryID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
						{htmloptions options=$warningQuickJumpOptions selected=$libraryID disableEncoding=true}
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
		<input type="submit" accesskey="s" value="{if $warningID|isset}{lang}.sls.acp.savewarning{/lang}{else}{lang}sls.acp.addwarning.submit{/lang}{/if}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		{if $warningID|isset}<input type="hidden" name="warningID" value="{@$warningID}" />{/if}
 	</div>
</form>

{include file='footer'}
