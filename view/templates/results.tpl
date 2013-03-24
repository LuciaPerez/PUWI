<div id="content">

		<div id="projectName">
			<p>{$smarty.get.projectName}</p>
		</div>

		<div class="totalTests box">
			{if  ($smarty.get.totalTests) == 0}
				<p>No tests executed!</p>
			{else}
				<p>{$smarty.get.totalTests} test passing 
				   <button type="button" id="runAllTests" >Run All Tests</button>
				   <button type="button" id="hideTestsOK">Hide/Show Passed Tests</button>
				</p>
			{/if}
			
		</div>
