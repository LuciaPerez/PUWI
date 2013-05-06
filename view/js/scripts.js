
$(document).on('ready',function(){

	var countDivs = 0;
	var countFolder = 0;
	var countClass = 0;
	
	var count = 0;
	var idCode = "";
	var idTrace = "";
	
	getURLParams = function(){
		var pageURL = window.location.toString().split('?');
		var URLVariables = pageURL[1].split('&');
		
		var puwiParam = URLVariables[0].split('=');
		var projectParam = URLVariables[1].split('=');
		return [puwiParam[1], projectParam[1]];
		
	}

	createDiv = function (contentDiv,className,divParent,divName) {
		divParent="#"+divParent.replace(/:/g,'\\:');
		$('<div/>', {
		    id: divName,
		    class: className,
		    html: '<p class="nameNFT">'+contentDiv+'</p>'
		}).appendTo(divParent);
	}
	
	createDivFailedTest = function (contentDiv,className,divParent,divName,file,line,message,trace,code) { 
		idCode = "idCode"+count;		
		idTrace = "idTrace"+count;
		divParent="#"+divParent;
			$('<div/>', {
		    id: divName,
		    class: className,
		    html: '<p class="nameFT bold">'+contentDiv+'</p>'+'<p class="fileFT left">'+file+'</p>'+'<p class="red textRight bold">+'+line+'</p>'
		          +'<p class="italic">'+message
		          +'<input type="image" src="images/bullet_arrow_down1.png" title="Show code" class="code classButton" data-idcode='+"#"+idCode+' data-file='+file+' data-line='+line+' data-test='+contentDiv+'>'
		          +'<button type="button" class="trace classButton" data-idtrace='+"#"+idTrace+'>Trace</button></p>'
		}).appendTo(divParent);

		createDiv(code,"testInfo greyBox box",divName,idCode);
		createDiv(trace,"testInfo greyBox box",divName,idTrace);
		count = count + 1;
		
	}
	
	runFirstTime = function (){

		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:'rerun',argv:getURLParams()},
				
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
		$("#content").empty();
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
	
		createDiv(request["projectName"],"","content","projectName");

		createDiv(" ","totalTests greyBox box","content","");
		for (var group_name in groups) {
		    
		    var selector = "#"+group_name;
		    createDiv(group_name,"groupName","content",group_name);
		    createDiv('','groupContent', 'content', group_name+"content");
		    $(selector).prepend('<button type="button" class="buttonGroup classButton" data-name='+group_name+' data-type="group" data-action="runFile" >Run group</button>');
		    
		    $.each(groups[group_name], function(key, value) {
		    	
			      var classNameTest = getClassNameTest(value, passed, incomplete, skipped, errors);
			      separated_values = value.split("::"); 
			      var className = separated_values[0];
			      var test = separated_values[1];

			      var folder = getFolder(folders,className);
			      var fName = folder.replace(/\//g,'');
		    	  var idDivFolder =  group_name+fName;
		    	  var selector = "#"+idDivFolder;
		    	  
		    	  var existingFolder = $("#"+idDivFolder).html();
				  if (typeof existingFolder ===  "undefined") {
			    	  createDiv(folder,fName+' grey',group_name+"content",idDivFolder);
			    	  
			    	  var selector = "#"+idDivFolder+" > p";
			    	  $(selector).append('<input type="image" src="images/run-folder53.png" title="Run folder" class="buttonFolder classButton" data-name='+folder+' data-idfolder='+"."+idDivFolder+'  data-action="runFolder">');
			      }
				  
				  var existingClassName = $("#"+idDivFolder+className).html();
				  if (typeof existingClassName ===  "undefined") {
			    	  countClass = countDivs;
			    	
			    	  createDiv(className,'black margin20',idDivFolder, idDivFolder+className);
			    	  
			    	  var selector = "#"+idDivFolder+className+" > p";
			    	  $(selector).append('<input type="image" src="images/run-file50.png" title="Run file" class="buttonFile classButton" data-idfile='+idDivFolder+className+' data-name='+className+' data-type="file" data-action="runFile">');
			      }
			      var divName = className+'::'+test;
			      var divParent = idDivFolder+className;
			      
			      if (classNameTest == "testFailed box"){
			    	  var failedTest = getInfoFailedTests(value,info);
			    	  //createDivFailedTest = function (contentDiv,className,divParent,divName,file,line,message,trace)
			    	  createDivFailedTest(test,classNameTest,divParent,divName,failedTest['file'],failedTest['line'],
			    			  failedTest['message'],failedTest['trace'].replace(/#/g,'</br>#'),failedTest['code']);
			    	  
			    	  var selector = "#"+divName.replace(/:/g,'\\:')+" > p.nameFT";
			    	  createRunTestButton(selector,divName);
			    	
			      }else{
			    	  createDiv(test,classNameTest,divParent,divName);
			    	  var selector = "#"+divName.replace(/:/g,'\\:')+" > p";
			    	  createRunTestButton(selector,divName);
			      }
			      countDivs++;

			});
		   
		}
		displayTotalTests();
	}
	
	displayTotalTests = function(){
		var total = $(".testFailed").size() + $(".testOK").size() + $(".testIncomplete").size();
		var result = (total == 0) ? "No tests executed" : total+" test passing";
		$(".totalTests p").html(result);
	    if (total != 0){
		    $('.totalTests p').append('<button type="button" id="runAllTests" >Run All Tests</button>'
					+'<button type="button" id="hideTestsOK">Hide/Show Passed Tests</button>');
	    }
	}
	
	createRunTestButton = function (selector,divName){
		$(selector).append('<input type="image" src="images/run53.png" title="Run test" class="buttonTest classButton" data-name='+divName+' data-type="test" data-action="runTest">');
	}
	
	updateResults = function(request,folderName){
		countDivs = 0;
		passed = request["passed"];
		failures = request["failures"];
		errors = request["errors"];
		skipped = request["skipped"];
		incomplete = request["incomplete"];
		groups = request["groups"];
		folders = request["folders"];
		info = request["failedTests"];
		//createDiv(contentDiv,className,divParent,divName)

		for (var group_name in groups) {
		    var selector = "#"+group_name+" > p";
		
	    	var existingGroup = $(selector).html();

		    if (typeof existingGroup ===  "undefined") {
		    	createDiv(group_name,"groupName","content",group_name);
			    createDiv('','groupContent', 'content', group_name+"content");
			    $(selector).prepend('<button type="button" class="buttonGroup classButton" data-name='+group_name+' data-type="group" data-action="runFile" >Run group</button>');
		    }
		 
		    		    
		    $.each(groups[group_name], function(key, value) {
			      var classNameTest = getClassNameTest(value, passed, incomplete, skipped, errors);
			      separated_values = value.split("::"); 
			      var className = separated_values[0];
			      var test = separated_values[1];
			      
			      var folder = getFolder(folders,className);
			      folder = (folder == 0) ? folderName : folderName+folder;
			      
			      var fName = folder.replace(/\//g,''); 
			      var idDivFolder =  group_name+fName;
		    	  var divFolderSelector = "#"+idDivFolder;
			      
		    	  var folder_exists = $(divFolderSelector).html();
		    	  
		    	  if (typeof folder_exists ===  "undefined") {		    		  
		    		  createDiv(folder,fName+' grey',group_name+"content",idDivFolder);
		    		  
			    	  var selector = "#"+idDivFolder+" > p";
			    	  $(selector).append('<input type="image" src="images/run-folder53.png" title="Run folder" class="buttonFolder classButton" data-name='+folder+' data-idfolder='+"."+idDivFolder+'  data-action="runFolder">');
			      }
			     

			     
			      var divFileSelector = divFolderSelector+className;
			      if(typeof $(divFileSelector).html() === "undefined"){
						createDiv(className,'black margin20',idDivFolder, idDivFolder+className);
						var selector = "#"+idDivFolder+className+" > p";
						$(selector).append('<input type="image" src="images/run-file50.png" title="Run file" class="buttonFile classButton" data-idfile='+idDivFolder+className+' data-name='+className+' data-type="file" data-action="runFile">');
			      }
			      
			      var divName = className+'::'+test;
			      var divParent = idDivFolder+className;
			      var testSelector = "#"+divName.replace(/:/g,'\\:');
			      
			      $(testSelector).remove();
			     

			      if (classNameTest == "testFailed box"){
			    	  var failedTest = getInfoFailedTests(value,info);
			    	  	
			    	  createDivFailedTest(test,classNameTest,divParent,divName,failedTest['file'],failedTest['line'],
			    			  failedTest['message'],failedTest['trace'].replace(/#/g,'</br>#'));
			    	  
			    	  var selector = "#"+divName.replace(/:/g,'\\:')+" > p.nameFT";
			    	  createRunTestButton(selector,divName);
			    	
			      }else{
			    	  
			    	  createDiv(test,classNameTest,divParent,divName);
			    	  var selector = "#"+divName.replace(/:/g,'\\:')+" > p";
			    	  createRunTestButton(selector,divName);
			      }
			      
			      removeSingleElements();

			});
		}		
		displayTotalTests();
	}
	
	removeSingleElements = function (){
		elem = getSingleElement(".black");
		$(elem).remove();
		
		elem = getSingleElement(".grey");
		$(elem).remove();

		elem = getSingleElement(".groupContent");
		$(elem).prev().remove();
		$(elem).remove();
	    
	}
	
	getSingleElement = function(selector){
		var result;	
		$(selector).each(function(){
			if($(this).children().size() == 1){
				result = this;
			}
		});
		return result;
	}
	
	hideClassName = function(){
		$(".black").each(function(){
			if(!$(this).children().hasClass('testFailed')){
				$(this).slideToggle();
			}
		});
	}
	
	
	hideFolderName = function(){
		$(".grey").each(function(){
			if(!$(this).children().children().hasClass('testFailed')){
				$(this).slideToggle();
			}
		});
	}
	
	hideGroupName = function(){
		$(".groupContent").each(function(){
			if(!$(this).children().children().children().hasClass('testFailed')){
				$(this).slideToggle();
				$(this).prev().slideToggle();
			}
		});
		
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
	
	getInfoFailedTests = function(testName,infoFailedTests){
		var result;
		$.each(infoFailedTests, function(key, value) {
		      if (value['testName'] == testName){
		    	  result = value;
		      }
		});
		return result;
	}
	
	runSingleTest = function(request,test,idFile){
		var selector = "#"+test.replace(/:/g,'\\:');
		var testName = test.split('::');

		if ($(selector).hasClass('testOK')){
			$(selector).removeClass('testOK').addClass(request['result']);
		}else{
			if ($(selector).hasClass('testIncomplete')){
				$(selector).removeClass('testIncomplete').addClass(request['result']);
			}else{
				if ($(selector).hasClass('testFailed')){
					$(selector).removeClass('testFailed').addClass(request['result']);
					remove_content = selector+' p.fileFT,'+selector+' p.red,'+selector+' p.italic,'+selector+' .testInfo';
					$(remove_content).remove();
				}else{
					createDiv(testName[1],"box "+request['result'],idFile,test);
					var selectorCreateButton = "#"+test.replace(/:/g,'\\:')+" > p.nameNFT";
					createRunTestButton(selectorCreateButton,test);
				}
			}
		}
		$(selector+" p").removeClass('nameFT bold').addClass('nameNFT');
		if (request['result'] == 'testFailed'){
			idCode = "idCode"+count;
			idTrace = "idTrace"+count;

			$(selector+" p").removeClass('nameNFT').addClass('nameFT bold');
			$(selector).append('<p class="fileFT">'+request['file']+'</p>').append('<p class="red textRight bold">'+"+"+request['line']+'</p>').
						append('<p class="italic">'+request['message']+'<input type="image" src="images/bullet_arrow_down1.png" title="Show code" class="code classButton" data-idcode='+"#"+idCode+' data-file='+request['file']+' data-line='+request['line']+' data-test='+testName[1]+'><button type="button" class="trace classButton" data-idtrace='+"#"+idTrace+'>Trace</button></p>');
			
			createDiv("","testInfo greyBox box",test,idCode);
			createDiv(request['trace'].replace(/#/g,'</br>#'),"testInfo greyBox box",test,idTrace);
			count = count + 1;
		}
		
		displayTotalTests();
	}
	
	requestRunTests = function(element){
		var idFile = $(element).data('idfile');
		var nameRun = $(element).data('name');
		var typeRun = $(element).data('type');
		var action = $(element).data('action');

		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:action,name:nameRun,argv:getURLParams(),type:typeRun},
			success:function(request){
				switch (typeRun){
					case "file":
					case "group":
						$.each(request['result'], function(key, value){
							runSingleTest(value,value['testName'],idFile);
						});
					break;
					case "test":
						runSingleTest(request,nameRun);
					break;
				}
			},
		
			error: function(request){
				alert("an error ocurred in ajax request");
			}
		});
	}
	
	$("#content").on('click','.greyBox p #hideTestsOK', function() {
		hideGroupName();
		hideFolderName();
		hideClassName();
		$(".testIncomplete").slideToggle(); 
		$(".testOK").slideToggle(); 

	});
	
	
	$("#content").on('click','.greyBox p #runAllTests', function() {
		runAllTests();
	});
	
	$("#content").on('click',".groupName .buttonGroup", function() {
		requestRunTests(this);
	});
	
	$("#content").on('click',".groupContent .grey p .buttonFolder", function() {
		var folderName = $(this).data('name');
		var idFolder = $(this).data('idfolder');

		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:'runFolder',folderName:folderName,argv:getURLParams()},
			success:function(request){	
				updateResults(request['result'],folderName);
			},
		
			error: function(request){
				alert("an error ocurred in ajax request");
			}
		});
		//requestRunTests(this);
	});
	$("#content").on('click',".groupContent .black .buttonFile", function() {
		requestRunTests(this);
	});
	$("#content").on('click',".groupContent .grey .black .box p .buttonTest", function() {
		requestRunTests(this);
	});
	

	$( "#content" ).on('click',".groupContent .grey .black .testFailed .italic .code", function(){
		var idCode = $(this).data('idcode');
		$(idCode).slideToggle();
		
	});
	
	$( "#content" ).on('click',".groupContent .grey .black .testFailed .italic .trace", function(){
		var idTrace = $(this).data('idtrace');
		$(idTrace).slideToggle();
	});
	
	runFirstTime();
	
	
});

	












	




