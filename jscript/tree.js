var T_eventX;
var T_eventY;
// TODO 2 -o Natali -c Интерфейс: олоса прокруктурки должна быть только горизонтальная и прижата к нижнему краю браузера, всегда отображатся на экране.
function SetEvent(X,Y)
{
	//document.getElementById('ModalWindow').style.position = 'absolute';       
	T_eventX = X;  
	T_eventY = Y;  
}
//=================================================================

function NewElem(Id,ParentID,Str,Indicator,NewName)
{
	Str = Str.replace(/NewDivId/ig,Id);
	Str = Str.replace(/ParentDivId/ig,ParentID);
	Str = Str.replace(/Indicator/ig,Indicator);
	Str = Str.replace(/NewName/ig,NewName);
	return Str;
}

function NewMenuElement(Id,Str)
{
	Str = Str.replace(/ElemId/ig,Id);
	return Str;
}


function OpenModalWindow()   
{
	document.getElementById('ModalWindow').style.display = 'block'; 
	document.getElementById('ModalWindow').style.top = T_eventY;
	document.getElementById('ModalWindow').style.left = T_eventX;
}



/**************** продублирована для удобства gant.js *************/

var ElementID;
var ElementType;

var TreeUnit = new Array();
/*	TreeUnit[i] = new Array();
	i - номер блока вывода на экране, начиная с первого.
	TreeUnit[i]['Problem'] = 1;  // Является проблемной
	TreeUnit[i]['Child']  = 1;   // Есть подчиненный подразделения
	TreeUnit[i]['ChildOpen']  = 0;   // Открыты подчиненный подразделения
	TreeUnit[i]['ChildOpen']  = 0;   // Открыты подчиненный подразделения
	TreeUnit[i]['ID']  = 1;   // ИД для заросов к серверу */
var TreeRole = new Array();
var TreeStaff = new Array();


function DivShow(DivID,event)  
{
	SetEvent(event.clientX + document.body.scrollLeft, event.clientY + document.body.scrollTop);
	document.getElementById(DivID).style.display = 'block';  
	document.getElementById(DivID).style.position = 'absolute';  
	document.getElementById(DivID).style.top = T_eventY-10;
	document.getElementById(DivID).style.left = T_eventX-10;
}

function FactorShow(Id)
{
	document.getElementById('HiddenBlock-'+Id).style.display = 'none'; 
	document.getElementById('OpenBlock-'+Id).style.display = 'block'; 
} 

function FactorHidden(Id)
{
	document.getElementById('HiddenBlock-'+Id).style.display = 'block'; 
	document.getElementById('OpenBlock-'+Id).style.display = 'none'; 
}


function MenuUnit(Id,event)
{
	ElementType = "Unit";
	ElementID = Id;
	DivShow('MenuUnit',event);
}


function MenuRole(Id,UnitId,event)
{
	ElementType = "Role";
	ElementID = Id;
	ElementOwnerID = UnitId;
	DivShow('MenuRole',event);
}

function MenuStaff(Id,event)
{
	ElementType = "Staff";
	ElementID = Id;
	DivShow('MenuStaff',event);  
}

/*
function MenuAll(Id,event,type)
{
	BlockId = 'Menu' + type;
	Str = document.getElementById(BlockId).innerHTML;  
	Str = NewMenuElement(Id,Str);
	
	/////     
	
	"ВСЕ ТУТЮ. вывести на экран в нужном положении. Проверить передачу данных и сработыване кнопок.";
	
	DivShow(Id,'menuUnit',event);
}

function MenuSmall(Id,event,type)
{
	DivShow(Id,'MenuUnitSmall',event);  
}
*/

function CatchStatus(Messages)
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
}

