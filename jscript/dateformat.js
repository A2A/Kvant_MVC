function SetDate(dateObj)
{                        
	//var Val = DateVal*1000;
	//alert(dateObj);
	var ThisDate = new Array();
	 
	
	ThisDate['Day'] = dateObj.getDate();
	if (ThisDate['Day'] < 10) ThisDate['Day'] = '0' + ThisDate['Day'];
	
	ThisDate['Month'] = dateObj.getMonth() + 1;
	if (ThisDate['Month'] < 10) ThisDate['Month'] = '0' + ThisDate['Month'];
	
	ThisDate['Year'] = dateObj.getFullYear();
	
	ThisDate['Hour'] = dateObj.getHours();
	if (ThisDate['Hour'] < 10) ThisDate['Hour'] = '0' + ThisDate['Hour'];
	
	ThisDate['Minutes'] = dateObj.getMinutes();
	if (ThisDate['Minutes'] < 10) ThisDate['Minutes'] = '0' + ThisDate['Minutes'];
	dateObj = null; 
	
	return ThisDate;
}

function EchoDate(DateVal, ElementId)
{                        
	
	dateObj = new Date(DateVal*1000); 
   
	DateW = SetDate(dateObj);   
   
	OutDate =   DateW['Hour'] + ":" + DateW['Minutes'] + " " +DateW['Day'] + "." + DateW['Month'] + "." + DateW['Year'];
	document.getElementById(ElementId).innerHTML = OutDate;
	
  
}

function EchoFormDate(DateVal, ElementId)
{                        
	dateObj = new Date(DateVal*1000);   
	DateW = SetDate(dateObj);
	OutDate = '<input class="input_timedate2" maxlength=2 value="' + DateW['Hour'] + '">:<input class="input_timedate2" value="' + DateW['Minutes'] + '">' + 
			  '&nbsp;&nbsp;&nbsp;&nbsp;<input class="input_timedate2" value="' + DateW['Day'] + '">' + 
				'.<input class="input_timedate2" value="' + DateW['Month'] + '">' + 
				'.<input class="input_timedate4" value="' + DateW['Year'] + '">';
	
	document.getElementById(ElementId).innerHTML = OutDate;
}

function EchoDuration(Duration, StartDate, FinishDate)
{
	if (StartDate == 0) 
	{
		document.getElementById('Duration').value = "хз";
	}
		
}

function TraceDate()
{
	if (document.getElementById('InitDate'))
	{
		EchoDate(document.getElementById('InitDateValue').value, 'InitDate'); 
	}
	   
	if (document.getElementById('StartDate'))
	{  
		EchoFormDate(document.getElementById('StartDateValue').value, 'StartDate'); 
	}
	   
	if (document.getElementById('FinishDate'))
	{  
		EchoFormDate(document.getElementById('FinishDateValue').value, 'FinishDate'); 
	}
	
	if (document.getElementById('Duration'))
	{  
		EchoDuretion(document.getElementById('Duration').value, document.getElementById('StartDateValue').value, document.getElementById('FinishDateValue').value); 
	}
}

function Calendar(event)
{
	SetEvent(event.clientX + document.body.scrollLeft, event.clientY + document.body.scrollTop) ;     
	document.getElementById('ModalWindow').innerHTML =
	'<table border="0" cellspacing="0" cellpadding="0" class="CalendarWindow" id="CalendarWindow">'+
'<tr>'+
	'<td class="BorderWindow"><img src="images/info-top-left.png" hspace="0" vspace="0" border="0"></td>'+
	'<td style="background: url(\'images/info-top.png\') top;"  class="InfoHeader" valign="top">'+
	'<img src="images/info-close.png" align="right" style="margin-top:-8px;" onClick="CloseWindow(\'CalendarWindow\');">'+
	'<nobr><img src="images/calendar-date.png" hspace="0" vspace="0" border="0"> Дата</nobr>'+
	'</td>'+
	'<td class="BorderWindow"><img src="images/info-top-right.png" hspace="0" vspace="0" border="0"></td>'+
	'</tr>'+
	'<tr>'+
	'<td style="background: url(\'images/info-left.png\') repeat-y;font-size:4px;">&nbsp;</td>'+
	'<td class="InfoBody" valign="top">'+
	'<!-- Календарь -->'+
	'<table border="0" cellspacing="0" cellpadding="0" >'+
		'<tr>'+
			'<td colspan="4">'+
				'<input type="text" class="MonteSelect" value="{?Object=this&Var=this(Monte)}">'+
			'</td> '+
			'<td colspan="3"> '+
				'<input type="text" class="YearSelect" value="{?Object=this&Var=this(Year)}"> '+
			'</td>'+ 
		'</tr>'+
		
	' </table>'+
	'</td>'+
	'<td style="background: url(\'images/info-right.png\') repeat-y right;">&nbsp;</td> '+
	'</tr>'+
	'<tr>'+
	'<td><img src="images/info-bottom-left.png" hspace="0" vspace="0" border="0"></td>'+
	'<td style="background: url(\'images/info-bottom.png\') bottom;font-size:4px;">&nbsp;</td>'+
	'<td><img src="images/info-bottom-right.png" hspace="0" vspace="0" border="0"></td>'+
	'</tr>'+
'</table>';
OpenModalWindow();   
}
