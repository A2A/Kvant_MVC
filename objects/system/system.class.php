<?php
	class System extends BaseClass
	{
		public static   $UnAuthUse      = true;
		public static  $Cashable = false;

		public static $Actions = array(
		'login'=>'LogIn',
		'logout'=>'LogOut',
		'changepassword'=>'ChangePassword',
		'checksession'=>'IsUserAuthorized',
		'setinterval'=>'SetInterval',
		'setdru'=>'SetDRUAction',
		'shiftinterval'=>'ShiftInterval'
		);
		public static $UnAuthActions = array(
		'login'=>true,
		'changepassword'=>true,
		'checksession'=>true
		);

		public static   $UnAuthForms  	= array(
		'login'=>true,
		'defaultauth'=>true,
		'defaultunauth'=>true,
		);
		public static   $Forms			= array(
		'defaultauth'=>'index.html',
		'defaultunauth'=>'index_login.html',
		'login'=>'objects/system/login_form.html',
		'currentuser'=>'objects/system/current_user.html',
		'rolelistwithactive'=>'objects/system/roles_list_with_active.html',

		'gant' 					=> 'objects/system/gant/gant.html',
		'gant_body'             => 'objects/system/gant/body.html',
		'gant_header'           => 'objects/system/gant/header.html',

		'event'         		=> 'objects/system/event/desktop.html',
		'event_list'       		=> 'objects/system/event/list.html',

		'task'         			=> 'objects/system/task/desktop.html',
		'project'         		=> 'objects/system/project/desktop.html',


		'calendar' 				=> 'objects/system/calendar.html',
		'dru_menu' 				=> 'objects/system/dru_menu.html',
		'ssp' 					=> 'objects/system/ssp.html',
		'' 						=> 'objects/system/all_url.html',
		'all_url' 				=> 'objects/system/all_url.html',
		'current_list_role' 	=> 'objects/system/current_list_role.html',
		'list_all_dru' 			=> 'objects/system/list_all_dru.html',
		);

		public $meta = '';
		public $title = 'Система сбалансированных показателей. ООО "Квант"';

		protected function __construct(&$ProcessData)  
		{   
			parent::__construct($ProcessData);
			if (!isset($_SESSION['CurrentIntID'])) $_SESSION['CurrentIntID'] = 1;
			if (!isset($_SESSION['CurrentWPTime'])) 
			{
				$int = Interval::GetObject($null,$null,$this->DataBase,$_SESSION['CurrentIntID']);  
				$_SESSION['CurrentWPTime'] =  time() - 8 * $int->Duration;
			}                
			$this->InitCurrentUser();
		}


		static public function GetObject(&$ProcessData=null,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}

		function __get($FieldName)
		{
			$null = null;
			switch ($FieldName)
			{
				case 'CurrentIntID' : return isset($_SESSION['CurrentIntID'])?intval($_SESSION['CurrentIntID']):null; break;
				case 'CurrentInt'   : return is_null($this->CurrentIntID)?null:(Interval::GetObject($null,$_SESSION['CurrentIntID'])); 
				case 'CurrentWPTime': return isset($_SESSION['CurrentWPTime'])?intval($_SESSION['CurrentWPTime']):null; break;
				case 'CurrentUserID': return (isset($_SESSION['CurrentUserID']) and intval($_SESSION['CurrentUserID']) >0)?$_SESSION['CurrentUserID']:null; break;
				case 'CurrentUser'  : return is_null($this->CurrentUserID)?null:(User::GetObject($null,$_SESSION['CurrentUserID'])); 
				case 'CurrentRoleID': return (isset($_SESSION['CurrentRoleID']) and intval($_SESSION['CurrentRoleID']) >0)?$_SESSION['CurrentRoleID']:null; break;
				case 'CurrentRole'  : return is_null($this->CurrentRoleID)?null:(Role::GetObject($null,$_SESSION['CurrentRoleID'])); 
				case 'CurrentDRUID' : return (isset($_SESSION['CurrentDRUID']) and intval($_SESSION['CurrentDRUID']) >0)?$_SESSION['CurrentDRUID']:null; break;
				case 'CurrentDRU'   : return is_null($this->CurrentDRUID)?null:(DRU::GetObject($null,$_SESSION['CurrentDRUID'])); 
				case 'Date'   		: return date('Y-m-d'); 
				case 'DateText'   	: return date('d.m.Y'); 
			}
		}

		public function __set($FieldName,$Value)
		{
			switch ($FieldName)
			{
				case 'CurrentIntID' : $_SESSION['CurrentIntID'] = $Value; break;
				case 'CurrentInt'   : @ $_SESSION['CurrentIntID'] = $Value->ID; break;
				case 'CurrentWPTime': $_SESSION['CurrentWPTime'] = $Value; break;
				case 'CurrentUserID': $_SESSION['CurrentUserID'] = $Value; break;
				case 'CurrentUser'  : @ $_SESSION['CurrentUserID'] = $Value->ID; break;
				case 'CurrentRoleID': $_SESSION['CurrentRoleID'] = $Value; break;
				case 'CurrentRole'  : @ $_SESSION['CurrentRoleID'] = $Value->ID; break;
				case 'CurrentDRUID' : $this->SetDRUID($Value); break;
				case 'CurrentDRU'   : $this->SetDRU($Value); break;
			}
		}

		public function IsUserAuthorized($ShowMessage=false)
		{
			$result = !is_null($this->CurrentUser);
			if ($ShowMessage) ErrorHandle::ErrorHandle('Пользователь'.($result?' ':' не ').'авторизован.',0);
			return $result;
		}

		public function InitCurrentUser()              
		{
			if (isset($_SESSION['CurrentUserID']) and is_numeric($_SESSION['CurrentUserID']))
			{
				$this->CurrentUserID = $_SESSION['CurrentUserID'];
			}
			elseif(isset($_COOKIE['ssp_ssid_autoload_75483882827165']) and $_COOKIE['ssp_ssid_autoload_75483882827165'] != '')
			{
				$Sql = 'select * from user_sessions where `SESSIONID` = "'.$_COOKIE['ssp_ssid_autoload_75483882827165'].'" LIMIT 1';
				if ($SqlResult = DBMySQL::Query($Sql))
				{
					if ($Rows = DBMySQL::FetchArray($SqlResult) and is_numeric($Rows['USERID']))
					{
						$_SESSION['CurrentUserID'] = $Rows['USERID'];
						$this->CurrentUserID = $_SESSION['CurrentUserID'];
					}
				}
			}
			else
			{
				$this->CurrentUser = null;
			}

			$this->InitCurrentRole();
		}

		public function InitCurrentRole()              
		{
		}

		public function LogIn()
		{
			if (isset($this->ProcessData['Login']) and isset($this->ProcessData['Password']))
			{
				$Sql = "select ID from `users` where `LOGIN` = '".$this->ProcessData['Login']."' and `PASSWORD` = MD5('".$this->ProcessData['Password']."')";
				if ($SqlResult = DBMySQL::Query($Sql))
				{
					if ($Rows = DBMySQL::FetchArray($SqlResult) and is_numeric($Rows['ID']))
					{
						ErrorHandle::ActionErrorHandle("Авторизациия пользователя прошла успешно.", 0); 
						$_SESSION['CurrentUserID'] = $Rows['ID'];
						$this->InitCurrentUser();

						$SessionID =  md5($_SESSION['CurrentUserID'].date('s'));

						if (isset($this->ProcessData['SavePass']))
						{
							$Sql = 'insert into user_sessions (`USERID`,`SESSIONID`,`EXPIREDATE`) values ('.$_SESSION['CurrentUserID'].',"'.$SessionID.'",now())';
							$SqlResult = DBMySQL::Query($Sql);
							setcookie('ssp_ssid_autoload_75483882827165',$SessionID,time()+60*60*24*30);
						}

						$result = true;
					}
					else
					{
						ErrorHandle::ActionErrorHandle("Неверные имя пользователя или пароль. Авторизация не пройдена.", 1); 
						$result = false; 
					}
				}
				else
				{
					ErrorHandle::ActionErrorHandle("При авторизации пользователя произошла ощибка запроса к базе данных. Действие отменено.", 3);   
					$result = false;  
				}
			}
			else
			{
				ErrorHandle::ActionErrorHandle('Для действия "вход в систему" передано недостаточно информации. Обратитесь к разрабочику сайта.',2);
				$result = false;
			}
			return $result;
		}

		public function LogOut()
		{
			if(isset($_COOKIE['ssp_ssid_autoload_75483882827165']) and $_COOKIE['ssp_ssid_autoload_75483882827165'] != '')
			{
				$Sql = 'delete from user_sessions where `SESSIONID` = "'.$_COOKIE['ssp_ssid_autoload_75483882827165'].'"';
				$SqlResult = DBMySQL::Query($Sql);
			}

			setcookie('ssp_ssid_autoload_75483882827165','',time()-1);

			unset($_SESSION['CurrentUserID']);
			$this->InitCurrentUser();

			ErrorHandle::ActionErrorHandle("Завершение работы прошло успешно.", 0); 
			return true;
		} 

		public function ChangePassword()
		{
			if (isset($this->ProcessData['UserEmail']))
			{
				ErrorHandle::ActionErrorHandle('Пароль будет выслан на ваш почтовый адрес.',0);
				$result = true;
			}
			elseif (isset($this->ProcessData['UserName']))
			{
				ErrorHandle::ActionErrorHandle('Пароль будет выслан на ваш почтовый адрес.',0);
				$result = true;
			}
			else
			{
				ErrorHandle::ActionErrorHandle('Недостаточно данных для запрошенной операции смены пароля.',0);
				$result = false;
			}
			return $result;
		}

		public function SetInterval()
		{
			if (isset($this->ProcessData['ID']) and intval($this->ProcessData['ID'])>0)
			{
				$int_bak = $this->CurrentInt;
				$_SESSION['CurrentIntID'] = $this->ProcessData['ID'];     

				$int_new = $this->CurrentInt;
				$NewCurrentWPTime = $this->CurrentWPTime + 8 * $int_bak->Duration - 8 * $int_new->Duration;
				$_SESSION['CurrentWPTime'] = $NewCurrentWPTime - ($NewCurrentWPTime % $int_new->Duration);

				ErrorHandle::ActionErrorHandle('произведено изменение интервала. Текущяя длительность - '.$int_new->Duration.' Текущее время - '.$_SESSION['CurrentWPTime']);
				$result = true;
			}
			else
			{
				ErrorHandle::ActionErrorHandle('Недостаточно данных для изменения интервала.',2);
				$result = false;
			}
			return $result;
		}

		public function SetDRUAction()
		{
			$result = false;
			if (!isset($this->ProcessData['ID'])) 
			{
				ErrorHandle::ActionErrorHandle('Передано недостаточно данных для смены текущего элемента Подразделение/Роль/Сотрудник',2);
			}
			elseif (intval($this->ProcessData['ID'])<=0)
			{
				ErrorHandle::ActionErrorHandle('Переданы неверные данные для смены текущего элемента Подразделение/Роль/Сотрудник',2);
			}
			else
			{
				$result = $this->SetDRUID($this->ProcessData['ID']);
				ErrorHandle::ActionErrorHandle('Произошла смена текущего элемента Подразделение/Роль/Сотрудник',0);
			}
			return $result;
		}
		
		protected function SetDRUID($ID)
		{
			$result = false;
			if (intval($ID) <= 0)
			{
				ErrorHandle::ActionErrorHandle('Передан неверный параметр для выбора элемента Подразделение/Роль/Сотрудник',2);
			}
			elseif (!($hsql = DBMySQL::Query('Select ObjectID from ur_dru where ID = '.$this->CurrentUserID.' and OBJECTID = '.$ID.' and `READ`')))
			{
				ErrorHandle::ActionErrorHandle('Ошибка при проверке прав на чтение элемента Подразделение/Роль/Сотрудник',2);
			}
			elseif (!(DBMySQL::FetchObject($hsql)))
			{
				ErrorHandle::ActionErrorHandle('Не хватает прав для использования выбранного элемента Подразделение/Роль/Сотрудник',2);
			}
			elseif (!($hsql = DBMySQL::Query('Select ID from dru where ID = '.$ID)))
			{
				ErrorHandle::ActionErrorHandle('Ошибка при проверке существования элемента Подразделение/Роль/Сотрудник',2);
			}
			elseif (!(DBMySQL::FetchObject($hsql)))
			{
				ErrorHandle::ActionErrorHandle('Попытка обращения к несуществующему элементу Подразделение/Роль/Сотрудник',2);
			}
			else
			{
				$_SESSION['CurrentDRUID'] = $ID;     
				$result = true;
			}
			return $result;
		}
		
		protected function SetDRU($DRU)
		{
			if (get_class($DRU) != 'DRU')
			{
				ErrorHandle::ActionErrorHandle('Неверный тип объекта для установки текущего значение Подразделение/Роль/Сотрудник',2);
				$result = false;
			}
			else
			{
				$result = $this->SetDRUID($DRU->ID);
			}
			return $result;
		}
		
		public function ShiftInterval()
		{
			if ($result = is_numeric($this->ProcessData['Direction']))
			{
				$_SESSION['CurrentWPTime'] = $_SESSION['CurrentWPTime'] + $this->ProcessData['Direction'] * $this->CurrentInt->Duration;
			}
			else
			{
				$this->ErrorHandle('Не передано направление сдвига интервала.',1);
			}
			return $result;
		}
		

	}

?>