function CatchUnitShow(Messages) 
{
	CatchStatus(Messages);
	
	regexpResultStatus  = /<Owner>(.+)<\/Owner>/ ; 
	if(regexpResultStatus.test(Messages))
	{    
		result = regexpResultStatus.exec(Messages);  
		OwnerID = result[1]; 
	};
	regexpResultStatus  = null;
	TreeUnit[OwnerID]['ChildOpen'] = 1;
		
	var NextLevel = document.getElementById('NextLevel-'+OwnerID);
	document.getElementById('TdUnitVertical-'+OwnerID).style.backgroundImage = "url('images-tree/line-unit-vertical.png')";
		 
	regexpElementList     = /<Element>(.+)<\/Element>/ ; 
	if(regexpElementList.test(Messages))
	{
		ElementList = Messages.split('Element'); 

		regexpName     	= /<Name>(.+)<\/Name>/ ; 
		regexpId     	= /<Id>(.+)<\/Id>/ ; 
		regexpProblem   = /<Problem>(.+)<\/Problem>/ ; 
		regexpChild     = /<Child>(.+)<\/Child>/ ; 
		regexpIndicator = /<Indicator>(.+)<\/Indicator>/ ; 
				
		Count = ElementList.length;
		var Sablon = document.getElementById('MainUnit').innerHTML;  
		table = "";
		Tr1 = "";
		Tr2 = "";
		countUnit = 0;
		for(i=0; i < Count; i++)
		{
			if(regexpId.test(ElementList[i]))
			{
				ID = regexpId.exec(ElementList[i])[1];
				KeyArray = TreeUnit.length;
				
				TreeUnit[KeyArray] = new Array();
				TreeUnit[KeyArray]['Problem'] = regexpProblem.exec(ElementList[i])[1];  // Является проблемной
				TreeUnit[KeyArray]['Child']  = regexpChild.exec(ElementList[i])[1];   // Есть подчиненный подразделения
				TreeUnit[KeyArray]['Role']  = 0;   // открыты роли
				TreeUnit[KeyArray]['ChildOpen']  = 0;   // открыты подчиненный подразделения
				TreeUnit[KeyArray]['ID']  = ID;   // Есть подчиненный подразделения
	
				Name = regexpName.exec(ElementList[i])[1];
				Indicator = regexpIndicator.exec(ElementList[i])[1];
				
			
				Block1 = NewElem(KeyArray,OwnerID,Sablon,Indicator,Name);   
				Block2 = NewElem(KeyArray,OwnerID,Sablon,Indicator,Name);   
				Tr1 = Tr1 + '<td class="TreeBlockUnit" valign=top  colspan=2>' + Block1  +   '</td>';
				
				countUnit++;
				if (Count == 3) 
				{
					Tr2 = Tr2 + '<td class="NoneLineBegin"><img src="images/none.png" class="treeimage"></td><td class="NoneLineEnd">&nbsp;</td>';  
				}
				else
				if (countUnit == 1)
				{
					Tr2 = Tr2 + '<td class="NoneLineBegin"><img src="images/none.png" class="treeimage"></td><td class="LineLeftTop">&nbsp;</td>';  
				}
				else
				if (i<Count-2)
				{
					Tr2 = Tr2 + '<td class="LineTop"><img src="images/none.png" class="treeimage"></td><td class="LineLeftTop">&nbsp;</td>';  
				}
				else
				{
					Tr2 = Tr2 + '<td class="LineTop"><img src="images/none.png" class="treeimage"></td><td class="NoneLineEnd">&nbsp;</td>';  
				}
				
			} 
		}
		   
		regexpName     	= null; 
		regexpId     	= null; 
		regexpProblem   = null; 
		regexpChild     = null; 
		regexpIndicator = null; 
		// TODO 1 -o Natali -c Отображение дерева: проблема с первой ячейкой
		if (NextLevel.innerHTML == '') 
			NextLevel.innerHTML =  '<table border="0" cellspacing="0" cellpadding="0" bordercolor="#ff0000" width="100%" align="center">'
			+'<tr>' + Tr2 +  "</tr><tr>" + Tr1 +  "</tr></table>";
		//document.getElementById('TempTdForIE-'+OwnerID).style.width = "20px";
		
	
	}; 
   
}

