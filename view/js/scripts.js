var count = 0;
var idTest = "idTest";
function createDivFailedTest(contentDiv,className,divParent,divName,file,line,message,code) { 
	idTest = "idTest"+count;
	
	var div = document.createElement('div');
	div.id = divName;
	div.className  = className;
	var content = document.getElementById(divParent);

	
	div.innerHTML = '<p class="nameFT left bold">'+contentDiv+'</p>'
			+'<p class="red bold">+'+line+'</p>'
			+'<p class="fileFT">'+file+'</p>'
			+'<p class="italic">'+message+'<input type="image" src="images/bullet_arrow_down1.png" title="Show code" class="classButton" data-idtest='+"#"+idTest+'></p>';
	
	content.appendChild(div);

	createDiv(code,"testInfo totalTests box",divName,idTest);
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
	
	
	$( ".classButton" ).click(function(){
		var idTest = $(this).data('idtest');
		$(idTest).slideToggle();
		
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

	












	




