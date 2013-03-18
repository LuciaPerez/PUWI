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
		  createDiv('{/literal}{$folder}{literal}','classFolder','groupName{/literal}{$countGroup}{literal}', 'folderName{/literal}{$count}{literal}');
	{/literal}
	</script>
	{assign var="countFolder" value=$count}
{/if}



{if ($createClassNameDiv) == 'yes'}
	<script type="text/javascript">
	{literal}
		  createDiv('{/literal}{$class}{literal}','className','folderName{/literal}{$countFolder}{literal}', 'fileName{/literal}{$count}{literal}');
	{/literal}
	</script>
	 {assign var="countClass" value=$count} 
{/if}

<script type="text/javascript">
{literal}
	  createDiv('{/literal}{$test}{literal}','{/literal}{$classNameTest}{literal}','fileName{/literal}{$countClass}{literal}');
{/literal}
</script>

<!--<button type="button" onclick="prueba()">run</button>-->

