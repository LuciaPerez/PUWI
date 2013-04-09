var count = 0;
var idTest = "";
function createDivFailedTest(contentDiv,className,divParent,divName,file,line,message,code) { 

	idTest = "idTest"+count;
	var div = document.createElement('div');
	div.id = divName;
	div.className  = className;
	var content = document.getElementById(divParent);

	div.innerHTML = '<p class="nameFT left bold">'+contentDiv+'</p>'
			+'<p class="red textRight bold">+'+line+'</p>'
			+'<p class="fileFT">'+file+'</p>'
			+'<p class="italic">'+message+'<input type="image" src="images/bullet_arrow_down1.png" title="Show code" class="classButton" data-idtest='+"#"+idTest+' data-file='+file+' data-line='+line+' data-test='+contentDiv+'></p>';
	
	content.appendChild(div);

	createDiv(code,"testInfo totalTests box",divName,idTest);
	count = count + 1;
	
}

function createDiv(contentDiv,className,divParent,divName) { 

	var div = document.createElement('div');
	div.id = divName;
	div.className  = className;
	var content = document.getElementById(divParent);
	div.innerHTML = '<p >'+contentDiv+"</p>";
	content.appendChild(div);
}


$(document).ready(function(){

	$("#hideTestsOK").click(function(){
		$(".testOK").slideToggle();

	});
	
	
	var myClasses=new Array('testOK', 'testIncomplete', 'testFailed');
	
	updateChanges = function(arrayResults,classToChange,del){
		for (i=0; i<arrayResults.length; i++){
			var id = "#"+ arrayResults[i].replace(/:/g,'\\:'); 
			$(id).addClass(classToChange);
			
			var posBorrar=myClasses.indexOf(classToChange);
			myClasses.splice(posBorrar, 1);

			//for (i=0; i<myClasses.length; i++){
				$(id).removeClass(del);
			//}
		}
	}
	
	$("#runAllTests").click(function(){
		runAllTests();
	});

	runAllTests = function (){
		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:'rerun'},
				
			success: function(request){
				$passed = request["passed"];
				$failures = request["failures"];
				$errors = request["errors"];
				$skipped = request["skipped"];
				$incomplete = request["incomplete"];
				$info = request["infoFailedTests"];
				
				$('#title').html('passed: '+$passed+' failures: '+$failures+' skipped: '+$skipped+ ' infoFailedTests: '+$info[0]['data']);
				
				for (i=0; i<$passed.length; i++){
					var id = "#"+ $passed[i].replace(/:/g,'\\:'); 
					if($(id).hasClass('testFailed')){
						$(id).removeClass('testFailed').addClass('testOK');
						id = id+' p.fileFT,'+id+' p.red,'+id+' p.italic';
						$(id).empty();
					}else{
						$(id).removeClass('testIncomplete').addClass('testOK');
					}
				}
				
				for (i=0; i<$skipped.length; i++){
					var id = "#"+ $skipped[i].replace(/:/g,'\\:'); 
					if($(id).hasClass('testFailed')){
						$(id).removeClass('testFailed').addClass('testIncomplete');
						id = id+' p.fileFT,'+id+' p.red,'+id+' p.italic';
						$(id).empty();
					}else{
						$(id).removeClass('testOK').addClass('testIncomplete');
					}
				}
				
				for (i=0; i<$failures.length; i++){
					idTest = "idTest"+count;
					var pr ="MENSAJE PRUEBA";
					var id = "#"+ $failures[i].replace(/:/g,'\\:'); 
					if($(id).hasClass('testFailed')){
						
					}else{
						$(id).removeClass('testOK').
						addClass('testFailed').
						append('<p class="red bold"> holaaa</p><p class="fileFT">file/file</p><p class="italic">MENSAJE<input type="image" src="images/bullet_arrow_down1.png" title="Show code" class="classButton" data-idtest='+"#"+idTest+'></p>');
						createDiv("codigo","testInfo totalTests box",$failures[i],idTest);
						count = count + 1;
					}
					
				}
				/*updateChanges($passed,'testOK','testIncomplete');
				updateChanges($skipped,'testIncomplete','testOK');*/
				
			},
			
			error: function(request){
				$('#title').html('request: '+request);
				alert("falla");
			}
		});
	}
	
	
	$( ".classButton" ).click(function(){
		var idTest = $(this).data('idtest');
		var file = $(this).data('file');
		var line = $(this).data('line');
		var testName = $(this).data('test');
		
		$(idTest).slideToggle();
		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:'displayCode',file:file,line:line,testName:testName},
			success:function(request){
				$(idTest).html('<p>'+request['code']+'</p>');
			},
		
			error: function(request){
				alert("an error ocurred in ajax request");
			}
		});
		
	});

	
});

	












	




