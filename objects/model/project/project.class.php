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
		'shortinfo' 	=> 'objects/model/project/short_info.html',
		
		);

		protected $DBTableName = 'projects';

		public $ManagerID;
		public $Manager;
		public $ContractorID;
		public $Contractor;
		public $DRUID;

		public $InitDate;
		public $StartDate = null;
		public $FinishDate = null;

		public $ReadyState;
		public $FullDescription;
		public $CurrentStatusID;
		public $CurrentStatus;


		protected function Refresh()
		{
			$null = null;
			if (intval($this->ID))
			{
				$sql = 'Select * from '.$this->DBTableName.' where ID = '.$this->ID;
				$hSql = DBMySQL::Query($sql);
				while ($fetch = DBMySQL::FetchObject($hSql)) 
				{
					$sql="select USERID from `dru` where ID=".intval($fetch->DRUID);
					$UserSql = DBMySQL::Query($sql);
					if ($Userfetch = DBMySQL::FetchObject($UserSql) and is_numeric($Userfetch->USERID))
					{
						$this->ManagerID = $Userfetch->USERID;
						$this->Manager = new User($null,$this->ManagerID);
						$this->Manager->Refresh();
					}
					else
					{
						$this->ManagerID = null;
						$this->Manager   = "Постановщик не установлен";
					}

					$this->Description = $fetch->DESCRIPTION;
					$this->FullDescription = $fetch->FULL_DESCR;
					$this->FullDescription = $fetch->FULL_DESCR;

					$this->InitDate = strtotime($fetch->DATE_INIT);
					$this->StartDate = strtotime($fetch->DATE_START);
					
					$this->CurrentStatusID = intval($fetch->STATUSID);

					if (is_null($fetch->DATE_FINISH) or $fetch->DATE_FINISH == 0 or $fetch->DATE_FINISH == "")
						$this->FinishDate = time() + 24*3600*180;     
					else
						$this->FinishDate = strtotime($fetch->DATE_FINISH);

					if (is_null($fetch->READY_STATE) or $fetch->READY_STATE == "")
						$this->ReadyState = 0;
					else
						$this->ReadyState = $fetch->READY_STATE;
					
					if (isset($fetch->CONTRACTORID) and $this->ContractorID != intval($fetch->CONTRACTORID))
					{
						$this->ContractorID = intval($fetch->CONTRACTORID);
						if (!is_null($this->ContractorID))
						{
							$ClassName =  'Contractor';
							$this->Contractor = $ClassName::GetObject($null,$this->ContractorID);
						}
						else
						{
							$this->Contractor = null;
						}
					}
				

				}
			}
			else
			{
				$this->Manager = User::GetObject($null,$this->CurrentUserID);
				$this->ManagerID = $this->CurrentUserID;
				
				$tmpDate = mktime();
				$this->InitDate = $tmpDate;
				$this->InitDateText = DateTimeToStr($this->InitDate);

			}
		}

		
		protected function SetActionData()
		{
			parent::SetActionData();
			if (isset($this->ProcessData['DRUID']) and ($this->ProcessData['DRUID'] != $this->DRUID))
			{
				$this->DRUID = $this->ProcessData['DRUID'];
				
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
			if (isset($this->ProcessData['ContractorID']) and ($this->ProcessData['ContractorID'] != $this->ContractorID))
			{
				$this->ContractorID = $this->ProcessData['ContractorID'];
				$this->Contractor = null;
				$this->Modified = true;
			}
			if (isset($this->ProcessData['TPEClass']) and ($this->ProcessData['TPEClass'] != $this->TPEClass))
			{
				$this->TPEClass = $this->ProcessData['TPEClass'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['StatusID']) and ($this->ProcessData['StatusID'] != $this->StatusID))
			{
				$this->StatusID = $this->ProcessData['StatusID'];
				$this->Modified = true;
			}
			
			return $this->Modified; 
		}

		
		public function SaveAction()
		{
			$this->SetActionData();
			if (!intval($this->ID))
			{
				// TODO 4 -o Natali -c Ошибка формирования SQL запроса: при создании если не установлен пользователь, надо получить текущего для $this->UserID
				$sql = 'insert into '.$this->DBTableName.' (ID,`CLASSID`,`STATUSID`,`DESCRIPTION`,`DATE_INIT`,`DATE_START`,
				`DATE_FINISH`,`FULL_DESCR`,`DRUID`,`READY_STATE`,`CONTRACTORID`) 
				values (NULL,'.(intval($this->TPEClass)?intval($this->TPEClass):'null').',1,"'.$this->Description.'","'.DateTimeToMySQL($this->InitDate).'","'.DateTimeToMySQL($this->StartDate).'",
				"'.DateTimeToMySQL($this->FinishDate).'",
					"'.$this->FullDescription.'",'.(intval($this->DRUID)?intval($this->DRUID):'null').',"'.$this->ReadyState.'",'.(intval($this->ContractorID)?intval($this->ContractorID):'null').')';
		
				// TODO 4 -o Natali -c сообщение для отладки: SQL  
				ErrorHandle::ErrorHandle($sql);    

				$hSql = DBMySQL::Query($sql);
				if ($hSql)
				{
					$this->ID = DBMySQL::InsertID($hSql);
					$this->ChangedFields[] = array('name' => 'ID','value' => $this->ID);
					ErrorHandle::ActionErrorHandle('Объект типа '.get_class($this).' успешно сохранен.',0);
					$Result = true;
				}
				else
				{
					ErrorHandle::ActionErrorHandle('Ошибка сохранения объекта типа '.get_class($this).'.',2);
					$Result = true;
				}
			}
			else
			{
				
				$sql = 'update '.$this->DBTableName.' set 
					`STATUSID` = "'.((intval($this->StatusID) and $this->StatusID>0 and $this->StatusID<7)?intval($this->StatusID):'1').'",
					`DESCRIPTION` = "'.$this->Description.'",
					`DATE_START` = "'.DateTimeToMySQL($this->StartDate).'",
					`DATE_FINISH` = "'.DateTimeToMySQL($this->FinishDate).'",
					`FULL_DESCR` = "'.$this->FullDescription.'", 
					`READY_STATE` = "'.intval($this->ReadyState).'", 
					`CONTRACTORID` '.(intval($this->ContractorID)?"=".intval($this->ContractorID):'is null').' 
					where ID = '.$this->ID;
					
				// TODO 4 -o Natali -c сообщение для отладки: SQL 
				ErrorHandle::ErrorHandle($sql);       
				
				$hSql = DBMySQL::Query($sql);
				if ($hSql)
				{
					ErrorHandle::ActionErrorHandle('Объект типа '.get_class($this).' успешно сохранен.',0);
					$Result = true;
				}
				else
				{
					ErrorHandle::ActionErrorHandle('Ошибка сохранения объекта типа '.get_class($this).'.',2);
					$Result = true;
				}
			}
			return false;

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
					$int = new Interval($null,$null,$this->DataBase,$_SESSION['CurrentIntID']);     
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
			$System = System::GetObject();
			$this->CurrentUserID = $System->CurrentUserID;
			unset($System);
			$this->Refresh();     
		}
		
		
		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}

	}
?>