function UnitShow(OwnerID)
{
	var RoleBlock = document.getElementById('NextLevel-'+OwnerID);
	if (RoleBlock.innerHTML == '') 
	{
		
		Url = "?Ajax=1&Object=DRUDivisionList&Form=unit&Filter[0][Field]=ParentID&Filter[0][Oper]=eq&Filter[0][Val]="+
		TreeUnit[OwnerID]['ID']+"&Filter[1][Field]=RoleID&Filter[1][Oper]=eq&Filter[1][Val]=&ParentID=" + OwnerID;
		AjaxSendGET(Url,CatchUnitShow);
		TreeUnit[OwnerID]['ChildOpen'] = 1;     // открыты подчиненные
	}
	else
	{
		if (RoleBlock.style.display == 'block')
		{
			RoleBlock.style.display = 'none';
			TreeUnit[OwnerID]['ChildOpen'] = 0;  
			document.getElementById("TdUnitVertical-"+OwnerID).style.backgroundImage = "url('')";  
		}
		else
		{
			RoleBlock.style.display = 'block'; 
			TreeUnit[OwnerID]['ChildOpen'] = 1;  
			document.getElementById("TdUnitVertical-"+OwnerID).style.backgroundImage = "url('images-tree/line-unit-vertical.png')";  
		}  
		
	}   
}

function CatchRoleShow(Messages) 
{
	CatchStatus(Messages);
	
	regexpResultStatus  = /<Owner>(.+)<\/Owner>/ ; 
	if(regexpResultStatus.test(Messages))
	{    
		result = regexpResultStatus.exec(Messages);  
		OwnerID = result[1]; 
	};
	regexpResultStatus  = null;
	
	var NextLevel = document.getElementById('NextLevel-'+OwnerID);
	document.getElementById('TdUnitRole-'+OwnerID).style.backgroundImage = "url('images-tree/line-unit-role.png')";
		 
	regexpElementList     = /<Element>(.+)<\/Element>/ ; 
	if(regexpElementList.test(Messages))
	{
		ElementList = Messages.split('Element'); 

		regexpName     	= /<Name>(.+)<\/Name>/ ; 
		regexpId     	= /<Id>(.+)<\/Id>/ ; 
		regexpProblem   = /<Problem>(.+)<\/Problem>/ ; 
		regexpIndicator = /<Indicator>(.+)<\/Indicator>/ ; 
				
		Count = ElementList.length;
		//var Sablon = document.getElementById('ShablonRole').innerHTML;  
		table = "";
		var Sablon1 = document.getElementById('ShablonRole1').innerHTML;  
		var Sablon2 = document.getElementById('ShablonRole2').innerHTML;  
		table = '<table border="0" cellspacing="0" cellpadding="0" bordercolor="#0000ff">';
		var RoleBlock = document.getElementById('RoleBlock-'+OwnerID);
		Tr1 = "";
		Tr2 = "";
		countRole = 0;
		TreeRole[OwnerID] = new Array();   
		
		for(i=0; i < Count; i++)
		{
			//alert(ElementList[i]);
			if(regexpId.test(ElementList[i]))
			{
				ID = regexpId.exec(ElementList[i])[1];
				
				TreeRole[OwnerID][countRole] = new Array();
				TreeRole[OwnerID][countRole]['Problem'] = regexpProblem.exec(ElementList[i])[1];  // Является проблемной
				TreeRole[OwnerID][countRole]['ID'] = ID;  
				
				Name = regexpName.exec(ElementList[i])[1];
				Indicator = regexpIndicator.exec(ElementList[i])[1];
				
		
				Block1 = NewElem(ID,OwnerID,Sablon1,Indicator,Name);
				Block2 = NewElem(ID,OwnerID,Sablon2,Indicator,Name);
			
				Tr1 = Tr1 + '<td class="TreeBlockRole" id="TdRole-' + ID + '">' + Block1  + Block2 + '</td>';
				Tr2 = Tr2 + '<td valign=top><div id="Role.' + ID + '-Staff"></div></td>';
				countRole++;
			}
		}
		
		
		RoleBlock.innerHTML =   table + "<tr>" + Tr1 + "</tr><tr>" + Tr2 + '</tr></table>';
		RoleBlock.style.display = 'block';  
		SetRoleLineWidthTd(OwnerID); 
		   
		regexpName     	= null; 
		regexpId     	= null; 
		regexpProblem   = null; 
		regexpChild     = null; 
		regexpIndicator = null; 
	
	};
   
}

