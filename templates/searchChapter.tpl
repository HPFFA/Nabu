<div class="formElement">
	<div class="formFieldLabel">
		<label for="searchLibraries">{lang}sls.search.libraries{/lang}</label>
	</div>
	<div class="formField">
		<select id="searchLibraries" name="libraryIDs[]" multiple="multiple" size="10">
			<option value="*"{if $selectAllLibraries} selected="selected"{/if}>{lang}sls.search.libraries.all{/lang}</option>
			<option value="-">--------------------</option>
			{htmloptions options=$libraryOptions selected=$libraryIDs disableEncoding=true}
		</select>
		{*<input type="hidden" name="storyID" value="{@$storyID}" />*}
	</div>
	<div class="formFieldDesc">
		<p>{lang}wcf.global.multiSelect{/lang}</p>
	</div>
	
</div>
