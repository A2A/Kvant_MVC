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

		public static   $UnAuthForms  	= array('default'=>true);
		public static   $Forms			= array('default'=>'index.html');

		public $title = "test";

		static public function GetObject(&$ProcessData=null,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}
		function __get($FieldName)
		{
			switch ($FieldName)
			{
				case 'CurrentInt': return intval($_SESSION['CurrentInt']); break;
				case 'CurrentWPTime': return intval($_SESSION['CurrentWPTime']); break;
				case 'CurrentUserID': return isset($_SESSION['CurrentUserID'])?intval($_SESSION['CurrentUserID']):null; break;
			}
		}

		public function IsUserAuthorized($ShowMessage=false)
		{
			$result = is_numeric($this->CurrentUserID);
			if ($ShowMessage) ErrorHandle::ErrorHandle('Пользователь'.($result?' ':' не ').'авторизован.',0);
			return $result;
		}

		public function InitCurrentUser()              
		{
			if(isset($_COOKIE['ssp_ssid_autoload_75483882827165']) and $_COOKIE['ssp_ssid_autoload_75483882827165'] != '')
			{
				$Sql = 'select * from user_sessions where `SESSIONID` = "'.$_COOKIE['ssp_ssid_autoload_75483882827165'].'" LIMIT 1';
				if ($SqlResult = $this->DataBase->Query($Sql))
				{
					if ($Rows = $this->DataBase->FetchArray($SqlResult) and is_numeric($Rows['USERID']))
					{
						$_SESSION['CurrentUserID'] = $Rows['USERID'];
					}
				}

			}

			if (is_numeric($this->CurrentUserID))
			{
				$null = null;
				//$this->CurrentUser = User::GetObject($null,$this->CurrentUserID);
			}
			else
			{
				$this->CurrentUserID = null;
				$this->CurrentUser = null;
				$this->CurrentRoleID = null;
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
	


	}

?>
