var xmlhttp = false;
try {
    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
    try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }catch (E){
        xmlhttp = false;
    }
}
if(!xmlhttp && typeof XMLHttpRequest != 'undefined'){
    xmlhttp = new XMLHttpRequest();
}

function autocomplete(thevalue,e){
    theObject=document.getElementById("autocompletediv");
    theObject.style.visibility="visible";
    
    var theextrachar=e.which;
    if(theextrachar == undefined){
        theextrachar = e.keyCode
    }
    
    //Position of the object in the page
    var objID = "autocompletediv";
    
    //If char is Backspace
    if(theextrachar == 8){
        if(thevalue.length == 1){
            var serverPage="../wp-content/plugins/xtec-descriptors/autocomp.php?sstring=";
        }else{
            var serverPage="../wp-content/plugins/xtec-descriptors/autocomp.php" + "?sstring=" + thevalue.substr(0, (thevalue.length -1));
        }
    }else{
        var serverPage="../wp-content/plugins/xtec-descriptors/autocomp.php" + "?sstring=" + thevalue + String.fromCharCode(theextrachar);
    }
    var obj = document.getElementById(objID);
    xmlhttp.open("GET",serverPage);
    xmlhttp.onreadystatechange=function(){
        if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
            obj.innerHTML = xmlhttp.responseText;
        }
    }
    xmlhttp.send(null);
}

function setvalue(thevalue){
    acObject = document.getElementById("autocompletediv");
    acObject.style.visibility = "hidden";
    document.getElementById("descriptor").value=thevalue;
}
