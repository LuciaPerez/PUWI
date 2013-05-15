
$(document).on('ready',function(){

	var is_hidden = false;
	var projectName;
	
	getURLParams = function(){
		var pageURL = window.location.toString().split('?');
		var URLVariables = pageURL[1].split('&');
		
		var puwiParam = URLVariables[0].split('=');
		var projectParam = URLVariables[1].split('=');
		return [puwiParam[1], projectParam[1]];
		
	}

	createDiv = function (contentDiv,className,divParent,divName, pClass) {
		divParent="#"+divParent.replace(/:/g,'\\:');
		$('<div/>', {
		    id: divName,
		    class: className,
		    html: '<p class="nameNFT '+pClass+'">'+contentDiv+'</p>'
		}).appendTo(divParent);
	}
	
	createDivFailedTest = function (contentDiv,className,divParent,divName,file,line,message,trace,code,pClass) { 
		divParent="#"+divParent;
			$('<div/>', {
		    id: divName,
		    class: className,
		    html: '<p class="nameFT bold '+pClass+'">'+contentDiv+'</p>'+'<p class="fileFT left">'+file+'</p>'+'<p class="red textRight bold">+'+line+'</p>'
		          +'<p class="italic">'+message
		          +'<input type="image" src="images/console.png" title="Display trace" class="trace classButton" data-idtrace='+"#"+divName+"trace"+'>'
		          +'<input type="image" src="images/bullet_arrow_down1.png" class="code classButton" data-idcode='+"#"+divName+"code"+' data-file='+file+' data-line='+line+' data-test='+contentDiv+'></p>'
		         
		}).appendTo(divParent);
		changeButtonsAppearance(".code","Display test code","bullet_arrow_down1.png","arrow_down_hover.png");
		createDiv(code,"testInfo greyBox box",divName,divName+"code");
		createDiv(trace,"testInfo darkBox box",divName,divName+"trace");		
	}
	
	runFirstTime = function (){
		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:'rerun',argv:getURLParams()},
				
			success: function(request){
				projectName = request['result']["projectName"];
				//createDiv(request['result']["projectName"],"","content","projectName");
				createDiv("","totalTests greyBox box","content","");

				updateResults(request['result'],'','');
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
			    $(selector).prepend('<input type="image" src="images/run_group.png" class="buttonGroup classButton" data-name='+group_name+' data-type="group" data-action="runTests">');

			    showGroupsInOrder(group_name);		  
			    changeButtonsAppearance(".buttonGroup","Run group","run_group.png","run_group_hover.png");
		    }
		    
		    $.each(groups[group_name], function(key, value) {
				separated_values = value.split("::"); 
				var className = separated_values[0];
				var test = separated_values[1];
				
		       if (runSingleTest ==  '' || (runSingleTest == value && typeUpdate == 'test') || (runSingleTest == group_name && typeUpdate == 'group') || (runSingleTest == className && typeUpdate == 'file')){
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
			    	  $(selector).append('<input type="image" src="images/run_folder.png" class="buttonFolder classButton" data-name='+folder+' data-idfolder='+"."+idDivFolder+'  data-action="runFolder">');
			    	  
			    	  changeButtonsAppearance(".buttonFolder","Run folder","run_folder.png","run_folder_hover.png");
		    	  }

			      var divFileSelector = divFolderSelector+className;
			      if(typeof $(divFileSelector).html() === "undefined"){
						createDiv(className,'black margin20',idDivFolder, idDivFolder+className);
						var selector = "#"+idDivFolder+className+" > p";
						$(selector).append('<input type="image" src="images/run_file.png" class="buttonFile classButton" data-idfile='+idDivFolder+className+' data-name='+className+' data-type="file" data-action="runTests">');
			      
						changeButtonsAppearance(".buttonFile","Run file","run_file.png","run_file_hover.png");
			      }
			      
			      var divName = value;
			      var divParent = idDivFolder+className;
			      var testSelector = "#"+divName.replace(/:/g,'\\:');
			      
			      if(typeUpdate == 'test' && typeof $(testSelector).attr("id") !== "undefined"){		    
			    	    removeOldClass(testSelector,classNameTest);
						if (classNameTest == 'testFailed box'){
							updateTestFailedContent(testSelector,test,info,divName);
						}else{
							hideSingleTest(divName);
						}

			      }else{
						$(testSelector).remove();
						if (classNameTest == "testFailed box"){
							 
							  var failedTest = getInfoFailedTests(value,info);
							  
							  createDivFailedTest(test,classNameTest,divParent,divName,failedTest['file'],failedTest['line'],
									  failedTest['message'],failedTest['trace'].replace(/#/g,'</br>#'),failedTest['code'], "margin0");
							  
							  var selector = "#"+divName.replace(/:/g,'\\:')+" > p.nameFT";
							  createRunTestButton(selector,divName);
							  
						 }else{
							  createDiv(test,classNameTest,divParent,divName, "margin0");
							  var selector = "#"+divName.replace(/:/g,'\\:')+" > p";
							  createRunTestButton(selector,divName);
							  
							  hideSingleTest(divName);
						 }
			      }

		       }
			});
		}	

		checkDissapearedTests(request,typeUpdate,runSingleTest,folderName);
	    removeSingleElements();
		displayTotalTests();
	}
	
	changeButtonsAppearance = function(selector,title,image,image_hover){
		 $(selector).hover(
	    			function(){
	    				$(this).attr('src',"images/"+image_hover);$(this).attr('title',title);
	    			}, 
	    			function(){$(this).attr('src',"images/"+image);}
	    	);
	}
	
	removeOldClass = function(testSelector,classNameTest){
		if ($(testSelector).hasClass('testOK')){
			$(testSelector).removeClass('testOK').addClass(classNameTest);
		}else{
			if ($(testSelector).hasClass('testIncomplete')){
				$(testSelector).removeClass('testIncomplete').addClass(classNameTest);
			}else{
				if ($(testSelector).hasClass('testFailed')){
					
					$(testSelector).removeClass('testFailed box').addClass(classNameTest);
					remove_content = testSelector+' p.fileFT,'+testSelector+' p.red,'+testSelector+' p.italic,'+testSelector+' .testInfo';
					$(remove_content).remove();
				}
			}
		}
		$(testSelector+" p").removeClass('nameFT bold').addClass('nameNFT');
	}
	
	updateTestFailedContent = function(testSelector,testName,info,divName){
		var failedTest = getInfoFailedTests(divName,info);
		
		$(testSelector+" p").removeClass('nameNFT').addClass('nameFT bold');
		$(testSelector).append('<p class="fileFT">'+failedTest['file']+'</p>');
		$(testSelector).append('<p class="red textRight bold">'+"+"+failedTest['line']+'</p>');
		$(testSelector).append('<p class="italic">'+failedTest['message']+
				'<input type="image" src="images/console.png" title="Display trace" class="trace classButton" data-idtrace='+"#"+divName+"trace"+'>'+
				'<input type="image" src="images/bullet_arrow_down1.png" title="Display code" class="code classButton" data-idcode='+"#"+divName+"code"+' data-file='+failedTest['file']+' data-line='+failedTest['line']+' data-test='+testName+'></p>');

		createDiv(failedTest['code'],"testInfo greyBox box",divName,divName+"code");
		createDiv(failedTest['trace'].replace(/#/g,'</br>#'),"testInfo darkBox box",divName,divName+"trace");
	}
	
	showGroupsInOrder = function (groupName){
	    var divBefore = sortGroups(groupName);
	    if (typeof divBefore === "undefined"){
	    	$("#"+groupName).insertAfter(".totalTests");
	    }else{
	    	$("#"+groupName).insertAfter("#"+divBefore+"content");		    	
	    }
	    $("#"+groupName+"content").insertAfter("#"+groupName);
	}
	
	sortGroups = function(groupName){
		var result;
		$(".groupName").each(function(){
			if($(this).text() > groupName){
				result = $(this).attr("id");
			}
		});
		return result;
	}
	
	checkDissapearedTests = function(request,type, nameTest, folderName){
		var ids;
		switch (type){
			case "file":
				ids = getFileIds(".black",nameTest);
				$.each(ids, function(key,value){
					selector = "#"+value+" .box";
					removeDissapearedTest(selector,request);
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
	
	removeDissapearedTest = function(selector,request){
		$(selector).each(function(){
			if (!$(this).hasClass('testInfo')){
				var testName = $(this).attr('id');
				res = checkIfTestExists(testName,request);
				if(!res){ $("#"+testName.replace(/:/g,'\\:')).remove(); }
			}
		});
	}
	
	displayTotalTests = function(){
		var total = $(".testFailed").size() + $(".testOK").size() + $(".testIncomplete").size();
		var result = (total == 0) ? "No tests executed" : "<strong>"+projectName+"</strong> project: "+total+" test passing";
		$(".totalTests p").html(result);
	    if (total != 0){
		    $('.totalTests p').append('<input type="image" src="images/run.png" id="runAllTests" class="classButton">'
					+'<input type="image" src="images/hide.png" id="hideTestsOK" class="hideTests classButton">');
		    

		    changeButtonsAppearance("#runAllTests","Run all tests","run.png","run_hover.png");
	    	$("#hideTestsOK").hover(
	    			function(){
	    				var title = (is_hidden) ? 'Show passed tests' : 'Hide passed tests';
	    				$(this).attr('src','images/hide_hover.png');$(this).attr('title',title);
	    			}, 
	    			function(){$(this).attr('src','images/hide.png');}
	    	);
	    }
	}
	
	checkIfTestExists = function(nameTest, arrayData){
		var exists = false;
		$.each(arrayData,function(key,value){
			if((key == 'passed' || key == 'failures' || key == 'errors' || key == 'skipped' || key == 'incomplete') && !exists){
				if ($.inArray(nameTest,value) >= 0){
					exists = true;
				}
			}
		});
		return exists;
	}
	
	createRunTestButton = function (selector,divName){
		$(selector).append('<input type="image" src="images/run.png" class="buttonTest classButton" data-name='+divName+' data-type="test" data-action="runTests">');
	
		changeButtonsAppearance(".buttonTest","Run test","run.png","run_hover.png");
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
	
	hideSingleTest = function(divName){
		if(is_hidden){
  		  var testSelector = "#"+divName.replace(/:/g,'\\:');
  		  var classSelector = "#"+$(testSelector).parent().attr("id");
  		  var folderSelector = "#"+$(classSelector).parent().attr("id");
  		  var groupSelector = "#"+$(folderSelector).parent().attr("id");
  		  hideElements(testSelector,classSelector,folderSelector,groupSelector);
		}
	}
	
	hideClassName = function(selector){
		$(selector).each(function(){
			if(!$(this).children().hasClass('testFailed')){
				$(this).slideToggle();
			}
		});
	}

	hideFolderName = function(selector){
		$(selector).each(function(){
			if(!$(this).children().children().hasClass('testFailed')){
				$(this).slideToggle();
			}
		});
	}
	
	hideGroupName = function(selector){
		$(selector).each(function(){
			if(!$(this).children().children().children().hasClass('testFailed')){
				$(this).slideToggle();
				$(this).prev().slideToggle();
			}
		});
		
	}
	
	hideElements = function(testSelector,classSelector,folderSelector,groupSelector){
		hideGroupName(groupSelector);
		hideFolderName(folderSelector);
		hideClassName(classSelector);
		$(testSelector).slideToggle(); 
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
		var is_empty;
		$.ajax({
			url:  'http://localhost/PUWI/PUWI_LoadJSON.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {action:action,name:nameRun,argv:getURLParams(),type:typeRun},
			success:function(request){
				is_empty = checkEmptyResults(request['result']);
				switch (typeRun){
					case "file":
						if (is_empty == true){
							var idsFile = getFileIds(".black",nameRun);
							$.each(idsFile,function(key,value){
								$("#"+value).remove();
							});
							removeSingleElements();	
						}else{
							updateResults(request['result'],'',nameRun,'file');
						}
					break;
					case "group":
						if (is_empty == true){
							$("#"+nameRun.replace(/:/g,'\\:')).next().remove();
							$("#"+nameRun.replace(/:/g,'\\:')).remove();
							
							removeSingleElements();	
						}else{
							updateResults(request['result'],'',nameRun,'group');
						}

					break;
					case "test":
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
	
	getFileIds = function(selector,nameRun){
		var ids = new Array();
		$(selector).each(function(){
			if($(this).children("p.nameNFT").text() == nameRun){
				ids.push($(this).attr("id"));
			}
		});
		return ids;
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
	
	set_isHidden = function(){
		is_hidden = !is_hidden;
	}
	
	$("#content").on('click','.greyBox p #hideTestsOK', function() {
		hideElements(".testIncomplete,.testOK",".black",".grey",".groupContent");
		set_isHidden();
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
	

	$( "#content" ).on('click',".groupContent .grey .black .testFailed .code", function(){
		var idCode = $(this).data('idcode').replace(/:/g,'\\:');

		$(idCode).slideToggle();
		
	});
	
	$( "#content" ).on('click',".groupContent .grey .black .testFailed .trace", function(){
		var idTrace = $(this).data('idtrace').replace(/:/g,'\\:');
		$(idTrace).slideToggle();
	});
	
	runFirstTime();
	
	
});

	












	




