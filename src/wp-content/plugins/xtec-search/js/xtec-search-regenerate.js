
function nextpage() 
{
    var n = getURLParam("&n=") + 30;
    location.href="?page=ms-search&action=search&n="+n;
}

function getURLParam(strParamName){
    var strReturn = "";
    strReturn = location.search.substring(0);
    pos = strReturn.indexOf(strParamName, 0);
    if (pos != -1)
        strReturn = strReturn.substring(pos+strParamName.length);
    else
        strReturn = 0;
    return parseInt(strReturn, 10);
}


setTimeout("nextpage()", 2500);
