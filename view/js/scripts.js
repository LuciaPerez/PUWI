$(document).ready(function(){

	    $("button").click(function(){
	    $(".testOK").slideToggle();
	    });
	
});

	function createDiv(contentDiv,className) { 
		var div = document.createElement('div');
		div.className  = className;
		var content = document.getElementById('content');
		div.innerHTML = "<p>"+contentDiv+"</p>";
		content.appendChild(div);
	}

	function prueba (){
		alert("Probando...");
	}




	




