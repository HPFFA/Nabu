{include file='header'}{assign var=dataPermission value="admin.library.canEditGenre"}

{if $action == 'add'}{assign var=dataPermission value="admin.library.canAddGenre"}
{else}{assign var=dataPermission value="admin.library.canEditGenre"}
{/if}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>

<div class="mainHeadline">
	<img src="{@RELATIVE_SLS_DIR}icon/genre{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}sls.acp.genre.{@$action}{/lang}</h2>
		{if $genreID|isset}<p>{lang}{$genre->genre}{/lang}</p>{/if}
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{if $action == 'add'}{lang}sls.acp.genre.add.success{/lang}{else}{lang}sls.acp.genre.edit.success{/lang}{/if}</p>	
{/if}

<div class="contentHeader">
	
	<div class="largeButtons">
		<ul><li><a href="index.php?page=GenreList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}sls.acp.menu.link.content.genre.view{/lang}"><img src="{@RELATIVE_SLS_DIR}icon/genreM.png" alt="" /> <span>{lang}sls.acp.menu.link.content.genre.view{/lang}</span></a></li></ul>
	</div>
</div>

<form method="post" action="index.php?form=Genre{@$action|ucfirst}" id="genreAddForm">
	{if $genreID|isset && $genreQuickJumpOptions|count > 1}
		<fieldset>
			<legend>{lang}sls.acp.genre.edit{/lang}</legend>
			<div class="formElement">
				<div class="formFieldLabel">
					<label for="genreChange">{lang}sls.acp.genre.edit{/lang}</label>
				</div>
				<div class="formField">
					<select id="genreGenre&amp;libraryID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
						{htmloptions options=$genreQuickJumpOptions selected=$libraryID disableEncoding=true}
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
		<input type="submit" accesskey="s" value="{if $genreID|isset}{lang}.sls.acp.savegenre{/lang}{else}{lang}sls.acp.addgenre.submit{/lang}{/if}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		{if $genreID|isset}<input type="hidden" name="genreID" value="{@$genreID}" />{/if}
 	</div>
</form>

{include file='footer'}
