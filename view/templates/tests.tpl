
<script type="text/javascript">
{literal}
	  createDiv('{/literal}{$group}{literal}','{/literal}{$className}{literal}');
{/literal}
</script>

{if ($createFolderDiv) == 'yes'}
	<script type="text/javascript">
	{literal}
		  createDiv('{/literal}{$folder}{literal}','{/literal}{$className}{literal}');
	{/literal}
	</script>
{/if}

{if ($createClassNameDiv) == 'yes'}
	<script type="text/javascript">
	{literal}
		  createDiv('{/literal}{$class}{literal}','{/literal}{$className}{literal}');
	{/literal}
	</script>
{/if}

<script type="text/javascript">
{literal}
	  createDiv('{/literal}{$test}{literal}','{/literal}{$classNameTest}{literal}');
{/literal}
</script>

<!--<button type="button" onclick="prueba()">run</button>-->

