
$(document).on('ready',function(){

	var hide_tests = true;
	var class_isHidden = " isNoHidden";
	var projectName;
	var actualIdTest;
	
	/*
	 * Button actions 
	 */
	$("#content").on('click','.greyBox p #hideTestsOK', function() {
		set_isHidden();
		hideElements(".testIncomplete,.testOK",".black",".grey",".groupContent",hide_tests);

		
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
				updateResults(request['result'],folderName,'',"folder");
			},
		
			error: function(request){
				alert("An error ocurred in ajax request");
			}
		});
	});
	
	$("#content").on('click',".groupContent .black .buttonFile", function() {
		requestRunTests(this);
	});
	
	$("#content").on('click',".groupContent .grey .black .box p .buttonTest", function() {
		requestRunTests(this);
	});
	

	$( "#content" ).on('click',".groupContent .grey .black .testFailed .code", function(){
		var idCode = $(this).data('idcode').replace(/:/g,'\\:');
		$(idCode).slideToggle();
		
	});
	
	$( "#content" ).on('click',".groupContent .grey .black .testFailed .trace", function(){
		var idTrace = $(this).data('idtrace').replace(/:/g,'\\:');
		$(idTrace).slideToggle();
	});
	
	/**
	 * Inspect URL to get 'PUWI' and project location
	 * 
	 * @return array
	 */
	getURLParams = function(){
		var pageURL = window.location.toString().split('?');
		var URLVariables = pageURL[1].split('&');
		
		var puwiParam = URLVariables[0].split('=');
		var projectParam = URLVariables[1].split('=');
		return [puwiParam[1], projectParam[1]];
	}

	/**
	 * Change value of hide_tests variable
	 */
	set_isHidden = function(){
		hide_tests = !hide_tests;
		class_isHidden = (hide_tests == true) ? " isHidden" : " isNoHidden";
	}
	
	/**
	 * Manage selectors to hide elements
	 */
	hideElements = function(testSelector,classSelector,folderSelector,groupSelector,showTest){
		hideGroupName(groupSelector,showTest);
		hideFolderName(folderSelector,showTest);
		hideClassName(classSelector,showTest);

		changeHiddenClass(testSelector,showTest);
	}
	
	/**
	 * Check if there are some failed tests in a group
	 * @param string selector
	 */
	hideGroupName = function(selector,showTest){
		$(selector).each(function(){
			if(!$(this).children().children().children().hasClass('testFailed')){
				changeHiddenClass("#"+$(this).attr("id"),showTest);
				changeHiddenClass("#"+$(this).prev().attr("id"),showTest);
			}else{
				$("#"+$(this).attr("id")).removeClass('isHidden').addClass('isNoHidden');
				$("#"+$(this).prev().attr("id")).removeClass('isHidden').addClass('isNoHidden');
			}
		});
	}
	
	/**
	 * Check if there are some failed tests in a folder
	 * @param string selector
	 */
	hideFolderName = function(selector,showTest){
		$(selector).each(function(){
			if(!$(this).children().children().hasClass('testFailed')){
				changeHiddenClass("#"+$(this).attr("id"),showTest);
			}else{
				$("#"+$(this).attr("id")).removeClass('isHidden').addClass('isNoHidden');
			}
		});
	}
	
	/**
	 * Check if there are some failed tests in a class
	 * @param string selector
	 */
	hideClassName = function(selector,showTest){
		$(selector).each(function(){	
			if(!$(this).children().hasClass('testFailed')){
				changeHiddenClass("#"+$(this).attr("id"),showTest);
			}else{
				$("#"+$(this).attr("id")).removeClass('isHidden').addClass('isNoHidden');
			}
		});
		
	}
	
	/**
	 * Change class to hide a test
	 * @param string selector
	 */
	changeHiddenClass = function(selector,showTest){
		if (hide_tests){
			if ($(selector).hasClass("isNoHidden")){
				$(selector).removeClass('isNoHidden').addClass('isHidden');
			}
		}else{
			if ($(selector).hasClass("isHidden")){
				$(selector).text();
				$(selector).removeClass('isHidden').addClass('isNoHidden');
			}
		}
	}
	
	/**
	 * Show only failed tests or all of them if there isn't any failed test
	 */
	showFaildedTest = function(selector){
		
		if ($("#content .groupContent .grey .black").children().hasClass('testFailed')){
			hideElements(".testIncomplete,.testOK",".black",".grey",".groupContent",hide_tests);
		}else{
			hide_tests = false;
			hideElements(".testIncomplete,.testOK",".black",".grey",".groupContent",hide_tests);
		}
	}
	
	/**
	 * Remove all the showed elements and show results again
	 */
	runAllTests = function(){
		//hide_tests = false;
		$("#content").empty();
		runFirstTime();
	}
	
	/**
	 * Get results of running tests from a project
	 */
	runFirstTime = function (){
		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:'rerun',argv:getURLParams()},
				
			success: function(request){	
				projectName = request['result']["projectName"];
				createDiv("","totalTests greyBox box","content","");
				
				updateResults(request['result'],'','');
			},
			error: function(request){
				alert("An error ocurred in AJAX request. \nOne of the most common reasons is to have a 'print' instruction in PUWI code.");
			}
		});
	}

	/**
	 * Create dynamic divs
	 */
	createDiv = function (contentDiv,className,divParent,divName, pClass) {
		divParent="#"+divParent.replace(/:/g,'\\:');
		$('<div/>', {
		    id: divName,
		    class: className,
		    html: '<p class="nameTest nameNFT '+pClass+'">'+contentDiv+'</p>'
		}).appendTo(divParent);
	}
	
	/**
	 * Create dynamic divs
	 */
	createDivNextSibling = function (contentDiv,className,divBefore,divName, pClass) {
		divBefore="#"+divBefore.replace(/:/g,'\\:');
		$('<div/>', {
		    id: divName,
		    class: className,
		    html: '<p class="nameTest nameNFT '+pClass+'">'+contentDiv+'</p>'
		}).insertAfter(divBefore);
	}
	
	/**
	 * Create dynamic divs for failed tests
	 */
	createDivFailedTest = function (contentDiv,className,divParent,divName,file,line,message,trace,code,pClass) {
		
		divParent="#"+divParent;
			$('<div/>', {
		    id: divName,
		    class: className,
		    html: '<p class="nameTest nameFT bold '+pClass+'">'+contentDiv+'</p>'+'<p class="fileFT left">'+file+'</p>'+'<p class="red textRight bold">+'+line+'</p>'
		          +'<p class="italic">'+message
		          +'<input type="image" src="images/console.png" title="Display trace" class="trace classButton" data-idtrace='+"#"+divName+"trace"+'>'
		          +'<input type="image" src="images/bullet_arrow_down1.png" class="code classButton" data-idcode='+"#"+divName+"code"+' data-file='+file+' data-line='+line+' data-test='+contentDiv+'></p>'
		         
		}).appendTo(divParent);
		changeButtonsAppearance(".code","Display test code","bullet_arrow_down1.png","arrow_down_hover.png");
		createDiv(code,"testInfo greyBox box",divName,divName+"code");
		createDiv(trace,"testInfo darkBox box",divName,divName+"trace");		
	}
	
	/**
	 * Run a group of tests (group, single test or file)
	 * 
	 * @param object element 
	 */
	requestRunTests = function(element){
		var idName = $(element).data('idname');
		var nameRun = $(element).data('name');
		var typeRun = $(element).data('type');
		var action = $(element).data('action');
		var is_empty;

		actualIdTest = idName;

		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:action,name:nameRun,argv:getURLParams(),type:typeRun},
			success:function(request){
				//hide_tests = false;
				
				is_empty = checkEmptyResults(request['result']);

				switch (typeRun){
					case "file":
						if (is_empty == true){
							var idsFile = getFileIds(".black",nameRun);
							$.each(idsFile,function(key,value){
								$("#"+value).remove();
							});
							removeSingleElements(request['result']['groups']);	
						}else{

							updateResults(request['result'],'',nameRun,'file');
						}
					break;
					case "group":
						if (is_empty == true){
							$("#"+nameRun.replace(/:/g,'\\:')).next().remove();
							$("#"+nameRun.replace(/:/g,'\\:')).remove();
							
							removeSingleElements(request['result']['groups']);	
						}else{
							updateResults(request['result'],'',nameRun,'group');
						}

					break;
					case "test":					
						if (is_empty == true){
							$("."+nameRun.replace(/:/g,'\\:')).remove();
							removeSingleElements(request['result']['groups']);	
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
	
	/**
	 * Check if results after run an element are empty (element is dissapeared)
	 * 
	 * @param array array_data
	 * @return boolean is_empty
	 */
	checkEmptyResults = function(array_data){
		
		var is_empty = true;
		$.each(array_data,function(key,value){
			
			if(key != 'groups' && key != 'folders' && key != 'projectName'){
				if(!$.isEmptyObject(value)){	
					is_empty = false;
				}
			}
		});
		return is_empty;
	}
	
	/**
	 * Check if exits some group without tests and removes it.
	 */
	removeSingleElements = function (groups){
		
		elem = getSingleElement(".black");
		$.each(elem,function(index,single_element){
			$(single_element).remove();
		});
		
		
		elem = getSingleElement(".grey");
		$.each(elem,function(index,single_element){
			$(single_element).remove();
		});

		elem = getSingleElement(".groupContent");
		$.each(elem,function(index,single_element){
			$(single_element).prev().remove();
			$(single_element).remove();
		});
		
		displayTotalTests(groups);
	}
	
	/**
	 * Check if an element has only child
	 * @param string selector
	 */
	getSingleElement = function(selector){	
		var result = [];	
		$(selector).each(function(){
			if($(this).children().size() == 1){
				result.push(this);
			}
		});

		return result;
	}
	
	/**
	 * Show results or update them.
	 * 
	 * @param array request
	 * @param string folderName
	 * @param string runSingleTest
	 * @param typeUpdate
	 */
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

		for (var group_name in groups) {
		    var selector = "#"+group_name+" > p";
	    	var existingGroup = $(selector).html();
	    	var divName;
	    	/*
	    	 * Show groups
	    	 */
		    if (typeof existingGroup ===  "undefined") {
		    	createDiv(group_name,"groupName isNoHidden","content",group_name);
			    createDiv('','groupContent isNoHidden', 'content', group_name+"content");
			    $(selector).prepend('<input type="image" src="images/run_group.png" class="buttonGroup classButton" data-name='+group_name+' data-type="group" data-action="runTests">');

			    showGroupsInOrder(group_name);		  
			    changeButtonsAppearance(".buttonGroup","Run group","run_group.png","run_group_hover.png");
		    }
		    
		    $.each(groups[group_name], function(key, value) {
		    	
				separated_values = value.split("::"); 
				var className = separated_values[0];
				var test = separated_values[1];
				
		       if (runSingleTest ==  '' || (runSingleTest == value && typeUpdate == 'test') || (runSingleTest == group_name && typeUpdate == 'group') || (runSingleTest == className && typeUpdate == 'file')){
			      var classNameTest = getClassNameTest(value, passed, incomplete, skipped, errors, failures);
			      /*
			       * Show folders
			       */
		    	  var folder = getFolder(folders,className);			      
			      
			      if (folderName != ''){
			    	  folder = (folder == 0) ? folderName : folderName+folder;
			      }
			     
			      var fName = folder.replace(/\//g,''); 
			      var idDivFolder =  group_name+fName;

		    	  var divFolderSelector = "#"+idDivFolder;
		    	  
		    	  var folder_exists = $(divFolderSelector).html();
		    	  
		    	  if (typeof folder_exists ===  "undefined") {
		    		  
		    		  createDiv(folder,fName+' grey isNoHidden',group_name+"content",idDivFolder);
		    		  
			    	  var selector = "#"+idDivFolder+" > p";
			    	  $(selector).append('<input type="image" src="images/run_folder.png" class="buttonFolder classButton" data-name='+folder+' data-idfolder='+"."+idDivFolder+'  data-action="runFolder">');
			    	  
			    	  changeButtonsAppearance(".buttonFolder","Run folder","run_folder.png","run_folder_hover.png");
		    	  }
		    	  /*
		    	   * Show class name
		    	   */
		    	  
			      var divFileSelector = divFolderSelector+className;
			      if(typeof $(divFileSelector).html() === "undefined"){
						createDiv(className,'black margin20 isNoHidden',idDivFolder, idDivFolder+className);
						var selector = "#"+idDivFolder+className+" > p";
						$(selector).append('<input type="image" src="images/run_file.png" class="buttonFile classButton" data-idfile='+idDivFolder+className+' data-name='+className+' data-type="file" data-action="runTests">');
			      
						changeButtonsAppearance(".buttonFile","Run file","run_file.png","run_file_hover.png");
			      }
			      
			      divName = value;
			      var divParent = idDivFolder+className;
			      var testSelector = "#"+divName.replace(/:/g,'\\:');  
			      var testNameWithoutAditionalInfo = divName.split('::');
			      var index = 0;
			      
			      /*
			       * Show each test
			       */
			      while(index < classNameTest.length){	    	  
			    	  var resultClassTestExecuted = classNameTest[index];
			    	  var testContent = classNameTest[index+1];

			    	  if(typeUpdate == 'test' && typeof $(actualIdTest).attr("id") !== "undefined"){
			    		  createOrDeleteDataProviderElements(divParent,divName,testSelector,classNameTest);
			
			    		  removeOldClass(testSelector+index,resultClassTestExecuted);

						  if (resultClassTestExecuted == 'testFailed box'){
								updateTestFailedContent(testSelector+index,test,info,testContent,divName+index);
								$(testSelector+index).removeClass("alwaysHidden");
								$(testSelector+index).removeClass("isHidden").addClass("isNoHidden");
						  }else{
							  	
								$(testSelector+index+" p").text(testNameWithoutAditionalInfo[1]);
								var selector = testSelector+index+" > p";
								createRunTestButton(selector,divName,testSelector+index);
								if (index>0){
									$(testSelector+index).addClass("alwaysHidden");
								}
						  }

			    		    
				      }else{

						$(testSelector+index).remove();
						
						if (resultClassTestExecuted == 'testFailed box'){
							
							  var failedTest = getInfoFailedTests(testContent,info);
							  testContent = failedTest['testName'].split("::");

							  resultClassTestExecuted += ' isNoHidden';
							  resultClassTestExecuted +=  " "+divName;

							  createDivFailedTest(testContent[1],resultClassTestExecuted,divParent,divName+index,failedTest['file'],failedTest['line'],
									  failedTest['message'],failedTest['trace'].replace(/#/g,'</br>#'),failedTest['code'], "margin0");
							  
							  var selector = testSelector+index+" > p.nameFT";
							  createRunTestButton(selector,divName,testSelector+index);

						 }else{

							 resultClassTestExecuted += ' isNoHidden';
							 resultClassTestExecuted +=  " "+divName;

							  createDiv(testNameWithoutAditionalInfo[1],resultClassTestExecuted,divParent,divName+index, "margin0");
							  var selector = testSelector+index+" > p";
							  createRunTestButton(selector,divName,testSelector+index);

						 }

				      }
			    	 
			    	  index+=2;
			      }

		       }
		       
			});
		}	

		checkDissapearedTests(request,typeUpdate,runSingleTest,folderName);
		
	    removeSingleElements(groups);
		displayTotalTests(groups);
		showFaildedTest();
		
		if(request['coverage'] != ''){
			$(".coverage").remove();

			$('#footer').before($("<p></p",{
				class: "coverage",
				html: "Code Coverage Analysis",
				css: { "text-align": "center" }
			}));
			
			$('#footer').before($("<iframe></iframe>", {
				class: "coverage",
		        src: request['coverage'],
		        scrolling: "auto",
		        frameborder: "no",
		        css: { "width": "100%", "height": "500", "text-align": "center" }
			}));

		}

	}
	
	createOrDeleteDataProviderElements = function(divParent,divName,testSelector,classNameTest){
		  var totalElementosCreados = $("."+divName.replace(/:/g,'\\:')).length;
		  var totalElementosReales = classNameTest.length/2;
		  var indexLastElement = totalElementosCreados*2;
		  indexLastElement = indexLastElement-2;
		  
		  if (totalElementosCreados > totalElementosReales){ 
			  var elems_to_delete = totalElementosCreados-totalElementosReales;
			  removeLastElementsDataProvider(testSelector,elems_to_delete,indexLastElement);
		  }
		  
		  if (totalElementosCreados < totalElementosReales){
			  var elems_to_create = totalElementosReales-totalElementosCreados;
			  createElementsDataProvider(divParent,divName,elems_to_create,indexLastElement);
		  }
	}

	removeLastElementsDataProvider = function (testSelector,elems_to_delete,indexLastElement){	  
		  var index_to_delete = 0;
		  while (index_to_delete < elems_to_delete){
			 $(testSelector+indexLastElement).remove();
			 indexLastElement = indexLastElement-2;
			 index_to_delete+=1;
		  } 
	}
	
	createElementsDataProvider = function (divParent,divName,elems_to_create,indexLastElement){
		  divBefore=divName+indexLastElement;
		  indexLastElement+=2;
		  
		  var index_to_create = 0;
		  while (index_to_create < elems_to_create){
			 createDivNextSibling('','testOK box '+divName,divBefore,divName+indexLastElement, "margin0");
			 indexLastElement = indexLastElement+2;
			 index_to_create+=1;
		  } 
	}
	
	/**
	 * Change groups name position
	 * 
	 * @param string groupName
	 */
	showGroupsInOrder = function (groupName){
	    var divBefore = sortGroups(groupName);
	    if (typeof divBefore === "undefined"){
	    	$("#"+groupName).insertAfter(".totalTests");
	    }else{
	    	$("#"+groupName).insertAfter("#"+divBefore+"content");		    	
	    }
	    $("#"+groupName+"content").insertAfter("#"+groupName);
	}
	
	/**
	 * Check group name position
	 * 
	 * @param string groupName
	 */
	sortGroups = function(groupName){
		var result;
		$(".groupName").each(function(){
			if($(this).text() > groupName){
				result = $(this).attr("id");
			}
		});
		return result;
	}
	
	/**
	 * Add hover property to run buttons
	 * 
	 * @param string selector
	 * @param string title
	 * @param string image
	 * @param string image_hover
	 */
	changeButtonsAppearance = function(selector,title,image,image_hover){
		$(selector).hover(
			function(){
				$(this).attr('src',"images/"+image_hover);$(this).attr('title',title);
			}, 
			function(){$(this).attr('src',"images/"+image);}
	    );
	}
	
	/**
	 * Search one test in each results array
	 * 
	 * @param string value
	 * @param array passed
	 * @param array incomplete
	 * @param array skipped
	 * @param array errors
	 */
	
	searchTestEachArrayOfData = function (arrayData, regexequals, regex, classNameTestResult,className){
		var search = '';
		$.each(arrayData,function(index,testName){
			if(testName.match(regex) || testName.match(regexequals)){
				
				if (className == "testOK box"){
					className = (classNameTestResult.length == 0)? "testOK box" : "testOK box alwaysHidden";
				}

				classNameTestResult.push(className);
				testName = testName.split('::');
				classNameTestResult.push(testName[1]);

			}
		});
		return classNameTestResult;
	}
	
	getClassNameTest = function(value, passed, incomplete, skipped, errors, failures){
		var regexequals = new RegExp(value+"$","gi");
		var regex = new RegExp (".*"+value+" .*","gi");

		var classNameTestResult = [];
		classNameTestResult = searchTestEachArrayOfData(errors, regexequals, regex,classNameTestResult,"testFailed box");
		classNameTestResult = searchTestEachArrayOfData(failures, regexequals, regex,classNameTestResult,"testFailed box");
		classNameTestResult = searchTestEachArrayOfData(passed, regexequals, regex,classNameTestResult,"testOK box");
		classNameTestResult = searchTestEachArrayOfData(incomplete, regexequals, regex,classNameTestResult,"testIncomplete box");
		classNameTestResult = searchTestEachArrayOfData(skipped, regexequals, regex,classNameTestResult,"testIncomplete box");
		
		return classNameTestResult;
	}
	
	/**
	 * Search a folder name from a class name
	 */
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
	
	/**
	 * Remove old class from an element, adding the new one.
	 */
	removeOldClass = function(testSelector,classNameTest){
		if ($(testSelector).hasClass('testOK box')){
			$(testSelector).removeClass('testOK box').addClass(classNameTest);
		}else{
			if ($(testSelector).hasClass('testIncomplete box')){
				$(testSelector).removeClass('testIncomplete box').addClass(classNameTest);
			}else{
				if ($(testSelector).hasClass('testFailed box')){
					$(testSelector).removeClass('testFailed box').addClass(classNameTest);
					remove_content = testSelector+' p.fileFT,'+testSelector+' p.red,'+testSelector+' p.italic,'+testSelector+' .testInfo';
					$(remove_content).remove();
				}
				
			}
		}
		$(testSelector+" p").removeClass('nameFT bold').addClass('nameNFT');
	}
	
	/**
	 * Add failed test information (file, line, message, trace, code)
	 */
	updateTestFailedContent = function(testSelector,testName,info,divName,idName){
		var failedTest = getInfoFailedTests(divName,info);
		$(testSelector+" p").removeClass('nameNFT').addClass('nameFT bold');
		var testNameWithoutClass = failedTest['testName'].split('::');
		$(testSelector+" p").text(testNameWithoutClass[1]);
		var selector = testSelector+" > p";
		
		createRunTestButton(selector,failedTest['testName'],testSelector);
		
		$(testSelector).append('<p class="fileFT">'+failedTest['file']+'</p>');
		$(testSelector).append('<p class="red textRight bold">'+"+"+failedTest['line']+'</p>');
		$(testSelector).append('<p class="italic">'+failedTest['message']+
				'<input type="image" src="images/console.png" title="Display trace" class="trace classButton" data-idtrace='+"#"+idName+"trace"+'>'+
				'<input type="image" src="images/bullet_arrow_down1.png" title="Display code" class="code classButton" data-idcode='+"#"+idName+"code"+' data-file='+failedTest['file']+' data-line='+failedTest['line']+' data-test='+testName+'></p>');
		
		changeButtonsAppearance(".code","Display test code","bullet_arrow_down1.png","arrow_down_hover.png");
		createDiv(failedTest['code'],"testInfo greyBox box",idName,idName+"code");
		createDiv(failedTest['trace'].replace(/#/g,'</br>#'),"testInfo darkBox box",idName,idName+"trace");
	}
	
	/**
	 * Search failed test information from array of failed tests information
	 */
	getInfoFailedTests = function(testName,infoFailedTests){
		var result;
		
		var regexequals = new RegExp(testName+"$","gi");
		var regex = new RegExp (testName+" .*","gi");
		
		$.each(infoFailedTests, function(key, value) {
			 
		      if (value['testName'].match(regex) || value['testName'].match(regexequals)){
		    	  result = value;
		      }
		});
		return result;
	}
	
	/**
	 * Create button to run each test
	 */
	createRunTestButton = function (selector,divName,idName){
		$(selector).append('<input type="image" src="images/run.png" class="buttonTest classButton" data-idname='+idName+' data-name='+divName+' data-type="test" data-action="runTests">');
	
		changeButtonsAppearance(".buttonTest","Run test","run.png","run_hover.png");
	}
	
	/**
	 * Choose group of tests to check if some of them are dissapeared
	 */
	checkDissapearedTests = function(request,type, nameTest, folderName){
		var ids;
		switch (type){
			case "file":			
				ids = getFileIds(".black",nameTest);
				$.each(ids, function(key,value){
					selector = "#"+value+" .box";
					removeDissapearedTest(selector,request,value);
				});
			break;
			
			case "folder":
				ids = getFileIds(".grey",folderName);
				$.each(ids, function(key,value){
					selector = "#"+value+" .black .box";
					removeDissapearedTest(selector,request);
				});
			break;
			
			case "group":
				ids = nameTest+"content";
				selector = "#"+ids+" .grey .black .box";
				removeDissapearedTest(selector,request);
			break;
		}
	}
	
	/**
	 * Search "id" attribute of each test belongs to an element (selector)
	 * 
	 * @param string selector
	 * @param string nameRun
	 * @return array ids
	 */
	getFileIds = function(selector,nameRun){
		var ids = new Array();
		$(selector).each(function(){
			if($(this).children("p.nameNFT").text() == nameRun){
				ids.push($(this).attr("id"));
			}
		});
		return ids;
	}
	
	/**
	 * Remove every test dissapeared
	 */
	removeDissapearedTest = function(selector,request,testName){

		$(selector).each(function(){
			var idTest = $(this).attr("id");
			idTest = idTest.replace(/:/g,'\\:');
			
			if (!$(this).hasClass('testInfo')){
				var idParent = $("#"+idTest).parent().attr("id");
				classTestName = $("#"+idParent+" > p").text();
				testName = $("#"+idTest+" > p.nameTest").text();

				res = checkIfTestExists(classTestName+"::"+testName,request);
				if(!res){ $("#"+idTest).remove(); }
			}
		});
	}
	
	/**
	 * Check if a test is showed or not
	 * 
	 * @param string nameTest
	 * @param array arrayData
	 * @return boolean exists
	 */
	checkIfTestExists = function(nameTest, arrayData){
		var exists = false;

		$.each(arrayData,function(key,value){
			
			if((key == 'passed' || key == 'failures' || key == 'errors' || key == 'skipped' || key == 'incomplete') && !exists){
				if ($.inArray(nameTest,value) >= 0){
					exists = true;
				}else{
					$.each(arrayData['failedTests'],function(key,value){
						if (nameTest == value['testName']){
							exists = true;
						}
						
					});
				}
			}
		});
		return exists;
	}
	
	/**
	 * Display the number of tests executed
	 */
	displayTotalTests = function(groups){
		
		var total = 0;
		$.each(groups,function(key,value){
			if($('#'+key).length){
				total+=value.length;
			}
		});

		var result = (total == 0) ? "No tests executed" : "<strong>"+projectName+"</strong> project: "+total+" test passing";
		$(".totalTests p").html(result);
	    if (total != 0){
		    $('.totalTests p').append('<input type="image" src="images/run.png" id="runAllTests" class="classButton">'
					+'<input type="image" src="images/hide.png" id="hideTestsOK" class="hideTests classButton">');
		    

		    changeButtonsAppearance("#runAllTests","Run all tests","run.png","run_hover.png");
	    	$("#hideTestsOK").hover(
	    			function(){
	    				var title = (hide_tests) ? 'Show passed tests' : 'Hide passed tests';
	    				$(this).attr('src','images/hide_hover.png');$(this).attr('title',title);
	    			}, 
	    			function(){$(this).attr('src','images/hide.png');}
	    	);
	    }
	}

	runFirstTime();
});

	












	




