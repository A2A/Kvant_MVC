<?php
	class WorkBlock extends BaseClass
	{
		public $ID;
		public $ProjectID;
		public $TaskID;

		public static  $Forms = array(
		'gant_header' => 'objects/model/workblock/gh.html',
		'td_status' => 'objects/model/workblock/td_status.html',
		);

		public function __construct($ProcessData,$ID)
		{
			if (isset($ProcessData['ProjectID']))
			{
				//print_R($ProcessData); 
				//echo "<hr>";
			}
			parent::__construct($ProcessData,$ID);
		
			if (isset($ID) and (intval($ID)>0)) $this->ID = intval($ID);
			elseif (isset($ProcessData['ID']) and (intval($ProcessData['ID'])>0)) $this->ID = intval($ProcessData['ID']);
			else
				$this->ID = 1;

			if (isset($ProcessData['ProjectID']) and (intval($ProcessData['ProjectID'])>0)) 
			{
				$this->ProjectID = intval($ProcessData['ProjectID']);
			}
			if (isset($ProcessData['TaskID'])) $this->TaskID = $this->ProcessData['TaskID'] = $ProcessData['TaskID'];
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
					$int = Interval::GetObject($null,$_SESSION['CurrentIntID']);
					$res = $int->Duration;
					break;
				}
				case 'start' : 
				{
					$int = Interval::GetObject($null,$_SESSION['CurrentIntID']);  

					if (isset($_SESSION['CurrentWPTime'])) $StartWPTime = $_SESSION['CurrentWPTime'];
					else $StartWPTime = time() - 8*$int->Duration;
					$res = $StartWPTime + $int->Duration*($this->ID-1);
					break;
				}
				case 'finish' : 
				{
					$int = Interval::GetObject($null,$_SESSION['CurrentIntID']);  

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
					$int = Interval::GetObject($null,$_SESSION['CurrentIntID']);  

					if (isset($_SESSION['CurrentWPTime'])) 
						$StartWPTime = $_SESSION['CurrentWPTime'];
					else 
						$StartWPTime = time() - 8*$int->Duration;

					if ($_SESSION['CurrentIntID'] <= 3)
						$res = date('H:i',$StartWPTime + $int->Duration*($this->ID-1));
					else
						$res = date('d.m',$StartWPTime + $int->Duration*($this->ID-1));

					break;
				}
				case 'state': 
				{  
					if (intval($this->ProjectID)>0)
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
						
						if ($Obj->ReadyState == 100) 
						{  
							
							if ($Obj->ReadyState > (100 / $DrawLength * ($CurLength-2)))
							{   
								if($Obj->ReadyState > (100 / $DrawLength * ($CurLength-1)) and ($this->ID <= $FinishInt and 16 != $this->ID))
								{
									 $res = 'prev'; 
								}
								else
								{
									$res = 'stop';    
								}
							}
							elseif($DrawLength!=17)
							{
								$res = 'next';
							}
							else
							   $res = 'stop';
						}
						else
						{
							if ($Obj->ReadyState >= (100 / $DrawLength * ($CurLength-1)) and $StartInt>0)
							{
								$res = ($Obj->ReadyState >= (100 / $DrawLength * ($CurLength)) and $CurLength < $DrawLength) ? 'prev':'stop';
							}
							elseif($DrawLength!=17)
							{
								$res = 'next';
							}
							else
							   $res = 'stop';
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
						$res = '&nbsp;';    
					}

					break;
				}
				default: $res = null;
			}
			return $res;
		}
		
		static public function GetObject(&$ProcessData,$id=null)
		{
			return new WorkBlock($ProcessData,$id);
		}

	} 
?>
