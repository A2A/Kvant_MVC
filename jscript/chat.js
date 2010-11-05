
function ListMessageChat()
{
    AjaxSendGETADD('Object=Chat&Form=Message','ListMessageChat'); 
    window.setTimeout("ListMessageChat()",10000); 
    scroll_clipper=document.getElementById('ListMessageChat');
     var h = scroll_clipper.scrollHeight; 
    var c = scroll_clipper.clientHeight; 
    var y = scroll_clipper.scrollTop;
    var scrollBottom = h -  c; 
    if(scrollBottom>0) scroll_clipper.scrollTop=scrollBottom;     
   
}

function UsersOnline()
{
    AjaxSendGET('Object=Chat&Form=Users','UsersOnline'); 
    window.setTimeout("UsersOnline()",10000); 
}

function history()
{   
   var dhtmlMessage=dhtmlmodal.open("agebox", "iframe", "index_ajax.php?Object=Chat&Form=List", 'История сообщений', 'width=600px,height=600px,left=170px,top=100px,resize=0,scrolling=1', "recal")
      dhtmlMessage.show();  
       return false;  
//}

}

window.setTimeout("ListMessageChat()",10000); 
window.setTimeout("UsersOnline()",10000); 

