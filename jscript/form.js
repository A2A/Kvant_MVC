function GantRefresh()
{
	TempHref = window.location.href;
	
	if( TempHref.search(/\?/) > 0) TempHref = TempHref.replace("#", "&"); 
	else  TempHref = TempHref.replace("#", "\?&");  
	
	location.href = TempHref;
}

function OnPressEnterLogIn(event)
{
	if(event.keyCode==13) 
	   SendLogIn(); 
}

function OnPressKeyBody(event)
{
	if(event.keyCode==27)  
	{
		if (document.getElementById('Calendar').style.display == 'block')
		{
			DivHide('Calendar');
			return 1;
		} 
		if (document.getElementById('CurrentDRU').style.display == 'block')
		{
			DivHide('CurrentDRU');
			return 1;
		} 
		if (document.getElementById('NoActionDesktop').style.display == 'block')
		{
			CloseModalWindow();
			return 1;
		} 

		
	}
		
}

function OnClickBody(event)
{
	//if (document.getElementById('FullListEventType').style.display == 'block')
	{
		//alert('FullListEventType');
		//DivHide('FullListEventType');
		return 1;
	} 
	
}

function SendLogIn()
{
	LoginText = document.getElementById('Login').value;
	PasswordText = document.getElementById('Password').value;
	SavePassFlag = (document.getElementById('Memor').value='On'?1:0);
	
	params = "Object=System&Action=login&Login="+LoginText+"&Password="+PasswordText+"&SavePass="+SavePassFlag+"";
	Text = AjaxSendPOSTSync(params);
	Res = ParseStatusXML(Text,'');
	if (Res['ActionStatus'] == 0) 
	{
		location.reload();
	}
	else
	{
	}
		
}

function SendLogout()
{
	params = "Object=System&Action=logout";
	
	Text = AjaxSendPOSTSync(params);
	Res = ParseStatusXML(Text,'');
	if (Res['ActionStatus'] == 0) location.reload();
}

function SetDRU(ID)
{
	params = "Object=System&Action=SetDRU&ID="+ID;
	
	Text = AjaxSendPOSTSync(params);
	Res = ParseStatusXML(Text,'');
	//if (Res['ActionStatus'] == 0) location.reload();
	document.getElementById('CurrentDRU').style.display='none';
	GantRefresh();
}


/*=============================================================================
*/

function DrowHelpWindow(text)
{
	 WindowId = CreateDialogWindow();  
	 InfoWindow("Помощь",text,WindowId);  
}

function DrowPassRequestWindow(text)
{
	document.getElementById('NoActionDesktop').style.display = "block"; 
	document.getElementById('ActionDesktop').style.display = "block"; 
	
	document.getElementById('NoActionDesktop').style.height = document.getElementById('MainPage').style.height; 
	document.getElementById('ActionDesktop').style.height = document.getElementById('MainPage').style.height; 
   
	document.getElementById('ActionDesktop').innerHTML = text;  
}

function Help()
{
	Url = "?Object=system&Form=login_help&Ajax=1";
	AjaxSendGET(Url,DrowHelpWindow); 
}

function UpdatePassword()
{
	Url = "?Object=system&Form=update_password&Ajax=1";
	AjaxSendGET(Url,DrowPassRequestWindow); 
}

function SelectStaff(Text)
{
	document.getElementById('UserDiv').style.display = 'block'; 
	document.getElementById('UserDiv').innerHTML = Text;
	MaxTdNum = document.getElementById('CountTr').innerHTML - 1;
	TdNum = -1;
	//alert("form.js SelectContractor()");       
}
/**********************************************************************/

/**************** Авто подсказка ***************************************/
function SelectContractor(Text)
{
	document.getElementById('ContractorDiv').style.display = 'block'; 
	document.getElementById('ContractorDiv').innerHTML = Text;
	//alert("form.js SelectContractor()");       
}

var TdNum = -1;
var MaxTdNum = 3;

