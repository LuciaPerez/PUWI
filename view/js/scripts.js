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
	//alert(contentDiv+" --divparent: "+divParent+" --divname "+divName);
	var div = document.createElement('div');
	div.id = divName;
	div.className  = className;
	var content = document.getElementById(divParent);
	div.innerHTML = '<p >'+contentDiv+"</p>";
	content.appendChild(div);

}


$(document).ready(function(){

	
	var countDivs = 0;
	var countFolder = 0;
	var countClass = 0;
	var showedClass = '';
	var showedFolder = '';

	
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
				var last_idTest = "idTest"+(count-1);
				alert (last_idTest);
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
					var new_codeDiv = 'no';
								
					if($(id).hasClass('testOK')){
						$(id).removeClass('testOK');
						new_codeDiv = 'yes';
					}else{
						if ($(id).hasClass('testIncomplete')){
							$(id).removeClass('testIncomplete');
							new_codeDiv = 'yes';
							
						}
					}
						   
					
					if ($(id).hasClass('testFailed')){ 
						$(id).removeClass('testFailed');
						remove_content = id+' p.fileFT,'+id+' p.red,'+id+' p.italic';
						$(remove_content).empty();
						$("div.testInfo").remove();

					}
			
					$(id).addClass('testFailed').
					append('<p class="red textRight bold">'+line+'</p>').
					append('<p class="fileFT">'+file+'</p>').
					append('<p class="italic">'+info[i]["message"]+'<input type="image" src="images/bullet_arrow_down1.png" title="Show code2" class="classButton" data-idtest='+"#"+idTest+' data-file='+file+' data-line='+line+' data-test='+"test_setUpWorks"+'></p>');
				
					if (new_codeDiv == 'yes'){
						createDiv(idTest,"testInfo totalTests box",failures[i],idTest);
					}
					
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
	
	runFirstTime = function (){
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
				groups = request["groups"];
				folders = request["folders"];
				info = request["infoFailedTests"];
			
				$('#projectName p').html(request["projectName"]);
				if (request["totalTests"] == 0){
					$('.totalTests p').html("<p>No tests executed!</p>");
				}else{
					$('.totalTests p').html('<p>'+request["totalTests"]+' test passing'
											+'<button type="button" id="runAllTests" >Run All Tests</button>'
											+'<button type="button" id="hideTestsOK">Hide/Show Passed Tests</button></p>');
				}

				var array_keys = new Array();
				var array_values = new Array();
				for (var group_name in groups) {
				    array_keys.push(group_name);
				    createDiv(group_name,"groupName","content","groupName");
				    var countGroup = countDivs;
				    createDiv('','groupContent', 'content', 'groupName'+countGroup);
				    		    
				    $.each(groups[group_name], function(key, value) {
				    	
					      var classNameTest = getClassNameTest(value, passed, incomplete, skipped, errors);
					      separated_values = value.split("::"); 
					      var className = separated_values[0];
					      var test = separated_values[1];
  
					      var folder = getFolder(folders,className);
					      if (!is_showedFolder(folder)){
					    	  countFolder = countDivs;
					    	  createDiv(folder,'grey','groupName'+countGroup,'folderName'+countFolder);
					    	  
					      }
					      
					      if (!is_showedClass(className)){
					    	  countClass = countDivs;
					    	  createDiv(className,'black','folderName'+countFolder, 'fileName'+countClass);
					      }
					      if (classNameTest == "testFailed box"){
					    	  var failedTest = getInfoFailedTests(value,info);
					    	  createDivFailedTest(test,classNameTest,'fileName'+countClass,className+'::'+test,failedTest['file'],failedTest['line'],
					    			  failedTest['message']);
					      }else{
					    	  createDiv(test,classNameTest,'fileName'+countClass,className+'::'+test);
					      }
					      countDivs++;
					});
				   
				}

			},
			error: function(request){
				$('#title').html('request: '+request);
				alert("falla");
			}
		});
	}

	$("#hideTestsOK").click(function(){
		$(".testOK").slideToggle(); 
	});
	
	getClassNameTest = function(value, passed, incomplete, skipped, errors){
		var classNameTest = "";
		if($.inArray(value, passed) > -1){
			classNameTest = "testOK box";
		}else{
			if(($.inArray(value,incomplete) > -1) || ($.inArray(value,skipped) > -1)){
				classNameTest = "testIncomplete box";
			}else{
				classNameTest = "testFailed box";
			}
		}
		return classNameTest;
	}
	
	getFolder = function(folders,className){
		var result = "";
		 $.each(folders, function(folder, tests) {
			 $.each(folders[folder], function(index, test) {
				 var regex = new RegExp (".*"+className+".*","gi");
				 if (test.match(regex)){
					 result = folder;
				 }
			     
			 });
		});
		return result;
	}
	
	is_showedClass = function(className){
		if(showedClass == className){ 
			return true; 
		} else { 
			showedClass = className;
			return false; 
		} 
	}
	
	is_showedFolder = function(folder){
		if(showedFolder == folder){ 
			return true; 
		} else { 
			showedFolder = folder;
			return false; 
		} 
	}
	
	getInfoFailedTests = function(testName,infoFailedTests){
		var result;
		$.each(infoFailedTests, function(key, value) {
		      if (value['testName'] == testName){
		    	  result = value;
		      }
		});
		return result;
	}
	
	runFirstTime();
	
});

	












	




