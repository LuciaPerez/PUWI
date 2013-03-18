{if ($group) != ''}
	<script type="text/javascript">
	{literal}
		  createDiv('{/literal}{$group}{literal}','{/literal}{$classGroup}{literal}', 'content', 'groupName{/literal}{$count}{literal}');
	{/literal}
	</script>
	{assign var="countGroup" value=$count} 
{/if}

{if ($createFolderDiv) == 'yes'}
	<script type="text/javascript">
	{literal}
		  createDiv('{/literal}{$folder}{literal}','{/literal}{$classFolder}{literal}','groupName{/literal}{$countGroup}{literal}', 'folderName{/literal}{$count}{literal}');
	{/literal}
	</script>
	{assign var="countFolder" value=$count}
{/if}



{if ($createClassNameDiv) == 'yes'}
	<script type="text/javascript">
	{literal}
		  createDiv('{/literal}{$class}{literal}','{/literal}{$className}{literal}','folderName{/literal}{$countFolder}{literal}', 'fileName{/literal}{$count}{literal}');
	{/literal}
	</script>
	 {assign var="countClass" value=$count} 
{/if}

<script type="text/javascript">
{literal}
	  createDiv('{/literal}{$test}{literal}','{/literal}{$classNameTest}{literal}','fileName{/literal}{$countClass}{literal}');
{/literal}
</script>