function AutoSelectInText(ObjId,event,ObjectName,Filtered)
{
	if(event.keyCode==38 || event.keyCode==40 || event.keyCode==37 || event.keyCode==39 || event.keyCode==32 || event.keyCode==13)
	{
		SelectScroll(event,ObjectName);
	}
	else
	{
		if (ObjectName == 'User')
		{
			Url = "?Ajax=1&Object=UserList&Form=selectbox&Enc=Рус";
			if (Filtered) Url = Url + "&Filter[1][Field]=Description&Filter[1][Oper]=like&Filter[1][Val]=" + ObjId.value;
			if (Filtered || document.getElementById('UserDiv').style.display!='block') 
			{
				AjaxSendGET(Url,SelectStaff);
			}
			else
			{
				document.getElementById('UserDiv').style.display = 'none';
				document.getElementById('UserDiv').innerHTML = '';
			}
		}
		if (ObjectName == 'Contractor')
		{
			Url = "?Ajax=1&Object=ContractorList&Form=selectbox&Enc=Рус";
			if (Filtered) Url = Url + "&Filter[1][Field]=Description&Filter[1][Oper]=like&Filter[1][Val]=" + ObjId.value;
			if (Filtered || document.getElementById('ContractorDiv').style.display!='block') 
			{
				AjaxSendGET(Url,SelectContractor);
			}
			else
			{
				document.getElementById('ContractorDiv').style.display = 'none';
				document.getElementById('ContractorDiv').innerHTML = '';
			}
		}
	} 
}


function SelectScroll(event,ObjectName)
{ 

	if(event.keyCode==38 || event.keyCode==40 || event.keyCode==37 || event.keyCode==39 || event.keyCode==32 || event.keyCode==13)
	{ 
		MaxTdNum = document.getElementById('tdCount').innerHTML; 
	
		if(event.keyCode==40) 
		{  
			i = TdNum;
			if(i >= 0 && (i + 1 < MaxTdNum)) document.getElementById('td'+i).style.background="#ffffff"; 
			if (i + 1 < MaxTdNum) 
				{
					document.getElementById('td'+(i+1)).style.background="#f9fbff"; 
					document.getElementById(ObjectName).value = document.getElementById('td'+(i+1)).innerHTML; 
				}
			if (i + 1 >= MaxTdNum)
				TdNum = MaxTdNum; 
			else
				TdNum = i + 1; 
		}
		if(event.keyCode==38) 
		{  
			i = TdNum; 
			if(i < MaxTdNum && i >= 0) document.getElementById('td'+i).style.background="#ffffff"; 
			if(i > 0) 
				{
					document.getElementById('td'+(i-1)).style.background="#f9fbff"; 
					document.getElementById(ObjectName).value = document.getElementById('td'+(i-1)).innerHTML;  
				} 
			
			if (i - 1 <= 0)      
				TdNum = 0;
			else
				TdNum = i - 1;     
		} 
		if(event.keyCode==37) 
		{                                       
		}; 
		if(event.keyCode==39) 
		{  
		};
		if(event.keyCode==32 || event.keyCode==13) 
		{ 
			SetSelectValue(TdNum,ObjectName);
			 
		};
	}
   
}

function SetSelectValue(ElementId,ObjectName)
{
	document.getElementById(ObjectName).value = document.getElementById('td'+ElementId).innerHTML;   
	document.getElementById(ObjectName+'ID').value = document.getElementById('ValueTd'+ElementId).innerHTML;   
	document.getElementById(ObjectName+'Div').style.display = 'none'; 
	document.getElementById(ObjectName+'Div').innerHTML = ""; 
}  

function FieldClear(ObjectName)
{
	 document.getElementById(ObjectName+'Descr').value = "";   
	 document.getElementById(ObjectName+'ID').value = "null";   
}

/*=============================================================================
*/
function CloseModalWindow()
{
	ModalWindowOpen = 'none';
	document.getElementById('NoActionDesktop').style.display = "none";    
	document.getElementById('ActionDesktop').style.display = "none";
	document.getElementById('ActionDesktop').innerHTML = "";
	//MainPage
}

function CatchTaskOpen(Text)
{
	ModalWindowOpen = 'Task';
	document.getElementById('NoActionDesktop').style.display = "block"; 
	document.getElementById('ActionDesktop').style.display = "block"; 
	document.getElementById('ActionDesktop').innerHTML = Text;
	
   
	TraceDate(); 
	heig = ( (typeof window.innerHeight != 'undefined')? window.innerHeight : document.body.offsetHeight);
	//heig = document.body.clientHeight;
	//document.getElementById('PageMain').style.height = heig+"px"; 
	
}

function CatchTaskShortInfo(Text)
{
	ParseStatusXML(Text,'');
}
function ClickTaskView(ID)
{
	
	ModalWindowOpen = 'none';
	Url = "?Ajax=1&Object=Task&Form=view_full&ID=" + ID;
	AjaxSendGET(Url,CatchTaskOpen);
	return 1;
	
}

