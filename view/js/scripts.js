
num=0; 
function createDiv(fil) { 
  obj=fil.form; 
  tab = document.createElement('div');
  tab.id = 'calendario';
  tab.style.padding = "1px";
  tab.style.border = "1px solid red";
  tab.style.background = "blue";
  obj.appendChild(tab); 
} 

