<?php
	class WorkBlock extends BaseClass
	{
		public $ID;
		public $ProjectID;
		public $TaskID;

		public static  $Forms = array(
		'gant_header' => 'objects/model/workblock/gh.html',
		);

		public function __construct(&$ProcessData,$ID)
		{
			if ($ID <= 2) print_R($ProcessData);
			
			parent::__construct($ProcessData,$ID);
		
			if (isset($ID) and (intval($ID)>0)) $this->ID = intval($ID);
			elseif (isset($ProcessData['ID']) and (intval($ProcessData['ID'])>0)) $this->ID = intval($ProcessData['ID']);
			else
				$this->ID = 1;

			if (isset($ProcessData['ProjectID'])) $this->ProjectID = $ProcessData['ProjectID'];
			if (isset($this->ProcessData['TaskID'])) $this->TaskID = $this->ProcessData['TaskID'];
			//print_r($this);
		}


		public function __toString()
		{
			return date('H:i d.m.Y',$this->start);
		}

		public function __get($FieldName)
		{
			//echo $FieldName ."==";
			$res = '';    
			switch (strtolower($FieldName))
			{
				case 'duration' : 
				{
					$int = Interval::GetObject($null,$_SESSION['CurrentInt']);
					$res = $int->Duration;
					break;
				}
				case 'start' : 
				{
					$int = Interval::GetObject($null,$_SESSION['CurrentInt']);  

					if (isset($_SESSION['CurrentWPTime'])) $StartWPTime = $_SESSION['CurrentWPTime'];
					else $StartWPTime = time() - 8*$int->Duration;
					$res = $StartWPTime + $int->Duration*($this->ID-1);
					break;
				}
				case 'finish' : 
				{
					$int = Interval::GetObject($null,$_SESSION['CurrentInt']);  

					if (isset($_SESSION['CurrentWPTime'])) $StartWPTime = $_SESSION['CurrentWPTime'];
					else $StartWPTime = time() - 8*$int->Duration;
					$res = $StartWPTime + $int->Duration*$this->ID;
					break;
				}

				case 'class' : 
				{
					$CurrentTime = time() - $this->Duration;

					if ($this->Start < $CurrentTime and $CurrentTime < $this->Finish)
						$res = "PolsunokLeft";
					elseif (($this->Start - $this->Duration) < $CurrentTime and $CurrentTime < ($this->Finish - $this->Duration))
						$res = "PolsunokRight"; 
					else
						$res = "";
					break;
				}

				case 'text' : 
				{
					$int = Interval::GetObject($null,$_SESSION['CurrentInt']);  

					if (isset($_SESSION['CurrentWPTime'])) 
						$StartWPTime = $_SESSION['CurrentWPTime'];
					else 
						$StartWPTime = time() - 8*$int->Duration;

					if ($_SESSION['CurrentInt'] <= 3)
						$res = date('H:i',$StartWPTime + $int->Duration*($this->ID-1));
					else
						$res = date('d.m',$StartWPTime + $int->Duration*($this->ID-1));

					break;
				}
				case 'state': 
				{   // все правильно расчитывает
					if (isset($this->ProcessData['ProjectID']) and is_numeric($this->ProcessData['ProjectID']))
					{
						$Obj = Project::GetObject($null,intval($this->ProcessData['ProjectID']));
						if (($Obj->StartDate > $this->Finish) or (!is_null($Obj->FinishDate) and $Obj->FinishDate <= $this->Start))
						{
							$res = 'None';
						}
						else
						{
							$res = $Obj->State;
						}
					}
					elseif (isset($this->ProcessData['TaskID']))
					{
						$Obj = Task::GetObject($null,intval($this->ProcessData['TaskID']));
						if (($Obj->StartDate > $this->Finish) or ((!is_null($Obj->FinishDate)) and $Obj->FinishDate < $this->Start))
						{
							$res = 'None';
						}
						else
						{
							$res = $Obj->State;
						}

					}
					else
					{
						$res = '';    
					}

					break;
				}

				case 'readystate':
				{
					if (isset($this->ProcessData['ProjectID']) and is_numeric($this->ProcessData['ProjectID']))
					{
						$Obj = Project::GetObject($null,intval($this->ProcessData['ProjectID']));
					}
					elseif (isset($this->ProcessData['TaskID']))
					{
						$Obj = Task::GetObject($null,intval($this->ProcessData['TaskID']));
					}
					else
					{
						$Obj = null;
					}

					if (!is_null($Obj))
					{
						//$int = new Interval($null,$null,$this->DataBase,$_SESSION['CurrentInt']);  

						$Step = $this->Duration;
						$StartInt = 0;
						$FinishInt = 16;
						for ($i=1;$i<=16;$i++)
						{
							$BlockFin = $_SESSION['CurrentWPTime'] + $i*$Step;
							if (($Obj->StartDate < $BlockFin) and ($BlockFin <= $Obj->FinishDate) and ($StartInt == 0))  
								$StartInt = $i;
							if (($Obj->StartDate < $BlockFin) and ($BlockFin <= $Obj->FinishDate))  
								$FinishInt = $i;
						}


						$DrawLength = $FinishInt - $StartInt + 1;
						$CurLength =  $this->ID - $StartInt + 1;
						//echo "||".$Obj->ReadyState."<br>";
						//echo $Obj->ReadyState." >= (100 / ".$DrawLength." * ".$CurLength.") = ".($Obj->ReadyState >= (100 / $DrawLength * $CurLength)) ."<br>";

						
						if ($Obj->ReadyState >= (100 / $DrawLength * ($CurLength - 1)))
						{
							$res = ($Obj->ReadyState >= (100 / $DrawLength * ($CurLength)) and $CurLength < $DrawLength) ? 'prev '.$CurLength:'stop';
						}
						else
						{
							$res = 'next '.$CurLength;
						}
					}
					else
					{
						$res = '';    
					}
					break;
				}
				case 'readystatetext':
				{
					if (isset($this->ProcessData['ProjectID']) and is_numeric($this->ProcessData['ProjectID']))
					{
						$Obj = Project::GetObject($null,intval($this->ProcessData['ProjectID']));
						if ($this->readystate == 'stop' and $this->state != 'None') 
							$res = $Obj->ReadyState.'<small>%</small>';
						else 
							$res = '&nbsp;';
					}
					elseif (isset($this->ProcessData['TaskID']))
					{
						$Obj = Task::GetObject($null,intval($this->ProcessData['TaskID']));
						if ($this->readystate == 'stop' and $this->state != 'None') 
							$res = $Obj->ReadyState.'<small>%</small>';
						else 
							$res = '&nbsp;';
					}
					else
					{
						$res = '';    
					}

					break;
				}
				default: $res = null;
			}
			return $res;
		}
		
		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}

	} 
?>
