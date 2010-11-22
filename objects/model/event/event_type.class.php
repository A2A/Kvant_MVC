<?php
	class EventType extends Entity
	{
		protected $DBTableName = 'event_types';
		protected $Forms = array(
		'event' => 'objects/event/description.html'
		);

		// TODO 1 -o Natali -c Функционал: проверка "рабочего стола" пользователя, закрепление событий
		public $Dock = 'nodock';
		
		public function __construct(&$ProcessData,&$ViewData,&$DataBase,$ID=null)  
		{   
			parent::__construct($ProcessData,$ViewData,$DataBase,$ID);
			$this->Refresh();  
			//echo "<hr>". $ID;
			//print_r($this); 
		}

	
		static public function GetObject(&$ProcessData,&$ViewData,&$DataBase,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$ViewData,$DataBase,$id,__CLASS__);
		}
		
		public function Refresh()
		{
			if (is_int($this->ID))
			{
				//$this->Modified = false;
				$sql = 'Select * from '.$this->DBTableName.' where ID = '.$this->ID;
				// TODO 10 -o N -c Сообщение для отладки: SQL
				$this->ErrorHandle($sql);
				
				$hSql = $this->DataBase->Query($sql);
				while ($fetch = $this->DataBase->FetchObject($hSql)) 
				{
					$this->Description = $fetch->DESCRIPTION;
				}
			}
		}

	}
?>
