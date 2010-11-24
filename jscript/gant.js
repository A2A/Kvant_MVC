function FiltrVisible()
{
	alert("FiltrVisible() : отобразить фильт в таблице.");
}

var T_eventX;
var T_eventY;

function SetEvent(X,Y)
{
	document.getElementById('ModalWindow').style.position = 'absolute';       
	T_eventX = X;  
	T_eventY = Y;  
}

function OpenModalWindow()   
{
	document.getElementById('ModalWindow').style.display = 'block'; 
	document.getElementById('ModalWindow').style.top = T_eventY;
	document.getElementById('ModalWindow').style.left = T_eventX;
}
function VisibleNewProject(event)
{
	SetEvent(event.clientX + document.body.scrollLeft, event.clientY + document.body.scrollTop);     
	document.getElementById('ModalWindow').innerHTML =
	'<table border="0" cellspacing=0 cellpadding=0>'+
	'<tr>'+
	'<td><img src="images/modal-top-left.png" hspace="0" vspace="0" border="0"></td>'+
	'<td class="RoundTop" >&nbsp;</td>'+
	'<td><img src="images/modal-top-right.png" hspace="0" vspace="0" border="0"></td>'+
	'</tr>'+
	'<tr>'+
	'<td class="RoundLeft">&nbsp;</td> '+
	'<td style="background: #ffffff;" valign="top" width="180px">'+
	'<li><a href="#" onclick="NewTask();">Новую задачу</a></li>'+
	'<li><a href="#" onclick="NewProject();">Новый проект</a></li>'+
	'</td>'+
	'<td class="RoundRight" >&nbsp;</td>'+
	'</tr>'+
	'<tr>'+
	'<td><img src="images/modal-bottom-left.png" hspace="0" vspace="0" border="0"></td>'+
	'<td class="RoundBottom" >&nbsp;</td>'+
	'<td><img src="images/modal-bottom-right.png" hspace="0" vspace="0" border="0"></td>'+
	'</tr>'+
	'</table>';

	OpenModalWindow();
}


function CatchProjectInfo(Text)
{
	Res = ParseStatusXML(Text,'');
	if (Res['SystemStatus'] == 0)
		{
		document.getElementById('ModalWindow').innerHTML = Text; 
		document.getElementById('ModalWindow').style.display = 'block'; 
		document.getElementById('ModalWindow').style.top = T_eventY;
		document.getElementById('ModalWindow').style.left = T_eventX;
	} 
}

function VisibleStatusEditProject(ID,event)
{
	Url = "?Ajax=1&Object=Project&Form=view_status&ID=" + ID;
	SetEvent(event.clientX + document.body.scrollLeft, event.clientY + document.body.scrollTop) ;
	AjaxSendGET(Url,CatchProjectInfo);

}

function VisibleStatusEditTask(ID,event)
{
	Url = "?Ajax=1&Object=Task&Form=view_status&ID=" + ID;
	SetEvent(event.clientX + document.body.scrollLeft, event.clientY + document.body.scrollTop) ;
	AjaxSendGET(Url,CatchProjectInfo);
} 

function GetProjectInfo(ID,event)
{
	Url = "?Ajax=1&Object=Project&Form=view_short&ID=" + ID;
	SetEvent(event.clientX + document.body.scrollLeft, event.clientY + document.body.scrollTop) ;
	AjaxSendGET(Url,CatchProjectInfo);
}

function GetTaskInfo(ID,event)
{
	Url = "?Ajax=1&Object=Task&Form=view_short&ID=" + ID;
	SetEvent(event.clientX + document.body.scrollLeft, event.clientY + document.body.scrollTop) ;  
	AjaxSendGET(Url,CatchProjectInfo);
}

function GetEventInfo(ID,event)
{
	Url = "?Ajax=1&Object=Event&Form=view_short&ID=" + ID;
	SetEvent(event.clientX + document.body.scrollLeft, event.clientY + document.body.scrollTop) ;
	AjaxSendGET(Url,CatchProjectInfo);
}


function DivHide(DivId)
{
	document.getElementById(DivId).style.display = 'none';   
}



function SetInterval(ID)
{ 
	params = "?Ajax=1&Object=System&Action=SetInterval&ID=" + ID;
	Text = AjaxSendPOSTSync(params);
	Res = ParseStatusXML(Text,'Изменение интервала');
	if (Res['ActionStatus'] == 0) location.reload();
	return 1;
}

function ShiftInterval(Direction)
{ 
	params = "?Ajax=1&Object=System&Action=ShiftInterval&Direction=" + (Direction*5);
	Text = AjaxSendPOSTSync(params);
	Res = ParseStatusXML(Text,'');
	if (Res['ActionStatus'] == 0) location.reload();
	return 1;
}

function SetStatusTask(StatusID,TaskID)
{
	params = "?Ajax=1&Object=Task&Action=SetStatus&TaskID=" + TaskID + "&StatusID=" + StatusID;
	Text = AjaxSendPOSTSync(params);
	Res = ParseStatusXML(Text,'');
	return 1;
} 

function SetStatusProject(StatusID,ProjectID)
{
	Url = "?Ajax=1&Object=Project&Action=SetStatus&ProjectID=" + ProjectID + "&StatusID=" + StatusID;
	Text = AjaxSendPOSTSync(params);
	Res = ParseStatusXML(Text,'');
	return 1;
}

function SetProcentTask(Procent,TaskID)
{
	Url = "?Ajax=1&Object=Task&Action=SetProcent&TaskID=" + TaskID + "&Procent=" + Procent;
	Text = AjaxSendPOSTSync(params);
	Res = ParseStatusXML(Text,'');
	return 1;
} 

function SetProcentProject(Procent,ProjectID)
{
	params = "?Ajax=1&Object=Project&Action=Save&ID=" + ProjectID + "&ReadyState=" + Procent;
	Text = AjaxSendPOSTSync(params);
	Res = ParseStatusXML(Text,'');
	return 1;
}

function FilterGant()
{
	if (document.getElementById('Filter').style.display != 'block')
		document.getElementById('Filter').style.display = 'block';
	else
		document.getElementById('Filter').style.display = 'none';   
}