function SetRoleLineWidthTd(KeyArray)
{
	if (TreeUnit[KeyArray]['Role'] == 1 && TreeRole[KeyArray]) 
	{
		countRole = TreeRole[KeyArray].length;
	
		Block2 = document.getElementById('UnitOpenBlock-'+KeyArray);
		if (Block2.style.display == 'block')
		{
			
			document.getElementById("TdUnitRole-"+KeyArray).style.width = (220*countRole - 130) + "px"; 
			document.getElementById("TableUnit-"+KeyArray).style.width = (220*countRole)+ 165 + "px"; 
		}
		else
		{  	
			document.getElementById("TdUnitRole-"+KeyArray).style.width = (220*countRole - 60 - 130) + "px";
			document.getElementById("TableUnit-"+KeyArray).style.width = (220*countRole)+ 165 - 60 + "px"; 
	
		} 
		
		   
	}
	
}

function RoleShow(KeyArray)
{
	
	var RoleBlock = document.getElementById('RoleBlock-'+KeyArray);
	if (RoleBlock.innerHTML == '') 
	{
		Url = "?Ajax=1&Object=DRUDivisionList&Form=role&Filter[0][Field]=ParentID&Filter[0][Oper]=eq&Filter[0][Val]="+
		TreeUnit[KeyArray]['ID']+"&Filter[1][Field]=RoleID&Filter[1][Oper]=!eq&Filter[1][Val]=&ParentID=" + KeyArray;
		AjaxSendGET(Url,CatchRoleShow);
		TreeUnit[KeyArray]['Role']  = 1;   // открыты роли
	}
	else
	{
		if (RoleBlock.style.display == 'block')
		{
			RoleBlock.style.display = 'none';
			TreeUnit[KeyArray]['Role']  = 0;   // закрыть роли
			document.getElementById("TdUnitRole-"+KeyArray).style.backgroundImage = "url('')";  
		}
		else
		{
			RoleBlock.style.display = 'block'; 
			TreeUnit[KeyArray]['Role']  = 1;   // открыты роли
			//TreeUnit[OwnerID]['ChildOpen']  = 0;   // открыты подчиненный подразделения
			document.getElementById("TdUnitRole-"+KeyArray).style.backgroundImage = "url('images-tree/line-unit-role.png')";  
			SetRoleLineWidthTd(OwnerID); 
		}  
		
	} 
	
	//alert(Url);
}

function RoleStaffShowAll(KeyArray)
{   
	UnitID = KeyArray;
//	alert(TreeRole[UnitID].length +"|"+TreeUnit[KeyArray]['ID']);
	var i;
	if (TreeRole[UnitID].length >= 1)
	{
		for(i=0;i<TreeRole[UnitID].length;i++)
			StaffShow(TreeRole[UnitID][i]['ID'],UnitID);
	}
	
}

function RoleStaffShow(UnitID)
{
	document.getElementById('RoleBlock-'+UnitID).style.display = 'none';  
	RoleShow(UnitID);
	setTimeout("RoleStaffShowAll("+UnitID+")", 300); 
}


