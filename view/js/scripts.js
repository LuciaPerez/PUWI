var count = 0;
var idButton = "codeTest";
var idTest = "idTest";
function createDivFailedTest(contentDiv,className,divParent,divName,file,line,message,code) { 
	divFailedTest = divName;
	idButton = "codeTest"+count;
	idTest = "idTest"+count;
	
	var div = document.createElement('div');
	div.id = divName;
	div.className  = className;
	var content = document.getElementById(divParent);
	
	div.innerHTML = '<p class="left">'+contentDiv+'</p>'
			+'<p class="red">+'+line+'</p>'
			+'<p>'+file+'</p>'
			+'<p>'+message+'</p>'
			+'<button type="button" id='+idButton+' >Hide/Show test code</button>';
	
	content.appendChild(div);
	
	createDiv(code,"testInfo totalTests box",divName);
	count = count + 1;
	
}

function createDiv(contentDiv,className,divParent,divName) { 
	var div = document.createElement('div');
	div.id = divName;
	div.className  = className;
	var content = document.getElementById(divParent);
	div.innerHTML = '<p >'+contentDiv+"</p>";
	content.appendChild(div);
}


$(document).ready(function(){

	$("#hideTestsOK").click(function(){
		$(".testOK").slideToggle();

	});
	
	
	$( "#codeTest0" ).click(function(){
		$(".testInfo").slideToggle();

	});
	
	
	$("#runAllTests").click(function(){
		runAllTests();
	});

	runAllTests = function (){
		$.ajax({
			url:  'http://localhost/PUWI/view/prueba.php',
			dataType: "json",
			type: 'POST',
		    async: true,	
			data: {parametro:'RUN'},
				
			success: function(request){
				 
				$('#title').html('request: '+request['probando']+request['whoami']);
			},
			
			error: function(){
				
				alert("falla");
			}
		});
	}
	

	
});

	












	