function ClickTask(ID,event)
{
	//alert(ModalWindowOpen);
	if (ModalWindowOpen == 'none') 
	{
		Url = "?Ajax=1&Object=Task&Form=edit&ID=" + ID;
		AjaxSendGET(Url,CatchTaskOpen);
		return 1;
	}
	else
	{
		document.getElementById('SelectAction').style.display = "block"; 
		document.getElementById('SelectAction').style.position = "absolute"; 
		document.getElementById('SelectAction').style.top = (event.clientY + document.body.scrollTop - 10); 
		document.getElementById('SelectAction').style.left = (event.clientX + document.body.scrollLeft - 50); 
		document.getElementById('SelectAction').style.zIndex = 2000; 
		document.getElementById('SelectAction').innerHTML = 
		'<table border="0" cellspacing=0 cellpadding=0>'+
	'<tr>'+
		'<td><img src="images/rect-top-left.png" hspace="0" vspace="0" border="0"></td>'+
		'<td class="RectTop">&nbsp;</td>'+
		'<td><img src="images/rect-top-right.png" hspace="0" vspace="0" border="0"></td>'+
	'</tr>'+
	'<tr>'+
		'<td class="RectLeft">&nbsp;</td>'+
		'<td class="InfoBody" valign="top"> '+
			'<a href="#" OnClick="FillFieldTask(' + ID + ');document.getElementById(\'SelectAction\').style.display = \'none\';">Заполнить поле "Задача"</a> <br>'+
			'<a href="#" OnClick="ModalWindowOpen=\'none\';ClickTask(' + ID + ');document.getElementById(\'SelectAction\').style.display = \'none\';">Открыть</a> '+
		'</td> '+
		'<td  class="RectRight">&nbsp;</td> '+
	'</tr> '+
	'<tr>  '+
		'<td><img src="images/rect-bottom-left.png" hspace="0" vspace="0" border="0"></td> '+
		'<td  class="RectBottom">&nbsp;</td> '+
		'<td><img src="images/rect-bottom-right.png" hspace="0" vspace="0" border="0"></td>'+
	'</tr>'+
'</table>';      
		
	}
}

function FillFieldTask(ID)
{
	 Url = "?Ajax=1&Object=Task&Form=ShortInfo&ID=" + ID;
	 AjaxSendGET(Url,CatchTaskShortInfo);
	 return 1;
}

function NewTask()
{
	location.href ="?Object=System&Form=task";
}

function ListTaskEvent(TaskID)
{
	 location.href ="?Object=System&Form=event_list&Filter[0][Field]=TaskID&Filter[0][Oper]=eq&Filter[0][Val]="+TaskID;   
}

function SaveTask()
{
	params = "Object=Task&Action=save";
	if (document.getElementById('ID')) params = params + "&ID="            + document.getElementById('ID').value;
	params = params + "&UserID="            + document.getElementById('UserID').value;
	params = params +  "&ProjectID="    + document.getElementById('ProjectID').value ;
	params = params + "&ParentID="       + document.getElementById('ParentID').value;
	params = params  + "&ContractorID="       + document.getElementById('ContractorID').value;
	params = params  + "&StartDateValue="     + document.getElementById('StartDateValue').value;
	params = params  + "&FinishDateValue="    + document.getElementById('FinishDateValue').value;
	params = params  + "&FullDescription="      + document.getElementById('FullDescription').value;
	if (document.getElementById('ReadyState')) params = params  + "&ReadyState="      + document.getElementById('ReadyState').value;
	if (document.getElementById('Description')) params = params  + "&Description="      + document.getElementById('Description').value;
	if (document.getElementById('RoleID')) params = params  + "&RoleID="      + document.getElementById('RoleID').value;
	if (document.getElementById('UserID')) params = params  + "&UserID="      + document.getElementById('UserID').value;
	params = params  + "";
   
	Text = AjaxSendPOSTSync(params);
	Res = ParseStatusXML(Text,'Сохранение задачи');
	if(Res)
		GantRefresh();
	return 1;
}

function CatchProjectOpen(Text)
{
	ModalWindowOpen = 'none';
	document.getElementById('NoActionDesktop').style.display = "block"; 
	document.getElementById('ActionDesktop').style.display = "block"; 
	document.getElementById('ActionDesktop').innerHTML = Text;
	
   
	TraceDate(); 
	heig = ( (typeof window.innerHeight != 'undefined')? window.innerHeight : document.body.offsetHeight) - 100;
	
	//document.getElementById('PageMain').style.height = heig+"px"; 
	
}

function CatchProjectShortInfo(Text)
{
	ParseStatusXML(Text,'');
}

