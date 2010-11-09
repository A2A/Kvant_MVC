<?php
	class System extends BaseClass
	{
		public static   $UnAuthUse      = true;

		public static $Actions = array(
		'login'=>'LogIn',
		'logout'=>'LogOut',
		'changepassword'=>'ChangePassword',
		'checksession'=>'IsUserAuthorized',
		'setinterval'=>'SetInterval',
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
		);

		public $title = "test";
		public $CurrentUser;

		static public function GetObject(&$ProcessData=null,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}
		function __get($FieldName)
		{
			switch ($FieldName)
			{
				case 'CurrentInt': return isset($_SESSION['CurrentInt'])?intval($_SESSION['CurrentInt']):null; break;
				case 'CurrentWPTime': return isset($_SESSION['CurrentWPTime'])?intval($_SESSION['CurrentWPTime']):null; break;
				case 'CurrentUserID': return (!is_null($this->CurrentUser))?$this->CurrentUser->ID:null; break;
			}
		}
		public function __set($FieldName,$Value)
		{
			switch ($FieldName)
			{
				case 'CurrentInt': return isset($_SESSION['CurrentInt'])?intval($_SESSION['CurrentInt']):null; break;
				case 'CurrentWPTime': return isset($_SESSION['CurrentWPTime'])?intval($_SESSION['CurrentWPTime']):null; break;
				case 'CurrentUserID': 
				{
					$_SESSION['CurrentUserID'] = $Value;
					$this->CurrentUser = User::GetObject($null,$Value);
					break;
				}
				case 'CurrentRoleID': 
				{
					$_SESSION['CurrentRoleID'] = $Value;
					$this->CurrentRole = Role::GetObject($null,$Value);
					break;
				}
					// TODO 2 -o Molev -c СЕТ: Установка ИД при __СЕТ
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
			if (is_numeric($_SESSION['CurrentUserID']))
			{
				$this->CurrentUserID = $_SESSION['CurrentUserID'];
			}
			elseif(isset($_COOKIE['ssp_ssid_autoload_75483882827165']) and $_COOKIE['ssp_ssid_autoload_75483882827165'] != '')
			{
				$Sql = 'select * from user_sessions where `SESSIONID` = "'.$_COOKIE['ssp_ssid_autoload_75483882827165'].'" LIMIT 1';
				if ($SqlResult = $this->DataBase->Query($Sql))
				{
					if ($Rows = $this->DataBase->FetchArray($SqlResult) and is_numeric($Rows['USERID']))
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
			if (is_numeric($_SESSION['CurrentRoleID']))
			{
				$this->CurrentRoleID = $_SESSION['CurrentRoleID'];
			}
			else
			{
				$this->CurrentRole = null;
			}
		}

		public function LogIn()
		{
			if (isset($this->ProcessData['Login']) and isset($this->ProcessData['Password']))
			{
				$Sql = "select ID from `users` where `LOGIN` = '".$this->ProcessData['Login']."' and `PASSWORD` = '".$this->ProcessData['Password']."'";
				if ($SqlResult = $this->DataBase->Query($Sql))
				{
					if ($Rows = $this->DataBase->FetchArray($SqlResult) and is_numeric($Rows['ID']))
					{
						ErrorHandle::ActionErrorHandle("Авторизациия пользователя прошла успешно.", 0); 
						$_SESSION['CurrentUserID'] = $Rows['ID'];
						$this->InitCurrentUser();

						$SessionID =  md5($_SESSION['CurrentUserID'].date('s'));

						if (isset($this->ProcessData['SavePass']))
						{
							$Sql = 'insert into user_sessions (`USERID`,`SESSIONID`,`EXPIREDATE`) values ('.$_SESSION['CurrentUserID'].',"'.$SessionID.'",now())';
							$SqlResult = $this->DataBase->Query($Sql);
							setcookie('ssp_ssid_autoload_75483882827165',$SessionID,time()+60*60*24*30);
						}

						$result = true;
					}
					else
					{
						ErrorHandle::ActionErrorHandle("Неверные имя пользователя или пароль. Авторизация не пройдена.", 0); 
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
				$SqlResult = $this->DataBase->Query($Sql);
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
				$_SESSION['CurrentInt'] = $this->ProcessData['ID'];     

				$int_new = Interval::GetObject($null,$this->CurrentInt);
				$NewCurrentWPTime = $this->CurrentWPTime + 8 * $int_bak->Duration - 8 * $int_new->Duration;
				$_SESSION['CurrentWPTime'] = $NewCurrentWPTime - ($NewCurrentWPTime % $int_new->Duration);

				$result = true;
			}
			else
			{
				$result = false;
			}
		}



	}

?>
