
$(document).on('ready',function(){

	var countDivs = 0;
	var countFolder = 0;
	var countClass = 0;
	
	var is_hidden = false;
	
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
		          +'<input type="image" src="images/console.png" title="Display trace" class="trace classButton" data-idtrace='+"#"+idTrace+'>'
		          +'<input type="image" src="images/bullet_arrow_down1.png" title="Display code" class="code classButton" data-idcode='+"#"+idCode+' data-file='+file+' data-line='+line+' data-test='+contentDiv+'></p>'
		         
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
				createDiv(request['result']["projectName"],"","content","projectName");
				createDiv(" ","totalTests greyBox box","content","");
				updateResults(request['result'],'');
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

	updateResults = function(request,folderName,runSingleTest, typeUpdate){
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
			    $(selector).prepend('<button type="button" class="buttonGroup classButton" data-name='+group_name+' data-type="group" data-action="runTests" >Run group</button>');
		    }
		 
		    		    
		    $.each(groups[group_name], function(key, value) {
				separated_values = value.split("::"); 
				var className = separated_values[0];
				var test = separated_values[1];
		       if (typeof runSingleTest ===  "undefined" || (runSingleTest == value && typeUpdate == 'test') || (runSingleTest == group_name && typeUpdate == 'group') || (runSingleTest == className && typeUpdate == 'file')){
			      var classNameTest = getClassNameTest(value, passed, incomplete, skipped, errors);

			      var folder = getFolder(folders,className);
			      
			      if (folderName != ''){
			    	  folder = (folder == 0) ? folderName : folderName+folder;
			      }
			      
			      var fName = folder.replace(/\//g,''); 
			      var idDivFolder =  group_name+fName;
		    	  var divFolderSelector = "#"+idDivFolder;
			      
		    	  var folder_exists = $(divFolderSelector).html();
		    	  
		    	  if (typeof folder_exists ===  "undefined") {		    		  
		    		  createDiv(folder,fName+' grey',group_name+"content",idDivFolder);
		    		  
			    	  var selector = "#"+idDivFolder+" > p";
			    	  $(selector).append('<input type="image" src="images/run_folder.png" title="Run folder" class="buttonFolder classButton" data-name='+folder+' data-idfolder='+"."+idDivFolder+'  data-action="runFolder">');
			      }

			      var divFileSelector = divFolderSelector+className;
			      if(typeof $(divFileSelector).html() === "undefined"){
						createDiv(className,'black margin20',idDivFolder, idDivFolder+className);
						var selector = "#"+idDivFolder+className+" > p";
						$(selector).append('<input type="image" src="images/run_file.png" title="Run file" class="buttonFile classButton" data-idfile='+idDivFolder+className+' data-name='+className+' data-type="file" data-action="runTests">');
			      }
			      
			      var divName = value;
			      var divParent = idDivFolder+className;
			      var testSelector = "#"+divName.replace(/:/g,'\\:');
			      
			      $(testSelector).remove();

			      if (classNameTest == "testFailed box"){
			    	 
			    	  var failedTest = getInfoFailedTests(value,info);
			    	  
			    	  createDivFailedTest(test,classNameTest,divParent,divName,failedTest['file'],failedTest['line'],
			    			  failedTest['message'],failedTest['trace'].replace(/#/g,'</br>#'),failedTest['code']);
			    	  
			    	  var selector = "#"+divName.replace(/:/g,'\\:')+" > p.nameFT";
			    	  createRunTestButton(selector,divName);
			    	
			      }else{
			    	  createDiv(test,classNameTest,divParent,divName);
			    	  var selector = "#"+divName.replace(/:/g,'\\:')+" > p";
			    	  createRunTestButton(selector,divName);
			    	  if(is_hidden){
			    		  
			    		  $("#"+divName.replace(/:/g,'\\:')).slideToggle(); 
			    	  }
			      }
			      
			      removeSingleElements();
		       }
			});
		}		
		displayTotalTests();
	}
	
	displayTotalTests = function(){
		var total = $(".testFailed").size() + $(".testOK").size() + $(".testIncomplete").size();
		var result = (total == 0) ? "No tests executed" : total+" test passing";
		$(".totalTests p").html(result);
	    if (total != 0){
		    $('.totalTests p').append('<input type="image" src="images/run_hover.png" title="Run All Tests" id="runAllTests">'
					+'<button type="button" id="hideTestsOK">Hide/Show Passed Tests</button>');
	    }
	}
	
	createRunTestButton = function (selector,divName){
		$(selector).append('<input type="image" src="images/run_hover.png" title="Run test" class="buttonTest classButton" data-name='+divName+' data-type="test" data-action="runTests">');
	}

	removeSingleElements = function (){
		elem = getSingleElement(".black");
		$(elem).remove();
		
		elem = getSingleElement(".grey");
		$(elem).remove();

		elem = getSingleElement(".groupContent");
		$(elem).prev().remove();
		$(elem).remove();
		
		displayTotalTests();
	    
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
		$(".black ").each(function(){
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
	
	hideElements = function(selector){
		hideGroupName();
		hideFolderName();
		hideClassName();
		$(selector).slideToggle(); 
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
	
	
	requestRunTests = function(element){
		var idFile = $(element).data('idfile');
		var nameRun = $(element).data('name');
		var typeRun = $(element).data('type');
		var action = $(element).data('action');
		var is_empty = false;
		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:action,name:nameRun,argv:getURLParams(),type:typeRun},
			success:function(request){
				switch (typeRun){
					case "file":
						is_empty = checkEmptyResults(request['result']);
						if (is_empty == true){
							$('.black').each(function(){
								if($(this).children("p.nameNFT").text() == nameRun){
									$("#"+$(this).attr("id")).remove();
								}
							});
							removeSingleElements();	
						}else{
							updateResults(request['result'],'',nameRun,'test');
						}
					break;
					case "group":
						is_empty = checkEmptyResults(request['result']);
						if (is_empty == true){
							$("#"+nameRun.replace(/:/g,'\\:')).next().remove();
							$("#"+nameRun.replace(/:/g,'\\:')).remove();
							
							removeSingleElements();	
						}else{
							updateResults(request['result'],'',nameRun,'group');
						}

					break;
					case "test":
						is_empty = checkEmptyResults(request['result']);
						if (is_empty == true){
							$("#"+nameRun.replace(/:/g,'\\:')).remove();
							removeSingleElements();	
						}else{
							updateResults(request['result'],'',nameRun,'test');
						}
					break;
				}
			},
		
			error: function(request){
				alert("an error ocurred in ajax request");
			}
		});
	}
	
	checkEmptyResults = function($array_data){
		var is_empty = true;
		$.each($array_data,function(key,value){
			if(key != 'groups' && key != 'folders'){
				if(!$.isEmptyObject(value)){	
					is_empty = false;
				}
			}
		});
		return is_empty;
	}
	
	$("#content").on('click','.greyBox p #hideTestsOK', function() {
		hideElements(".testIncomplete,.testOK");
		is_hidden = !is_hidden;
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

	












	




