$(document).ready(function(){

	
	$("#hideTestsOK").click(function(){
		$(".testOK").slideToggle();

	});
	
	
	$( "#codeTest0" ).click(function(){
		$(".testInfo").slideToggle();

	});

	$("#codeTest0").click(function(){
		procesar();
	});

	procesar = function (){
		$.ajax({
			url:  'http://localhost/view/lucia.php',
			type: 'POST',
		    async: true,	
			data: 'parametro=AQUI',
				
			success: function(request){
				
				$('.testInfo').html('ese: '+request);
			},
			
			error: function(){
				
				alert("falla");
			}
		});
	}
	
	
	$("#runAllTests").click(function(){
		runAllTests();
	});

	runAllTests = function (){
		$.ajax({
			url:  'http://localhost/view/lucia.php',
			type: 'POST',
		    async: true,	
			data: 'parametro=RUN',
				
			success: function(request){
				
				$('#title').html('ese: '+request);
			},
			
			error: function(){
				
				alert("falla");
			}
		});
	}
	
	
});
	var divFailedTest='esto deberia cambiar';
	var count = 0;
	var idButton = "codeTest";
	
	function createDivFailedTest(contentDiv,className,divParent,divName,file,line,message,code) { 
		divFailedTest = divName;
		idButton = "codeTest"+count;
		
		var div = document.createElement('div');
		div.id = divName;
		div.className  = className;
		var content = document.getElementById(divParent);
		div.innerHTML = '<p class="left">'+contentDiv+'</p>'
				+'<p class="red">+'+line+'</p>'
				+'<p>'+file+'</p>'
				+'<p>'+message+'</p>'
				+'<button type="button" id='+idButton+'  >Hide/Show test code</button>';
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












	




