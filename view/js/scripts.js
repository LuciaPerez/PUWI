$(document).on('ready',function(){
	
	var countDivs = 0;
	var countFolder = 0;
	var countClass = 0;
	var showedClass = '';
	var showedFolder = '';
	
	var count = 0;
	var idTest = "";
	createDiv = function (contentDiv,className,divParent,divName) { 
		var div = document.createElement('div');
		div.id = divName;
		div.className  = className;
		var content = document.getElementById(divParent);
		div.innerHTML = '<p>'+contentDiv+'</p>';
		content.appendChild(div);

	}
	createDivFailedTest = function (contentDiv,className,divParent,divName,file,line,message,code) { 
		var idButton = "showcode"+count;
		idTest = "idTest"+count;
		var div = document.createElement('div');
		div.id = divName;
		div.className  = className;
		var content = document.getElementById(divParent);

		div.innerHTML = '<p class="nameFT left bold">'+contentDiv+'</p>'
				+'<p class="red textRight bold">+'+line+'</p>'
				+'<p class="fileFT">'+file+'</p>'
				+'<p class="italic">'+message+'<input type="image" src="images/bullet_arrow_down1.png" title="Show code" id='+idButton+' class="classButton" data-idtest='+"#"+idTest+' data-file='+file+' data-line='+line+' data-test='+contentDiv+'><button type="button" class="classButton" >Trace</button></p>';
		content.appendChild(div);

		createDiv("","testInfo totalTests box",divName,idTest);
		count = count + 1;
		
	}
	
	runFirstTime = function (){
		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:'rerun'},
				
			success: function(request){	
				
				showResults(request);
			},
			error: function(request){
				$('#title').html('request: '+request);
				alert("falla");
			}
		});
	}
	
	runAllTests = function(){
		$("#content").remove();
		runFirstTime();
	}


	showResults = function (request){
		passed = request["passed"];
		failures = request["failures"];
		errors = request["errors"];
		skipped = request["skipped"];
		incomplete = request["incomplete"];
		groups = request["groups"];
		folders = request["folders"];
		info = request["infoFailedTests"];
	
		//createDiv(contentDiv,className,divParent,divName)
		$('#projectName p').html(request["projectName"]);
		if (request["totalTests"] == 0){
			$('.totalTests p').html("<p>No tests executed!</p>");
		}else{
			$('.totalTests p').append(request["totalTests"]+' test passing'
									+'<button type="button" id="runAllTests">Run All Tests</button>'
									+'<button type="button" id="hideTestsOK" class= "button">Hide/Show Passed Tests</button>');
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
			    	  var selector = "#"+'folderName'+countFolder+" > p";
			    	  var idButton = "idButton"+countFolder;
			    	  $(selector).append('<button type="button" id='+idButton+' class= "classButton">Run folder</button>');
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
	}
	
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
	
	$(".totalTests").on('click','#runAllTests', function() {
		alert("Ejecutar todos...");
		//runAllTests();
	});
	
	$(".grey").on('click',"#idButton0", function() {
		alert("foldeer");
	});
	

		$(".totalTests").on('click','#hideTestsOK', function() {
			$(".testOK").slideToggle(); 
		});

	
	$( "p.italic" ).on('click',"#showcode"+count, function(){
		
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
	

	runFirstTime();
	
	
	
});

	












	




