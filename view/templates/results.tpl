<div id="content">

		<div id="projectName">
			<p>{$smarty.get.projectName}</p>
		</div>

		<div id="totalTests" class="box">
			{if  ($smarty.get.totalTests) == 0}
				<p>No tests executed!</p>
			{else}
				<p>{$smarty.get.totalTests} test passing 
				   <button type="button" onclick="prueba()">Run All Tests</button>
				   <button type="button">Hide Passed Tests</button>
				</p>
			{/if}
			
		</div>
