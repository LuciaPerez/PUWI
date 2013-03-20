$(document).ready(function(){

	hideTestOK = function(){
		$(".testOK").slideToggle();

	}
	
});

	function createDivFailedTest(contentDiv,className,divParent,file,line,message) { 
		var div = document.createElement('div');
		div.className  = className;
		var content = document.getElementById(divParent);
		div.innerHTML = '<p>'+contentDiv+"</p>"+'<p>'+file+"</p>"+'<p class="red">'+line+"</p>"+'<p>'+message+"</p>";
		content.appendChild(div);

	}

	function createDiv(contentDiv,className,divParent,divName) { 
		var div = document.createElement('div');
		div.id = divName;
		div.className  = className;
		var content = document.getElementById(divParent);
		div.innerHTML = '<p>'+contentDiv+"</p>";
		content.appendChild(div);

	}










	