function CatchStaffShow(Messages) 
{
	CatchStatus(Messages);
	
	regexpResultStatus  = /<Owner>(.+)<\/Owner>/ ; 
	if(regexpResultStatus.test(Messages))
	{    
		result = regexpResultStatus.exec(Messages);  
		OwnerID = result[1]; 
	};
	regexpResultStatus  = null;
	document.getElementById("TdRole-"+OwnerID).style.backgroundImage = "url('images-tree/line-open-role.png')";  
	/*
	for (ki=1;ki<count;ki++)
	{
		UnitID = TreeUnit[ki]['ID'];
		if (TreeUnit[ki]['Role'] == 1 && TreeRole[UnitID]) 
		{
			for (ri=0;ri<TreeRole[UnitID].length;ri++)
			if (OwnerID == TreeRole[UnitID][ri]['ID']) 
				TreeRole[UnitID][OwnerID]['Staff'] = 1;
		}
	}
	*/
		 
	regexpElementList     = /<Element>(.+)<\/Element>/ ; 
	if(regexpElementList.test(Messages))
	{
		ElementList = Messages.split('Element'); 

		regexpName     	= /<Name>(.+)<\/Name>/ ; 
		regexpId     	= /<Id>(.+)<\/Id>/ ; 
		regexpProblem   = /<Problem>(.+)<\/Problem>/ ; 
		regexpIndicator = /<Indicator>(.+)<\/Indicator>/ ; 
				
		Count = ElementList.length;
		//alert(Count+" | "+ElementList);
		//var Sablon = document.getElementById('ShablonRole').innerHTML;  
		table = "";
		var Sablon1 = document.getElementById('ShablonStaff1').innerHTML  
		var Sablon2 = document.getElementById('ShablonStaff2').innerHTML  
		table = '<table border="0" cellspacing="0" cellpadding="0" bordercolor="#00ff00"><tr>'
		var StaffBlock = document.getElementById('Role.'+OwnerID+'-Staff');
		TreeStaff[OwnerID] = new Array(); 
		for(i=0; i < Count - 1; i++)
		{
			TreeStaff[OwnerID][i] = new Array();
				
			if(regexpId.test(ElementList[i]))
			{
				ID = regexpId.exec(ElementList[i])[1];  
				
				TreeStaff[OwnerID][i]['Problem'] = regexpProblem.exec(ElementList[i])[1];  // Является проблемной
				TreeStaff[OwnerID][i]['ID'] = ID;  // Является проблемной
						
				Name = regexpName.exec(ElementList[i])[1];
				Indicator = regexpIndicator.exec(ElementList[i])[1];
				
		
				Block1 = NewElem(ID,OwnerID,Sablon1,Indicator,Name);
				Block2 = NewElem(ID,OwnerID,Sablon2,Indicator,Name);
			
				if (i>=Count-2) 
					table = table + '<tr><td class="StaffBlockEnd">' + Block1  + Block2 + '</td></tr>';
				else  
					table = table + '<tr><td class="StaffBlock">' + Block1  + Block2 + '</td></tr>';  
			}
		}
		
		
		StaffBlock.innerHTML =   table + '</table>';
		StaffBlock.style.display = 'block';  
		   
		regexpName     	= null; 
		regexpId     	= null; 
		regexpProblem   = null; 
		regexpChild     = null; 
		regexpIndicator = null; 
	
	};
   
}

function StaffShow(RoleID,UnitKeyArray)
{
	var StaffBlock = document.getElementById('Role.'+RoleID+'-Staff');
	if (StaffBlock.innerHTML == '') 
	{
		Url = "?Ajax=1&Object=DRUUserList&Form=user&Filter[0][Field]=ParentID&Filter[0][Oper]=eq&Filter[0][Val]="+
		TreeUnit[UnitKeyArray]['ID']+"&Filter[1][Field]=RoleID&Filter[1][Oper]=eq&Filter[1][Val]="+RoleID+"&ParentID=" + RoleID;
		AjaxSendGET(Url,CatchStaffShow);
	}
	else
	{
		StaffBlock.style.display = 'block';    
	}
	//alert(Url);
}

function CatchCategorShow(Message)
{
	regexpResultStatus  = /<OwnerDiv>(.+)<\/OwnerDiv>/ ; 
	if(regexpResultStatus.test(Message))
	{    
		result = regexpResultStatus.exec(Message);  
		OwnerDiv = result[1]; 
	
		regexpResultStatus  = null;
		regexpText    = /<Text>(.+)<\/Text>/ ; 
		
		
		if(regexpText.test(Message))
		{
			 document.getElementById(OwnerDiv).innerHTML = regexpText.exec(Message)[1]; 
		}
		regexpText    = null;    
	};
	 
}

function UnitCategorShow(OwnerID)
{
	var Block = document.getElementById('UnitCategory-'+OwnerID);
	if (Block.innerHTML == '') 
	{
		Url = "?Ajax=1&Object=CategoryList&Form=list&Filter[0][Field]=DRUID&Filter[0][Oper]=eq&Filter[0][Val]="+
		TreeUnit[OwnerID]['ID']+"&DivID=" + 'UnitCategory-'+ OwnerID;
		//alert(Url);   
	
		AjaxSendGET(Url,CatchCategorShow);
	}
	
	Block1 = document.getElementById('UnitHiddenBlock-'+OwnerID);
	Block2 = document.getElementById('UnitOpenBlock-'+OwnerID);
	Block3 = document.getElementById('TableUnitCategory-'+OwnerID);
	
	if (Block2.style.display == 'block')
	{
		Block2.style.display = 'none';
		Block3.style.display = 'none';
		Block1.style.display = 'block';
		SetRoleLineWidthTd(OwnerID);
	}
	else
	{
		Block2.style.display = 'block'; 
		Block3.style.display = 'block'; 
		Block1.style.display = 'none';
		SetRoleLineWidthTd(OwnerID); 
	}   
}

