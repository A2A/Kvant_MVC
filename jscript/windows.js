var TimeCloseModal = 10000; // милисекунды, 1 сек = 1000
var SystemStatus = 0;
var ActionStatus = 0;
var OpenNum = 0;
var ModalWindowOpen = 'none'; // none - нет активных форм для заолнения, переход на ганд; task - заполняем форму задачи, event - запоняем форму события.

function ShowAllMessages()
{
    for (i=1; i<=OpenNum; i++)
        document.getElementById('InfoWindow'+i).style.display = 'block';     
}

function ShowMessage(Header,Message,Status)
{
    WindowID = CreateDialogWindow();
    if (Status == 'info')   InfoWindow(Header,Message,WindowID);
    if (Status == 'notice') WarningWindow(Header,Message,WindowID);
    if (Status == 'error')  ErrorWindow(Header,Message,WindowID);
    if (Status == 'fatal')  ErrorWindow(Header,"<span style='color:#af0000;'>"+Message+"</span>",WindowID);
}

function ParseStatusXML(Messages,Header)
{
    ReturnRes = new Array();
    ReturnRes['ActionStatus'] = 0;
    ReturnRes['SystemStatus'] = 0;
    
    regexpSystemStatus  = /<SystemStatus>(.+)<\/SystemStatus>/ ; 
    if(regexpSystemStatus.test(Messages))
    {       
        result = regexpSystemStatus.exec(Messages);  
        ReturnRes['SystemStatus'] = result[1]; 
    }; 
    regexpSystemStatus = null;

    regexpResultStatus  = /<ActionStatus>(.+)<\/ActionStatus>/ ; 
    if(regexpResultStatus.test(Messages))
    {    
        result = regexpResultStatus.exec(Messages);  
        ReturnRes['ActionStatus'] = result[1]; 
    };
    regexpResultStatus  = null;
     
    regexpFieldList     = /<ChangedFields>(.+)<\/ChangedFields>/ ; 
    regexpField         = /Name="(.+)">(.+)<\/Field>/; 
    if(regexpFieldList.test(Messages))
    {
        result = regexpFieldList.exec(Messages);  
        FieldList = result[1].split('<Field'); 

        Count = FieldList.length;
        for(i=1;i<Count;i++)
        {
            if(regexpField.test(FieldList[i]))
            {
                result = regexpField.exec(FieldList[i]);
                if (document.getElementById(result[1]))  
                    document.getElementById(result[1]).value =  result[2]; 
            } 
        }

    };
    regexpFieldList     = null; 
    regexpField         = null; 

    regexpMessageList   = /<Messages>(.+)<\/Messages>|<messages>(.+)<\/messages>|<MESSAGES>(.+)<\/MESSAGES>/i ; 
    regexpMessage       = /status="(.+)">(.+)/; 
    if(regexpMessageList.test(Messages))
    {    
        result = regexpMessageList.exec(Messages);  
        MessageList = result[1].split('<'); 
         
        Count = MessageList.length;
        for(i=1;i<Count;i++)
        {   
            if(regexpMessage.test(MessageList[i]))
            {
                result = regexpMessage.exec(MessageList[i]);
                ShowMessage(Header,result[2],result[1])
            }
        }
    }

    regexpMessageList   = null; 
    regexpMessage       = null; 
    
    return ReturnRes;
} 

function WarningMessageOnLoad(DivErrorMess)
{
    var ErrorMess = document.getElementById(DivErrorMess).innerHTML;
    ErrorMess = DeleteServiceCharacter(ErrorMess);
    if (ErrorMess.length > 100)
    {
            ParseStatusXML(ErrorMess,'Загрузка страницы','');
    } 
}

function CreateDialogWindow()
{
    OpenNum = OpenNum + 1; 
    Div = '<table border="0" cellspacing="0" cellpadding="0" class="InfoWindow" id="InfoWindow'+OpenNum+'">'+
            '<tr>'+
                '<td class="BorderWindow"><img src="images/info-top-left.png" hspace="0" vspace="0" border="0"></td>'+
                '<td style="background: url(\'images/info-top.png\') top;"  class="InfoHeader" valign="top">'+
                    '<img src="images/info-close.png" align="right" style="margin-top:-8px;" onClick="CloseWindow(\'InfoWindow'+OpenNum+'\');">'+ 
                    '<nobr id="ModalInfoHeader'+OpenNum+'"></nobr>'+
                '</td>'+
                '<td class="BorderWindow"><img src="images/info-top-right.png" hspace="0" vspace="0" border="0"></td>'+
            '</tr>'+
            '<tr>'+
                '<td style="background: url(\'images/info-left.png\') repeat-y;font-size:4px;">&nbsp;</td>'+
                '<td id="ModalInfoBody'+OpenNum+'" class="InfoBody" valign="top"></td>'+
                '<td style="background: url(\'images/info-right.png\') repeat-y right;">&nbsp;</td>'+
            '</tr>'+
            '<tr>'+
                '<td><img src="images/info-bottom-left.png" hspace="0" vspace="0" border="0"></td>'+
                '<td style="background: url(\'images/info-bottom.png\') bottom;font-size:4px;">&nbsp;</td>'+
                '<td><img src="images/info-bottom-right.png" hspace="0" vspace="0" border="0"></td>'+
            '</tr>'+
          '</table>';
          
    document.getElementById('InfoWindow').innerHTML = document.getElementById('InfoWindow').innerHTML + Div;  
    return OpenNum;    
}

function InfoWindow(Header,Body,Check)
{
    document.getElementById('ModalInfoHeader'+Check).innerHTML = '<img src="images/info.png" align="left"> ' + Header; 
    document.getElementById('ModalInfoBody'+Check).innerHTML = Body;      
    OpenInfoWindow(Check);     
}

function ErrorWindow(Header,Body,Check)
{
    document.getElementById('ModalInfoHeader'+Check).innerHTML = '<img src="images/error.png" align="left"> ' + Header; 
    document.getElementById('ModalInfoBody'+Check).innerHTML = Body; 
    OpenInfoWindow(Check);
}

function WarningWindow(Header,Body,Check)  
{
    document.getElementById('ModalInfoHeader'+Check).innerHTML = '<img src="images/warning.png" align="left"> ' + Header; 
    document.getElementById('ModalInfoBody'+Check).innerHTML = Body;  
    OpenInfoWindow(Check);
    
}

function OpenInfoWindow(Check)
{
    document.getElementById('InfoWindow').style.display = 'block'; 
    window.setTimeout("CloseWindow('InfoWindow"+Check+"')",TimeCloseModal+(Check*200));  
}

function CloseWindow(DivBlock)  
{
     document.getElementById(DivBlock).style.display = 'none';
}

/*=============================================================================
*/
