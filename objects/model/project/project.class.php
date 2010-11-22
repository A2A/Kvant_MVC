<?php
	class Project extends Entity
	{
		// TODO 4 -o Natali -c Замечание: форма edit и view_full, если запрошено форма редактирования, а пользователь не имеет рава редактировать этот проект, то надо отдавать форму view_full - полное описание 
		public static $Forms = array(
		'edit' 			=> 'objects/model/project/edit.html',
		'view_short' 	=> 'objects/model/project/view_short.html',
		'view_full' 	=> 'objects/model/project/view_full.html',
		'new' 			=> 'objects/model/project/new.html',
		'view_status' 	=> 'objects/model/project/view_status.html',
		);

		protected $DBTableName = 'projects';

		protected $UserID;
		protected $User;

		public $InitDate;
		public $StartDate = null;
		public $FinishDate = null;

		public $ReadyState = 0;
		protected $FullDescription;
		public $CurrentStatusID = 1;
		public $CurrentStatus = 'Поставлена';


		// TODO 4 -o Natali -c Перенести: коректно работает  Refresh()

		protected function Refresh()
		{
			$null = null;
			if (is_int($this->ID))
			{
				$sql = 'Select * from '.$this->DBTableName.' where ID = '.$this->ID;
				$hSql = $this->DataBase->Query($sql);
				while ($fetch = $this->DataBase->FetchObject($hSql)) 
				{
					$this->UserID = $fetch->USERID;
					$this->User = new User($null,$null,$this->DataBase,$this->UserID);
					$this->User->Refresh();

					$this->Description = $fetch->DESCRIPTION;
					$this->FullDescription = $fetch->FULL_DESCR;

					$this->InitDate = strtotime($fetch->DATE_INIT);
					$this->StartDate = strtotime($fetch->DATE_START);

					if (is_null($fetch->DATE_FINISH) or $fetch->DATE_FINISH == 0 or $fetch->DATE_FINISH == "")
						$this->FinishDate = time() + 24*3600*180;     
					else
						$this->FinishDate = strtotime($fetch->DATE_FINISH);

					if (is_null($fetch->READY_STATE) or $fetch->READY_STATE == "")
						$this->ReadyState = 0;
					else
						$this->ReadyState = $fetch->READY_STATE;

				}
			}
		}

		// TODO 4 -o Natali -c Перенести: коректно работает  SetActionData()

		protected function SetActionData()
		{
			parent::SetActionData();
			if (isset($this->ProcessData['UserID']) and ($this->ProcessData['UserID'] != $this->UserID))
			{
				$this->UserID = $this->ProcessData['UserID'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['Description']) and ($this->ProcessData['Description'] != $this->Description))
			{
				$this->Description = $this->ProcessData['Description'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['DateInit']) and ($this->ProcessData['DateInit'] != $this->DateInit))
			{
				$this->InitDate = $this->ProcessData['DateInit'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['StartDateValue']) and ($this->ProcessData['StartDateValue'] != $this->StartDate))
			{
				$this->StartDate = $this->ProcessData['StartDateValue'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['FinishDateValue']) and ($this->ProcessData['FinishDateValue'] != $this->DateFinish))
			{
				$this->FinishDate = $this->ProcessData['FinishDateValue'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['FullDescription']) and ($this->ProcessData['FullDescription'] != $this->FullDescription))
			{
				$this->FullDescription = $this->ProcessData['FullDescription'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['ReadyState']) and ($this->ProcessData['ReadyState'] != $this->ReadyState))
			{
				$this->ReadyState = $this->ProcessData['ReadyState'];
				$this->Modified = true;
			}
			return $this->Modified; 
		}

		// TODO 4 -o Natali -c Перенести: коректно работает  Save()

		public function Save()
		{
			if (!intval($this->ID))
			{
				// TODO 4 -o Natali -c Ошибка формирования SQL запроса: при создании если не установлен пользователь, надо получить текущего для $this->UserID
				$sql = 'insert into '.$this->DBTableName.' (ID, DESCRIPTION,DATE_INIT,DATE_START,DATE_FINISH,
				FULL_DESCR,USERID,READY_STATE) 
				values (NULL,"'.$this->Description.'","'.DateTimeToMySQL($this->InitDate).'","'.DateTimeToMySQL($this->StartDate).'","'.DateTimeToMySQL($this->FinishDate).'",
				"'.$this->FullDescription.'",'.(intval($this->UserID)?intval($this->UserID):'null').',"'.$this->ReadyState.'")';
				$this->ErrorHandle($sql);   // TODO 4 -o Natali -c сообщение для отладки: SQL   

				$hSql = $this->DataBase->Query($sql);
				if ($hSql)
				{
					$this->ID = $this->DataBase->InsertID($hSql);
					$this->ChangedFields[] = array('name' => 'ID','value' => $this->ID);
					$this->ErrorHandle('Объект типа '.get_class($this).' успешно сохранен.',0);
					$Result = true;
				}
				else
				{
					$this->ErrorHandle('Ошибка сохранения объекта типа '.get_class($this).'.',2);
					$Result = true;
				}
			}
			else
			{
				$sql = 'update '.$this->DBTableName.' set 
				DESCRIPTION="'.$this->Description.'",
				DATE_INIT="'.DateTimeToMySQL($this->InitDate).'",
				DATE_START="'.DateTimeToMySQL($this->StartDate).'",
				DATE_FINISH="'.DateTimeToMySQL($this->FinishDate).'",
				FULL_DESCR="'.$this->FullDescription.'", 
				READY_STATE="'.$this->ReadyState.'", 
				USERID='.(intval($this->UserID)?intval($this->UserID):'null').' 
				where ID = '.$this->ID;
				// TODO 4 -o Natali -c сообщение для отладки: SQL 
				$this->ErrorHandle($sql);       
				$hSql = $this->DataBase->Query($sql);
				if ($hSql)
				{
					$this->ErrorHandle('Объект типа '.get_class($this).' успешно сохранен.',0);
					$Result = true;
				}
				else
				{
					$this->ErrorHandle('Ошибка сохранения объекта типа '.get_class($this).'.',2);
					$Result = true;
				}
			}

		}

		// TODO 4 -o Natali -c Сделать:  GetStatus() - установка издражения статуса


		

		public function ProcessMessage()
		{
			if (isset($this->ProcessData['Action']))
			{
				switch ($this->ProcessData['Action'])
				{
					default : 
					{
						$Result = parent::ProcessMessage();
					}
				}
			}
			else
			{
				$this->ErrorHandle('Переданы неверные параметры для события.',2);
				$Result = false;
			}
			return $Result;
		}



		// TODO 4 -o Natali -c Не используется: 
		public function GetDuration()
		{
			$W = 604800;
			$D = 86400;
			$H = 3600;
			$M = 60;

			$WD_Start = 9*$H + 0*$M;
			$WD_Finish = 18*$H + 0*$M;

			$Start00 = GetBeginOfDay($this->InitDate);
			$Start09 = GetBeginOfWorkDay($this->InitDate);
			$Start18 = GetEndOfWorkDay($this->InitDate);

			$WD_Duration = $Start18 - $Start09; 


			$Start = max($this->InitDate,$Start09);
			if ($Start > $Start18) $Start = $Start09 + $D;

			$Finish00 = GetBeginOfDay($this->FinishDate);
			$Finish09 = GetBeginOfWorkDay($this->FinishDate);
			$Finish18 = GetEndOfWorkDay($this->FinishDate);

			$Finish = min($this->FinishDate,$Finish18);
			if ($Finish < $Finish09)  $Finish  = $Finish18 - $D;

			if ($Finish <=$Start) return 0;

			$DurationTotal = ($Finish - $Start);

			$Days = $DurationTotal % $D;

			$Tail = $DurationTotal - $Days * $D;

			if ($Tail > $D - $WD_Duration)
			{
				$Days = $Days +1;
				$Tail = $Tail - ($D - $WD_Duration);
			}

			$Houres = $Tail % $H;
			$Tail = $Tail - $Houres * $H;

			$Minutes = $Tail % $M;

			$TimeLine = '';
			if ($Days > 0)      $TimeLine = $Days.' дней';
			if ($Houres > 0)    $TimeLine = $TimeLine . ($TimeLine = ''?'':' ').$Houres.' часов';
			if ($Minutes > 0)   $TimeLine = $TimeLine . ($TimeLine = ''?'':' ').$Minutes.' минут';

			$TimeLine = $TimeLine . ($TimeLine = ''?'':'.');
			return $TimeLine;
		}

		public function CreateView()
		{
			$this->ErrorHandle("Project CreateView()");
			return parent::CreateView();
		}


		public function GetState()
		{
			$FT = ($this->FinishDate - $this->StartDate);
			$CT = mktime() - $this->StartDate;

			$Status = $this->ReadyState * $FT / $CT;
			if ( $Status >= 100 or $Status < 0) 
				return 'green';
			elseif ( $Status > 90 ) 
				return 'yellow';
			else 
				return 'red';
		}

		public function __get($FieldName)
		{
			switch (strtolower($FieldName))
			{
				case 'state'            : return $this->GetState();
				case 'duration'         : return $this->GetDuration();
				case 'startdatetext'    : return date('d.m.y',$this->StartDate);
				case 'finishdatetext'   : return date('d.m.y',$this->FinishDate);
				case 'initdatetext'     : return date('d.m.y',$this->InitDate);
				case 'statebegin'       :{    
					if ($this->StartDate < $_SESSION['CurrentWPTime'] and $_SESSION['CurrentWPTime'] < $this->FinishDate)
						return $this->State."-TimeLine-begin";
					elseif ($_SESSION['CurrentWPTime'] > $this->FinishDate)
						return $this->State."-TimeLine-end";  
					else
						return "None";
				}
				case 'stateend'         :{   
					$int = new Interval($null,$null,$this->DataBase,$_SESSION['CurrentInt']);     
					$EndTime = $_SESSION['CurrentWPTime'] + 16 * $int->Duration; 

					if ($this->InitDate < $EndTime and $EndTime < $this->FinishDate)
					{   
						return $this->State."-TimeLine-end";
					}
					elseif ($EndTime < $this->InitDate and $EndTime < $this->StartDate)
					{   
						return $this->State."-TimeLine-begin"; 
					} 
					else
						return "None";
				}
			}

		}

		
		
		 
		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,$ID);
			$this->Refresh();     
		}
		
		
		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}

	}
?>
