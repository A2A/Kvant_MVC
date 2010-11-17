<?php
	Class ErrorHandle
	{
		public $Errors 		= array();
		public $ErrorState 	= 0; 
		/*                                                                                      
		0 - normal (нормальное)
		1 - notic (предупреждение)
		2 - error (ошибка)
		3 - fatal error (фатальная ошибка, блокировка действий с объектом)
		*/

		public $ActionStatus 	= true;
		public $ChangedFields 	= array();

		private static $Instance;

		protected function __construct()  
		{
			 ErrorHandle::$Instance = $this;
		}

		public static function ErrorHandle($StringError, $State = 0)
		{
			$Eh = static::GetInstance();
			$Eh->ErrorState = max($State, $Eh->ErrorState);   
			$Eh->Errors[] = array('state' =>$State, 'message' => ($State==0 ? '' : (get_called_class().':')) .$StringError);
		}

		public static function ActionErrorHandle($StringError, $State = 0)
		{
			$Eh = static::GetInstance();
			$Eh->ActionStatus = $Eh->ActionStatus and ($State==0);
			$Eh->ErrorState = max($State, $Eh->ErrorState);   
			$Eh->Errors[] = array('state' =>$State, 'message' => ($State==0 ? '' : (get_called_class().':')) .$StringError);
		}

		public static function StrErrorState()
		{
			$Eh = static::GetInstance();
			switch ($Eh->ErrorState)
			{
				case 0: return 'info';
				case 1: return 'notice';
				case 2: return 'error';
				default: return 'fatal';
			}
		}

		public static function SystemStatusOutput()
		{
			$Eh = static::GetInstance();
			if (is_array($Eh->Errors) and count($Eh->Errors) > 0)
			{
				$Errors = '<Messages>';
				foreach ($Eh->Errors as $Key => $Val)
				{
					$Errors .= '<Message status="'.$Eh->StrErrorState($Val['state']).'">'.$Val['message'].'</Message>';
				}
				$Errors .= '</Messages>';
			}
			else
			{
				$Errors = '';
			}

			if (is_array($Eh->ChangedFields) and (count($Eh->ChangedFields) >0) and ($Eh->ActionStatus === true))
			{
				$Fields = '<ChangedFields>';
				foreach ($Eh->ChangedFields as $Key => $Val)
				{
					$Fields .= '<Field name="'.$Val['name'].'">'.$Val['value'].'</Field>';
				}
				$Fields .= '</ChangedFields>';
			}
			else
			{
				$Fields = '';
			}

			if (!is_null($Eh->ActionStatus))
			{
				if ($Eh->ActionStatus) $As = '<ActionStatus>0</ActionStatus>';
				else $As = '<ActionStatus>1</ActionStatus>';
			}
			else
			{
				$As = '';
			}


			return '<?xml version="1.0" encoding="utf-8"?>
			<Result>'.$As.'<SystemStatus>'.$Eh->ErrorState.'</SystemStatus>'.
			$Errors.$Fields.'</Result>';
		}
		
		public static function GetInstance()
		{
			if (!isset(static::$Instance)) new ErrorHandle();
			return ErrorHandle::$Instance;
		}
	}
?>
