var images=new Array(new Array(template_directory+'/css/img/cursos-i-proves.png','http://hipolit2.xtec.cat/blocs'), new Array(template_directory+'/css/img/help.png','index.php?a=help'))
var currentImage=0;
function changeImage(){
	setTimeout("changeImage()",5000);
  	$('espaicursos').setStyle({'background-image': 'url('+images[currentImage][0]+')'});
	$('espaicursoslink').href=images[currentImage][1];
	currentImage++;
	if (currentImage==images.length) currentImage=0;

  }