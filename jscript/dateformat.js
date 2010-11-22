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
	
	if (StartDate >= 0 && FinishDate >=0 && FinishDate > StartDate) 
	{
		var Duration = FinishDate - StartDate;
		dayDuration = Math.floor(Duration / (3600*24));
		S = "";  
		
		Duration1 = Duration - dayDuration * (3600*24);
		HoursDuration = Math.floor(Duration1 / (3600));
		
		Duration2 = Duration1 - HoursDuration * 3600;
		
		
		MinutDuration = Math.floor(Duration2 / (60));
		
		if (dayDuration>0)
			S = S + dayDuration + " д. ";
			
		if (dayDuration>0 || HoursDuration>0)
			S = S + HoursDuration + " ч. ";
			
		if (MinutDuration>0)
			S = S + MinutDuration + " мин. ";
			
		document.getElementById('Duration').value = S;
	}
	else
		document.getElementById('Duration').value = "не установлено даты и время ";       

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
		EchoDuration(document.getElementById('Duration').value, document.getElementById('StartDateValue').value, document.getElementById('FinishDateValue').value); 
	}
}

var Div =  document.getElementById('Calendar');			
var MonthDiv;			
var YearDiv;			
var WeekList;			
var TimeDiv;     						
var NowDay = new Date();			
var WeekClass = new Array();			
WeekClass[0] = "Sunday";			
WeekClass[1] = "Monday";	
WeekClass[2] = "Tuesday";			
WeekClass[3] = "Wednesday";			
WeekClass[4] = "Thursday";			
WeekClass[5] = "Friday";			
WeekClass[6] = "Saturday";			
WeekClass[100] = "Current";	
var ReturnValue;	

function Calendar(event, IdField)
{
	SetEvent(event.clientX + document.body.scrollLeft, event.clientY + document.body.scrollTop) ;     
	Div =  document.getElementById('Calendar');	
	ReturnValue = IdField; 
	if (Div.innerHTML == "")
	{
		Url = "?Object=System&Form=calendar&Ajax=1";
		AjaxSendGET(Url,OpenClendar); 
	}
	else
		OpenClendar(document.getElementById('Calendar').innerHTML);
}

function OpenClendar(Text)
{
	
	document.getElementById('Calendar').innerHTML = Text;
	
	MonthDiv = document.getElementById('SelectCalendarMonth');			
	YearDiv = document.getElementById('YearSelect');			
	WeekList = document.getElementById('WeekList');			
	TimeDiv = document.getElementById('TimeSelect');     						
	if (document.getElementById(ReturnValue).value*1 < 1000000) 
		NowDay = new Date();
	else
		NowDay = new Date(document.getElementById(ReturnValue).value*1000);  			
   
	YearDiv.value = NowDay.getFullYear();			
	SetMonth(NowDay.getMonth()*1 + 1);			
	SetTime(0)  ; 
	document.getElementById('Calendar').style.display = "block";
	document.getElementById('Calendar').style.top = T_eventY;
	document.getElementById('Calendar').style.left = T_eventX - 100;
}


function SendDateForm()			
{				
	SetTimeValue();
	DateUn = NowDay.getTime();	
	
	document.getElementById(ReturnValue).value = Math.round(DateUn / 1000);
				
	TraceDate() ;	
	document.getElementById('Calendar').style.display = "none";     						
}
								
function SetDateForm(DaySet)			
{				
	NowDay.setDate(DaySet);				
	DrawWeekList() ;   			
}

function SetMonth(Id)			
{				
	document.getElementById('SelectCalendarMonth').innerHTML = document.getElementById('Month_'+Id).innerHTML;				
	NowDay.setMonth(Id - 1);				
	DrawWeekList();				
	document.getElementById('FullListEventType').style.display = "none";			
}			

function SetYear(Delta)			
{				
	YearDiv.value = YearDiv.value*1 + Delta*1;				
	NowDay.setYear(YearDiv.value); 				
	DrawWeekList();			
}

function SetYearValue()			
{	
	// DONE 1000 -o Natali -c JS: 2000 < Year < 9000			
	if (YearDiv.value*1 > 2000 && YearDiv.value*1 < 9000 )
	{
		NowDay.setYear(YearDiv.value); 				
		DrawWeekList();
	}
	else
	{
		YearDiv.value =  NowDay.getFullYear();
	}			
}						

function DrawWeekList()			
{				
	OutHtml = "";				
	DayCount = new Date(NowDay.getFullYear(), NowDay.getMonth() + 1, 0).getDate();				
	WeekDay = new Date(NowDay.getFullYear(), NowDay.getMonth(), 1).getDay();	
	if (WeekDay == 0) 
		WeekDay = 7;				
	WeekCount = (WeekDay-1 + DayCount) / 7;				
	if (Math.round(WeekCount) < WeekCount) 
		WeekCount = Math.round(WeekCount) + 1;				
	else 
		WeekCount = Math.round(WeekCount);				
	DrawDay = new Date(YearDiv.value, NowDay.getMonth(), -(WeekDay-1));								
	for (i=-(WeekDay-2);i<= (WeekCount*7 - WeekDay + 1 );i++)				
	{					
		DrawDay.setDate(DrawDay.getDate()  + 1);					
		if ( DrawDay.getDay() == 1) 						
			OutHtml = OutHtml + "<tr>";  									
		if (i > 0 && i<=DayCount)  					
		{   												 						
			OutHtml = OutHtml + '<td class="Day' + WeekClass[DrawDay.getDay()];						
			if( DrawDay.toDateString() == NowDay.toDateString())  						
			{							
				OutHtml = OutHtml + ' Day' + WeekClass[100];						
			}						
			
			OutHtml = OutHtml + '" onClick="SetDateForm(' + i + ')">' + i + '</td>';    					
		}
		else  
			OutHtml = OutHtml + '<td class="Day' + WeekClass[DrawDay.getDay()] + '">&nbsp;</td>'; 
			
		if (DrawDay.getDay() == 0) 						
			OutHtml = OutHtml + "</tr>"; 					   				
	}				
	document.getElementById('WeekList').innerHTML = '<table border="0" cellspacing="0" cellpadding="0" width="140px">' + OutHtml + '</table>';			
}						

function SetTime(Delta)			
{				
	NowDay.setMinutes(NowDay.getMinutes() + Delta);				
	if (NowDay.getMinutes() < 10)					
		TimeDiv.value = NowDay.getHours() + ":0" + NowDay.getMinutes() ;				
	else					
		TimeDiv.value = NowDay.getHours() + ":" + NowDay.getMinutes() ;			
}

function SetTimeValue()			
{				
	if (TimeDiv.value.charAt(1) == ":")
	{
		Hours = TimeDiv.value.charAt(0);
		Minutes = TimeDiv.value.charAt(2) + TimeDiv.value.charAt(3); 
	}  
	else
	{
		Hours = TimeDiv.value.charAt(0) + TimeDiv.value.charAt(1);    
		Minutes = TimeDiv.value.charAt(3) + TimeDiv.value.charAt(4); 
	}
	
	if (Minutes * 1 >=0 && Minutes * 1 <= 59)
	{    
		NowDay.setMinutes(Minutes * 1);	 
	}
	
	if (Hours * 1 >=0 && Hours * 1 <= 23)
	{
		
		NowDay.setHours(Hours *1);	
	}
		
	SetTime(0);	
}