function RoleCategorShow(ID)
{
	var Block = document.getElementById('RoleCategory-'+ID);
	if (Block.innerHTML == '') 
	{
		Url = "?Ajax=1&Object=CategoryList&Form=list&Filter[0][Field]=DRUID&Filter[0][Oper]=eq&Filter[0][Val]="+
		ID+"&DivID=" + 'RoleCategory-'+ ID;
		//alert(Url);   
	
		AjaxSendGET(Url,CatchCategorShow);
	}
	
	Block1 = document.getElementById('RoleHiddenBlock-'+ID);
	Block2 = document.getElementById('RoleOpenBlock-'+ID);
	Block3 = document.getElementById('TableRoleCategory-'+ID);
	
	if (Block2.style.display == 'block')
	{
		Block2.style.display = 'none';
		Block3.style.display = 'none';
		Block1.style.display = 'block';
		//SetRoleLineWidthTd(ID);
	}
	else
	{
		Block2.style.display = 'block'; 
		Block3.style.display = 'block'; 
		Block1.style.display = 'none';
		//SetRoleLineWidthTd(ID); 
	}   
}

function StaffCategorShow(ID)
{
	var Block = document.getElementById('StaffCategory-'+ID);
	if (Block.innerHTML == '') 
	{
		Url = "?Ajax=1&Object=CategoryList&Form=list&Filter[0][Field]=DRUID&Filter[0][Oper]=eq&Filter[0][Val]="+
		ID+"&DivID=" + 'StaffCategory-'+ ID;
		//alert(Url);   
	
		AjaxSendGET(Url,CatchCategorShow);
	}
	
	Block1 = document.getElementById('StaffHiddenBlock-'+ID);
	Block2 = document.getElementById('StaffOpenBlock-'+ID);
	Block3 = document.getElementById('TableStaffCategory-'+ID);
	
	if (Block2.style.display == 'block')
	{
		Block2.style.display = 'none';
		Block3.style.display = 'none';
		Block1.style.display = 'block';
		//SetRoleLineWidthTd(ID);
	}
	else
	{
		Block2.style.display = 'block'; 
		Block3.style.display = 'block'; 
		Block1.style.display = 'none';
		//SetRoleLineWidthTd(ID); 
	}   
}

function CatchFactorListShow(Message)
{
	regexpResultStatus  = /<OwnerDiv>(.+)<\/OwnerDiv>/ ; 
	if(regexpResultStatus.test(Message))
	{    
		result = regexpResultStatus.exec(Message);  
		OwnerDiv = result[1]; 
	
		regexpResultStatus  = null;
		regexpText    = /<Text>(.+)<\/Text>/ ; 
		
		
		if(regexpText.test(Message))
		{
			 document.getElementById(OwnerDiv).innerHTML = regexpText.exec(Message)[1]; 
		}
		regexpText    = null;    
	};
}

function FactorListShow(CatID,DivID,DRUID)
{
	var Block = document.getElementById(DivID);
	if (Block.innerHTML == '') 
	{
		Url = "?Ajax=1&Object=ScoreCardList&Form=list&Filter[0][Field]=DRUID&Filter[0][Oper]=eq&Filter[0][Val]="+
		DRUID+"&Filter[0][Field]=CategoryID&Filter[0][Oper]=eq&Filter[0][Val]="+
		CatID+"&DivID=" + DivID;
		
		AjaxSendGET(Url,CatchFactorListShow);
	}
	
	
	if (Block.style.display == 'block')
	{
		Block.style.display = 'none';
	}
	else
	{
		Block.style.display = 'block'; 
	}   
}

function UnitFactorShow(OwnerID)
{
	document.getElementById('UnitOpenBlock-'+OwnerID).style.display = 'none';
	UnitCategorShow(OwnerID);
	setTimeout("FactorShow(" + OwnerID + ",'Unit')", 300);                                   
}

