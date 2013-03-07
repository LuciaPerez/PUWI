$(document).ready(function(){
	function exampleJQuery()
	{
	    $("p").click(function(){
	    $(".classTest").hide();
	    });
	}
});

	function createDiv(contentDiv,className) { 
		var div = document.createElement('div');
		div.className  = className;
		var content = document.getElementById('content');
		div.innerHTML = "<p>"+contentDiv+"</p>";
		content.appendChild(div);
	}




