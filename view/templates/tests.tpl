{if ($group) != ''}
	<script type="text/javascript">
	{literal}
		  createDiv('{/literal}{$group}{literal}','groupName', 'content', 'groupName');
		  createDiv('','groupContent', 'content', 'groupName{/literal}{$count}{literal}');
	{/literal}
	</script>
	{assign var="countGroup" value=$count} 
{/if}

{if ($createFolderDiv) == 'yes'}
	<script type="text/javascript">
	{literal}
		  createDiv('{/literal}{$folder}{literal}','grey','groupName{/literal}{$countGroup}{literal}', 
		  			'folderName{/literal}{$count}{literal}');
	{/literal}
	</script>
	{assign var="countFolder" value=$count}
{/if}



{if ($createClassNameDiv) == 'yes'}
	<script type="text/javascript">
	{literal}
		  createDiv('{/literal}{$class}{literal}','black','folderName{/literal}{$countFolder}{literal}', 
		  			'fileName{/literal}{$count}{literal}');
	{/literal}
	</script>
	
	 {assign var="countClass" value=$count} 
{/if}


{if ($classNameTest) == 'testFailed box'}
	<script type="text/javascript">
	{literal}
		  createDivFailedTest('{/literal}{$test}{literal}','{/literal}{$classNameTest}{literal}','fileName{/literal}{$countClass}{literal}',
		 					 'testName{/literal}{$count}{literal}','{/literal}{$file}{literal}','{/literal}{$line}{literal}',
		 					 '{/literal}{$message}{literal}','{/literal}{$code}{literal}');
	{/literal}
	</script>
{else}
	<script type="text/javascript">
	{literal}
		  createDiv('{/literal}{$test}{literal}','{/literal}{$classNameTest}{literal}','fileName{/literal}{$countClass}{literal}');
	{/literal}
	</script>
{/if}

