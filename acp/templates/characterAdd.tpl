{include file='header'}{assign var=dataPermission value="admin.library.canEditCharacter"}

{if $action == 'add'}{assign var=dataPermission value="admin.library.canAddCharacter"}
{else}{assign var=dataPermission value="admin.library.canEditCharacter"}
{/if}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>

<div class="mainHeadline">
	<img src="{@RELATIVE_SLS_DIR}icon/character{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}sls.acp.character.{@$action}{/lang}</h2>
		{if $characterID|isset}<p>{lang}{$character->character}{/lang}</p>{/if}
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{if $action == 'add'}{lang}sls.acp.character.add.success{/lang}{else}{lang}sls.acp.character.edit.success{/lang}{/if}</p>	
{/if}

<div class="contentHeader">
	
	<div class="largeButtons">
		<ul><li><a href="index.php?page=CharacterList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.menu.link.content.character.view{/lang}"><img src="{@RELATIVE_SLS_DIR}icon/characterM.png" alt="" /> <span>{lang}sls.acp.menu.link.content.character.view{/lang}</span></a></li></ul>
	</div>
</div>

<form method="post" action="index.php?form=Character{@$action|ucfirst}" id="characterAddForm">
	{if $characterID|isset && $characterQuickJumpOptions|count > 1}
		<fieldset>
			<legend>{lang}sls.acp.character.edit{/lang}</legend>
			<div class="formElement">
				<div class="formFieldLabel">
					<label for="characterChange">{lang}sls.acp.character.edit{/lang}</label>
				</div>
				<div class="formField">
					<select id="characterCharacter&amp;libraryID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
						{htmloptions options=$characterQuickJumpOptions selected=$libraryID disableEncoding=true}
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
		<input type="submit" accesskey="s" value="{if $characterID|isset}{lang}.sls.acp.savecharacter{/lang}{else}{lang}sls.acp.addcharacter.submit{/lang}{/if}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		{if $characterID|isset}<input type="hidden" name="characterID" value="{@$characterID}" />{/if}
 	</div>
</form>

{include file='footer'}
