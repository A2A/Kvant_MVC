
function AjaxSendPOSTSync(params)
{
    var http = new XMLHttpRequest();
    var url = "index.php";  
    http.open("POST", url, false); 
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.setRequestHeader("Content-length", params.length);
    http.setRequestHeader("Connection", "close");
    http.send(params);   
    return DeleteServiceCharacter(http.responseText)    
}
function AjaxSendPOST(params,CollbackFunc)
{
    var http = new XMLHttpRequest();
    var url = "index.php";  

    http.onreadystatechange = function() 
    {   
        if(http.readyState == 4 && http.status == 200)
        {   
            CollbackFunc(DeleteServiceCharacter(http.responseText));
        } 
    }  

    http.open("POST", url, true); 
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.setRequestHeader("Content-length", params.length);
    http.setRequestHeader("Connection", "close");

    http.send(params);   
    
    
}

function AjaxSendGET(url,CollbackFunc)    
{
    
    var http = new XMLHttpRequest();
    http.onreadystatechange = function() 
    {   
        if(http.readyState == 4 && http.status == 200) 
            {   
            CollbackFunc(DeleteServiceCharacter(http.responseText));
        }   
/*        if(http.readyState == 1 && http.status >= 400) 
            {   
            // Сообщить о таймауте
        }   
*/    
    } 
    http.open("GET", url, true);
    http.send(null);
}

function DeleteServiceCharacter(Text)
{
    Text = Text.replace(/\r*/ig, "");
    Text = Text.replace(/\n/ig, "");
    Text = Text.replace("  ", " ");
    return Text;
}
