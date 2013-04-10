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

	createDiv("","testInfo totalTests box",divName,idTest);
	count = count + 1;
	
}

function createDiv(contentDiv,className,divParent,divName) { 

	var div = document.createElement('div');
	div.id = divName;
	div.className  = className;
	var content = document.getElementById(divParent);
	div.innerHTML = '<p >'+contentDiv+"</p>";
	content.appendChild(div);
	alert(contentDiv);
}


$(document).ready(function(){

	$("#hideTestsOK").click(function(){
		$(".testOK").slideToggle();

	});
	
	
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
				passed = request["passed"];
				failures = request["failures"];
				errors = request["errors"];
				skipped = request["skipped"];
				incomplete = request["incomplete"];
				info = request["infoFailedTests"];
				
				$('#title').html('passed: '+passed+' failures: '+failures+' skipped: '+skipped+ ' infoFailedTests: '+info[0]['file']);
				
				for (i=0; i<passed.length; i++){
					var id = "#"+ passed[i].replace(/:/g,'\\:'); 
					if($(id).hasClass('testFailed')){
						$(id).removeClass('testFailed').addClass('testOK');
						id = id+' p.fileFT,'+id+' p.red,'+id+' p.italic';
						$(id).empty();
					}else{
						$(id).removeClass('testIncomplete').addClass('testOK');
					}
				}
				
				for (i=0; i<skipped.length; i++){
					var id = "#"+ skipped[i].replace(/:/g,'\\:'); 
					if($(id).hasClass('testFailed')){
						$(id).removeClass('testFailed').addClass('testIncomplete');
						id = id+' p.fileFT,'+id+' p.red,'+id+' p.italic';
						$(id).empty();
					}else{
						$(id).removeClass('testOK').addClass('testIncomplete');
					}
				}
				
				for (i=0; i<failures.length; i++){
					idTest = "idTest"+count;
					var pr ="probando variable";
					var file = info[i]["file"];
					var line = info[i]["line"];
					var id = "#"+ failures[i].replace(/:/g,'\\:'); 
			
					var p = $(id).hasClass('testOK') ? $(id).removeClass('testOK') : 
						    $(id).hasClass('testIncomplete') ? $(id).removeClass('testIncomplete') : 'failed test';
					
					if ($(id).hasClass('testFailed')){ 
						$(id).removeClass('testFailed');
						remove_content = id+' p.fileFT,'+id+' p.red,'+id+' p.italic';
						$(remove_content).empty();
						$("div.testInfo").remove();
					}
			
					$(id).addClass('testFailed').
					append('<p class="red textRight bold">'+line+'</p>').
					append('<p class="fileFT">'+idTest+'</p>').
					append('<p class="italic">'+info[i]["message"]+'<input type="image" src="images/bullet_arrow_down1.png" title="Show code2" class="classButton" data-idtest='+"#"+idTest+' data-file='+file+' data-line='+line+' data-test='+"test_setUpWorks"+'></p>');
					createDiv(idTest,"testInfo totalTests box",failures[i],idTest);
					count = count + 1;

				}
				
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
				$(idTest).html('<p>'+idTest+request['code']+'</p>');
			},
		
			error: function(request){
				alert("an error ocurred in ajax request");
			}
		});
		
	});

	
});

	












	