function ClickProjectView(ID)
{
	ModalWindowOpen = 'none';
	Url = "?Ajax=1&Object=Project&Form=view_full&ID=" + ID;
	AjaxSendGET(Url,CatchProjectOpen);
	return 1;
	
}

function ClickProject(ID,event)
{
	
	if (ModalWindowOpen == 'none') 
	{
		Url = "?Ajax=1&Object=Project&Form=edit&ID=" + ID;
		AjaxSendGET(Url,CatchProjectOpen);
		return 1;
	}
	else
	{
		document.getElementById('SelectAction').style.display = "block"; 
		document.getElementById('SelectAction').style.position = "absolute"; 
		document.getElementById('SelectAction').style.top = (event.clientY + document.body.scrollTop - 10); 
		document.getElementById('SelectAction').style.left = (event.clientX + document.body.scrollLeft - 50); 
		document.getElementById('SelectAction').style.zIndex = 2000; 
		document.getElementById('SelectAction').innerHTML = 
		'<table border="0" cellspacing=0 cellpadding=0>'+
	'<tr>'+
		'<td><img src="images/rect-top-left.png" hspace="0" vspace="0" border="0"></td>'+
		'<td class="RectTop">&nbsp;</td>'+
		'<td><img src="images/rect-top-right.png" hspace="0" vspace="0" border="0"></td>'+
	'</tr>'+
	'<tr>'+
		'<td class="RectLeft">&nbsp;</td>'+
		'<td class="InfoBody" valign="top"> '+
			'<a href="#" OnClick="FillFieldProject(' + ID + ');document.getElementById(\'SelectAction\').style.display = \'none\';">Заполнить поле "Проект"</a> <br>'+
			'<a href="#" OnClick="ModalWindowOpen=\'none\';ClickProject(' + ID + ');document.getElementById(\'SelectAction\').style.display = \'none\';">Открыть</a> '+
		'</td> '+
		'<td  class="RectRight">&nbsp;</td> '+
	'</tr> '+
	'<tr>  '+
		'<td><img src="images/rect-bottom-left.png" hspace="0" vspace="0" border="0"></td> '+
		'<td  class="RectBottom">&nbsp;</td> '+
		'<td><img src="images/rect-bottom-right.png" hspace="0" vspace="0" border="0"></td>'+
	'</tr>'+
'</table>';      
		
	}
}

function NewProject()
{
	location.href = "?Object=System&Form=project";
}

function FillFieldProject(ID)
{
	 Url = "?Ajax=1&Object=Project&Form=ShortInfo&ID=" + ID;
	 AjaxSendGET(Url,CatchProjectShortInfo);
	 return 1;
}

function SaveProject()
{
	params = "?Ajax=1&Object=Project&Action=save";
	if (document.getElementById('ID')) 
		params = params + "&ID="            	+ document.getElementById('ID').value;
	if (document.getElementById('DRUID')) 
		params = params + "&DRUID="           	+ document.getElementById('DRUID').value;
	params = params  + "&ContractorID="       	+ document.getElementById('ContractorID').value;
	params = params  + "&StartDateValue="    	+ document.getElementById('StartDateValue').value;
	params = params  + "&FinishDateValue="    	+ document.getElementById('FinishDateValue').value;
	params = params  + "&FullDescription="      + document.getElementById('FullDescription').value;
	if (document.getElementById('ReadyState')) 
		params = params  + "&ReadyState="      	+ document.getElementById('ReadyState').value;
	if (document.getElementById('TPEClass')) 
		params = params  + "&TPEClass="      	+ document.getElementById('TPEClass').value;
	if (document.getElementById('Description')) 
		params = params  + "&Description="      + document.getElementById('Description').value;
	params = params  + "";
 
	//alert(params);
	Text = AjaxSendPOSTSync(params);     
	
	Res = ParseStatusXML(Text,'Сохранение проекта');
	if(Res)
		GantRefresh();
	return 1;
}


//===============================================================================

var TPETypeId;

function CatchTPECreate(Text)
{
	document.getElementById('NoActionDesktop').style.display = "block"; 
	document.getElementById('ActionDesktop').style.display = "block"; 
	document.getElementById('ActionDesktop').innerHTML = Text;
	
   
	heig = ( (typeof window.innerHeight != 'undefined')? window.innerHeight : document.body.offsetHeight) - 100;
	
	//document.getElementById('PageMain').style.height = heig+"px"; 
}

function CreateNewElement(EventTypeId)
{
	TPETypeId = EventTypeId;
	AjaxSendGET("?Ajax=1&Object=System&Form=current_list_role",CatchTPECreate);  
	document.getElementById('FullListEventType').style.display = 'none';   
}