function RoleFactorShow(OwnerID)
{
	document.getElementById('RoleOpenBlock-'+OwnerID).style.display = 'none';
	RoleCategorShow(OwnerID);
	setTimeout("FactorShow(" + OwnerID + ",'Role')", 300);                                   
}

function StaffFactorShow(OwnerID)
{
	document.getElementById('StaffOpenBlock-'+OwnerID).style.display = 'none';
	StaffCategorShow(OwnerID);
	setTimeout("FactorShow(" + OwnerID + ",'Staff')", 300);                                   
}

function FactorShow(OwnerID,OwnerType)  
{
	DivID = "Categor"+ OwnerType +"-"+OwnerID + "-";   
	// TODO 1 -o Nat -c JS логика: список категорий фиксированный или он может менятся, надо ли хранить их для кахдого узла-листа дерева?
	// сейчас их 3 штуки
	if (document.getElementById(DivID + '1'))
	{
		FactorListShow(1, DivID + '1', OwnerID, OwnerType);                                  
		FactorListShow(2, DivID + '2', OwnerID, OwnerType);                                  
		FactorListShow(3, DivID + '3', OwnerID, OwnerType);  
	}
	else
		setTimeout("FactorShow(" + OwnerID + ",'" + OwnerType + "')", 300);   
}


//================ 
// TODO 1 -o Natali -c JS: ddddd
 /*
var TreeUnit = new Array();
var TreeRole = new Array();
var TreeStaff = new Array();
 */
function ZoomUnit(KeyArray)
{
	RoleShow(KeyArray);
	if (TreeUnit[KeyArray]['Child'] == 1) UnitShow(KeyArray);
	
	// TODO 1 -o N -c JS: поменять привязку всего на KeyArray, для подразделений, ролей и сотрудников
}

function ZoomProblemUnit(KeyArray)
{
	RoleShow(KeyArray);
	if (TreeUnit[KeyArray]['Child'] == 1) UnitShow(KeyArray);
}

function ZoomRole(ID)
{
	alert(ElementID + "=="+ ElementOwnerID + " == "+ ID);
	StaffShow(ElementID,ElementOwnerID);
}

function ZoomProblemRole(ID)
{
	
}


function ZoomProblem(ID)
{
	
}

function ClickUnit(ID)
{
	alert('Клик по узлу дерева');
}

// Функции главного меню
function ShowUnitAll()
{
	count = TreeUnit.length;
	for (i=1;i<count;i++)
	{
		if (TreeUnit[i]['ChildOpen'] != 1 && TreeUnit[i]['Child'] == 1) 
		{	
			UnitShow(i);
			
			setTimeout("ShowUnitAll()", 300+i);      
		}  
		document.getElementById('RoleBlock-'+i).style.display = 'none';
		TreeUnit[i]['Role']  = 0;   // закрыть роли
		document.getElementById("TdUnitRole-"+i).style.backgroundImage = "url('')";  
	
	
	}
	
}

function ShowRoleAll()
{
	count = TreeUnit.length;
	for (i=1;i<count;i++)
	{
		if (TreeUnit[i]['Role'] != 1) 
		{	
			RoleShow(i);
		}
	}
}

function ShowStaffAll()
{
	count = TreeUnit.length;
	var ii;      
	for (ii=1;ii<count;ii++)
	{
		if (TreeUnit[ii]['Role'] == 1) 
		{	
			RoleStaffShowAll(ii);
		}
	}
}

function ShowCategorAll()
{
	count = TreeUnit.length;
	for (ki=1;ki<count;ki++)
	{
		UnitID = TreeUnit[ki]['ID'];
		UnitCategorShow(ki);
		if (TreeUnit[ki]['Role'] == 1 && TreeRole[ki]) 
		{	
			for (ri=0;ri<TreeRole[ki].length;ri++)
			{
				RoleID = TreeRole[ki][ri]['ID'];
				RoleCategorShow(RoleID); 
				if (TreeStaff[RoleID]) 
				{		
					for (ui=0;ui<TreeStaff[RoleID].length;ui++)
					{
						if(TreeStaff[RoleID][ui]['ID'])  StaffCategorShow(TreeStaff[RoleID][ui]['ID']); 
						
					}  
				} 
			}  
		}
	}
}

function ShowFactorAll()
{
	
}
