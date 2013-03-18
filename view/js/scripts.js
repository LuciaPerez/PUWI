$(document).ready(function(){

	hideTestOK = function(){
		$(".testOK").slideToggle();
	}
	
});

	function createDiv(contentDiv,className,divParent,divName) { 
		var div = document.createElement('div');
		div.id = divName;
		div.className  = className;
		var content = document.getElementById(divParent);
		div.innerHTML = "<p>"+contentDiv+"</p>";
		content.appendChild(div);

	}







	




