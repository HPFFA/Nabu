<form method="get" action="index.php" class="quickJump">
	<div>
		<input type="hidden" name="page" value="Library" />
		<select name="libraryID" onchange="if (this.options[this.selectedIndex].value != 0) this.form.submit()">
			<option value="0">{lang}sls.library.quickJump.title{/lang}</option>
			<option value="0">-----------------------</option>
			{htmloptions options=$libraryQuickJumpOptions selected=$library->libraryID disableEncoding=true}
		</select>
		
		{@SID_INPUT_TAG}
		<input type="image" class="inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
	</div>
</form>
