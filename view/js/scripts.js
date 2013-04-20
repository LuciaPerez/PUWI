
$(document).on('ready',function(){

	var countDivs = 0;
	var countFolder = 0;
	var countClass = 0;
	var showedClass = '';
	var showedFolder = '';
	
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
		    html: '<p>'+contentDiv+'</p>'
		}).appendTo(divParent);
	}
	
	createDivFailedTest = function (contentDiv,className,divParent,divName,file,line,message,trace) { 
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

		createDiv("","testInfo totalTests box",divName,idCode);
		createDiv(trace,"testInfo totalTests box",divName,idTrace);
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
		if (request["totalTests"] == 0){
		
			createDiv(request["projectName"],"","content","projectName");
		}else{
			createDiv(request["totalTests"]+" test passing","totalTests box","content","");
			$('.totalTests p').append('<button type="button" id="runAllTests" >Run All Tests</button>'
					+'<button type="button" id="hideTestsOK">Hide/Show Passed Tests</button>');
		}

		var array_keys = new Array();
		var array_values = new Array();
		for (var group_name in groups) {
		    array_keys.push(group_name);
		    var countGroup = countDivs;
		    createDiv(group_name,"groupName","content","group"+countGroup);
		    createDiv('','groupContent', 'content', 'groupName'+countGroup);
		    		    
		    $.each(groups[group_name], function(key, value) {
		    	
			      var classNameTest = getClassNameTest(value, passed, incomplete, skipped, errors);
			      separated_values = value.split("::"); 
			      var className = separated_values[0];
			      var test = separated_values[1];

			      var folder = getFolder(folders,className);
			      if (!is_showedFolder(folder)){
			    	  countFolder = countDivs;
			    	  var fName = folder.split("/")[0];
			    	  createDiv(folder,fName+' grey','groupName'+countGroup,'folderName'+countFolder);
			    	  var selector = "#"+"folderName"+countFolder+" > p";

			    	  $(selector).append('<button type="button" class="buttonFolder classButton" data-folder='+folder+' data-idfolder='+"."+fName+'>Run folder</button>');
			      }
			      
			      if (!is_showedClass(className)){
			    	  countClass = countDivs;
			    	  createDiv(className,'black','folderName'+countFolder, 'fileName'+countClass);
			      }
			      var divName = className+'::'+test;
			      if (classNameTest == "testFailed box"){
			    	  var failedTest = getInfoFailedTests(value,info);
			    	  createDivFailedTest(test,classNameTest,'fileName'+countClass,divName,failedTest['file'],failedTest['line'],
			    			  failedTest['message'],failedTest['trace'].replace(/#/g,'</br>#'));
			    	  
			    	  var selector = "#"+divName.replace(/:/g,'\\:')+" > p.nameFT";
			    	 $(selector).append('<button type="button" class="buttonTest classButton" data-test='+divName+'>Run test</button>');
			      }else{
			    	  createDiv(test,classNameTest,'fileName'+countClass,divName);
			    	  var selector = "#"+divName.replace(/:/g,'\\:')+" > p";
			    	  $(selector).append('<button type="button" class="buttonTest classButton" data-test='+divName+'>Run test</button>');
			      }
			      countDivs++;

			});
		   
		}
	}
	
	updateResults = function(request,folderName){
		var is_newFolder = false;
		countDivs = 0;
		passed = request["passed"];
		failures = request["failures"];
		errors = request["errors"];
		skipped = request["skipped"];
		incomplete = request["incomplete"];
		groups = request["groups"];
		folders = request["folders"];
		info = request["infoFailedTests"];
	
		//createDiv(contentDiv,className,divParent,divName)


		var array_keys = new Array();
		var array_values = new Array();
		for (var group_name in groups) {
		    array_keys.push(group_name);
		    
		    var countGroup = countDivs;
		    var selector = "#"+"group"+countGroup;
		    $(".groupName p").each(function(){
		    	alert($.type($(".groupName p").contents()));
		    	var existingGroup = $(".groupName p").html();
		    	//var existingGroup = $(".groupName").text();
		    	//alert(existingGroup+" => "+group_name);
		    	
			    if (existingGroup!= group_name) {
			    	//alert(selector+"--" +$(selector).length);
				    createDiv(group_name,"groupName","content","group"+countGroup);
				    createDiv('','groupContent', 'content', 'groupName'+countGroup);
				    is_newFolder = true;
			    }else{
			    	$(".title").html("FUNCIONAAAA");
			    }
		    });
		    		    
		    $.each(groups[group_name], function(key, value) {
			      var classNameTest = getClassNameTest(value, passed, incomplete, skipped, errors);
			      separated_values = value.split("::"); 
			      var className = separated_values[0];
			      var test = separated_values[1];

			      if(is_newFolder == true){
				      
				      alert("mostrada "+is_showedFolder(folderName)+" "+folderName);
				      if (!is_showedFolder(folder)){
				    	  alert("entra");
				    	  countFolder = countDivs;
				    	  var fName = folderName.split("/")[0];
				    	  createDiv(folderName,fName+' grey','groupName'+countGroup,'folderName'+countFolder);
				    	  var selector = "#"+"folderName"+countFolder+" > p";
	
				    	  $(selector).append('<button type="button" class="buttonFolder classButton" data-folder='+folder+' data-idfolder='+"."+fName+'>Run folder</button>');
				      }
			      }
			      
			      /*if (!is_showedClass(className)){
			    	  countClass = countDivs;
			    	  createDiv(className,'black','folderName'+countFolder, 'fileName'+countClass);
			      }
			      
			      if (classNameTest == "testFailed box"){
			    	  var failedTest = getInfoFailedTests(value,info);
			    	  createDivFailedTest(test,classNameTest,'fileName'+countClass,className+'::'+test,failedTest['file'],failedTest['line'],
			    			  failedTest['message'],failedTest['trace'].replace(/#/g,'</br>#'));
			      }else{
			    	  createDiv(test,classNameTest,'fileName'+countClass,className+'::'+test);
			      }*/
			      countDivs++;
			});
		   
		}		
		alert("fin for");
		var total = $(".testFailed").size() + $(".testOK").size();
	    $(".totalTests p").empty().html(total+" test passing");
		
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
	
	runSingleTest = function(request,test){
		var selector = "#"+test.replace(/:/g,'\\:');
		var testName = test.split('::');

		if ($(selector).hasClass('testOK')){
			$(selector).removeClass('testOK').addClass(request['result']);
		}
		if ($(selector).hasClass('testIncomplete')){
			$(selector).removeClass('testIncomplete').addClass(request['result']);
		}
		if ($(selector).hasClass('testFailed')){
			$(selector).removeClass('testFailed').addClass(request['result']);
			remove_content = selector+' p.fileFT,'+selector+' p.red,'+selector+' p.italic,'+selector+' .testInfo';
			$(remove_content).remove();
		}
		if (request['result'] == 'testFailed'){
			idCode = "idCode"+count;
			idTrace = "idTrace"+count;
			$(selector).append('<p class="fileFT">'+request['file']+'</p>').append('<p class="red textRight bold">'+"++"+request['line']+'</p>').
						append('<p class="italic">'+request['message']+'<input type="image" src="images/bullet_arrow_down1.png" title="Show code" class="code classButton" data-idcode='+"#"+idCode+' data-file='+request['file']+' data-line='+request['line']+' data-test='+testName[1]+'><button type="button" class="trace classButton" data-idtrace='+"#"+idTrace+'>Trace</button></p>');
			
			createDiv("","testInfo totalTests box",test,idCode);
			createDiv(request['trace'].replace(/#/g,'</br>#'),"testInfo totalTests box",test,idTrace);
			count = count + 1;
		}
	}
	
	$("#content").on('click','.totalTest p #hideTestsOK', function() {
		$(".testOK").slideToggle(); 
	});
	
	$("#content").on('click','.totalTests p #runAllTests', function() {
		runAllTests();
	});
	
	$("#content").on('click',".groupContent .grey p .buttonFolder", function() {
		var folderName = $(this).data('folder');
		var idFolder = $(this).data('idfolder');

		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:'runFolder',folderName:folderName,argv:getURLParams()},
			success:function(request){
				//$(idFolder).empty();
				updateResults(request,folderName);
			},
		
			error: function(request){
				alert("an error ocurred in ajax request");
			}
		});
	});
	
	$("#content").on('click',".groupContent .grey .black .box p .buttonTest", function() {
		var test = $(this).data('test');
		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:'runTest',testName:test,argv:getURLParams()},
			success:function(request){
				//$(idFolder).empty();
				//updateResults(request,folderName);
				runSingleTest(request,test);
			},
		
			error: function(request){
				alert("an error ocurred in ajax request");
			}
		});
	});

	$( "#content" ).on('click',".groupContent .grey .black .testFailed .italic .code", function(){
		var idCode = $(this).data('idcode');
		var file = $(this).data('file');
		var line = $(this).data('line');
		var testName = $(this).data('test');

		$(idCode).slideToggle();
		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:'displayCode',file:file,line:line,testName:testName},
			success:function(request){
				$(idCode).html('<p>'+request['code']+'</p>');
			},
		
			error: function(request){
				alert("an error ocurred in ajax request");
			}
		});
		
	});
	
	$( "#content" ).on('click',".groupContent .grey .black .testFailed .italic .trace", function(){
		var idTrace = $(this).data('idtrace');
		$(idTrace).slideToggle();
	});
	
	runFirstTime();
	
	
});

	












	