function ChangeDRU(DRUID)
{
	ModalWindowOpen = document.getElementById('Desktop').value;
	if (ModalWindowOpen == 'Event')
	{
		AjaxSendGET("?Ajax=1&Object=Event&Form=new&DRUID="+DRUID+"&TypeID="+TPETypeId,CatchTPECreate); 
	}
	else if (ModalWindowOpen == 'Project')
	{
		AjaxSendGET("?Ajax=1&Object=Project&Form=new&DRUID="+DRUID+"&TypeID="+TPETypeId,CatchProjectOpen); 
	}
	else if (ModalWindowOpen == 'Task')
	{
		AjaxSendGET("?Ajax=1&Object=Task&Form=new&&DRUID="+DRUID+"&TypeID="+TPETypeId,CatchTaskOpen); 
	}
	//	alert(ModalWindowOpen);
	
}

function EventDelete()
{
	CloseModalWindow();
}

function EventStayBy()
{
	params = "?Ajax=1&Object=Event&Action=Save";
	if (document.getElementById('ID')) 		
		params = params + "&ID="  				+ document.getElementById('ID').value;
	if (document.getElementById('EventTypeID')) 		
		params = params + "&EventTypeID="  		+ document.getElementById('EventTypeID').value;
	if (document.getElementById('DRUID')) 
		params = params + "&DRUID="            	+ document.getElementById('DRUID').value;
	params = params  + "&ProjectID="       		+ document.getElementById('ProjectID').value;
	params = params  + "&TaskID="      		 	+ document.getElementById('ParentID').value;
	params = params  + "&ContractorID="       	+ document.getElementById('ContractorID').value;
	params = params  + "&InitDate="      		+ document.getElementById('InitDate').value;
	params = params  + "&Continue=1";
	if (document.getElementById('Description')) 
		params = params  + "&Description="	+ document.getElementById('Description').value;
	params = params  + "";
 
	
	Text = AjaxSendPOSTSync(params);     
	
	Res = ParseStatusXML(Text,'Сохранение события');
	
	CloseModalWindow();
	EventBlockRefresh();    
	return 1;  
}

function EventConfirm()
{
	params = "?Ajax=1&Object=Event&Action=Save";
	if (document.getElementById('ID')) 		
		params = params + "&ID="  				+ document.getElementById('ID').value;
	if (document.getElementById('EventTypeID')) 		
		params = params + "&EventTypeID="  		+ document.getElementById('EventTypeID').value;
	if (document.getElementById('DRUID')) 
		params = params + "&DRUID="            	+ document.getElementById('DRUID').value;
	params = params  + "&ProjectID="       		+ document.getElementById('ProjectID').value;
	params = params  + "&TaskID="      		 	+ document.getElementById('ParentID').value;
	params = params  + "&ContractorID="       	+ document.getElementById('ContractorID').value;
	params = params  + "&InitDate="      		+ document.getElementById('InitDate').value;
	params = params  + "&Continue=0";
	
	if (document.getElementById('Description')) 
		params = params  + "&Description="      + document.getElementById('Description').value;
	params = params  + "";
 
	
	Text = AjaxSendPOSTSync(params);     
 
	Res = ParseStatusXML(Text,'Сохранение события');
	
	CloseModalWindow();
	EventBlockRefresh();  
	return 1;  
}

function EventBlockRefresh()
{
	// TODO 10 -o Natali -c JS: подумать над тем, что перегружаем при создании события, нужен ли полный рефрешь страницы.
	GantRefresh();
	//location.href="?Object=System&Form=event"; 
}

function ClickEvent(ID,Continue)
{
	
	if (Continue == 1) 
	{
		Url = "?Ajax=1&Object=Event&Form=edit&ID=" + ID;
		AjaxSendGET(Url,CatchTPECreate);
		ModalWindowOpen = 'Event';    
		return 1;
	}
	else
	{
		Url = "?Ajax=1&Object=Event&Form=view&ID=" + ID;
		
		AjaxSendGET(Url,CatchTPECreate);
		return 1;
	}
} 

function NewEvent()
{
	location.href = "?Object=System&Form=event";      
}

function VisibleControlBlock(BlockId)
{   
	if (document.getElementById(BlockId).style.display == 'block') 
		document.getElementById(BlockId).style.display = 'none';  
	else
		document.getElementById(BlockId).style.display = 'block';
   
}

function DesktopPosition(Type,EventId)
{
	if ( Type == 'hand' )  ;
	if ( Type == 'auto' )   ;
	alert("form.js DesktopPosition(Type,EventId)");     
}




   