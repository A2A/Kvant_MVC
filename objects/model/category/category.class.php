<?php
	class Category extends Entity
	{
		protected $DBTableName = 'cards_types';    
		public static $Forms = array(
		'edit' => 'objects/model/category/edit.html',
		'view' => 'objects/model/category/view.html',
		);

		public $ColorValue = "green";
		public $DRUID;
		
		protected static $SQLFields = array(
		'Description' => 'DESCRIPTION'
		);
		
		
		static public function GetSQLField($Field)
		{
			return @ Category::$SQLFields[$Field];
		}

		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,$ID);
			$this->Refresh();     
		}

		public function Refresh()
		{
			$null = null;
			if (intval($this->ID))
			{
				$sql = 'Select * from '.$this->DBTableName.' where ID = '.$this->ID;

				$hSql = DBMySQL::Query($sql);
				while ($fetch = DBMySQL::FetchObject($hSql)) 
				{
					$this->Description = $fetch->DESCRIPTION;
				}
			}
			// TODO 1 -o Natali -c Заглушка Переписать: возврат DRUID
			$this->DRUID = $_GET['Filter'][0]['Val'];
		}

		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}
	}  
?>
	
