$(document).ready(function(){

	hideTestOK = function(){
		$(".testOK").slideToggle();

	}

	hideCode = function(){
		$(".testInfo").slideToggle();

	}
	
	
});

	function createDivFailedTest(contentDiv,className,divParent,divName,file,line,message) { 
		var div = document.createElement('div');
		div.id = divName;
		div.className  = className;
		var content = document.getElementById(divParent);
		div.innerHTML = '<p class="left">'+contentDiv+'</p>'
				+'<p class="red">+'+line+'</p>'
				+'<p>'+file+'</p>'
				+'<p>'+message+'</p>'
				+'<button type="button" onclick="hideCode()">Hide/Show test code</button>';
		content.appendChild(div);

		createDiv("code","testInfo totalTests box",divName);
	}

	function createDiv(contentDiv,className,divParent,divName) { 
		var div = document.createElement('div');
		div.id = divName;
		div.className  = className;
		var content = document.getElementById(divParent);
		div.innerHTML = '<p >'+contentDiv+"</p>";
		content.appendChild(div);
	}












	




