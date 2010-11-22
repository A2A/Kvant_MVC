<?php
	class Filter extends CollectionDB
	{
		protected $DBTableName = '';    
		public static $Forms = array(
		'list_select' => 'objects/model/filter/select.html',
		'tpe_types' => 'objects/model/filter/tpe_types.html',
		'tpe_class' => 'objects/model/filter/tpe_class.html',
		);

		
		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,'Filter');
			$this->Refresh();
		}
		
		
		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}
		
		protected function Refresh()
		{
			$null = null;
			// TODO 1 -o Natali -c Функционал: не работает ФИЛЬТР, точнее не получить данные из  ViewData ($_GET)
			$this->DBTableName = 'tpe_types';//$this->ViewData['SubObject'];
			$sql = 'Select * from '.$this->DBTableName;
			
			if (isset($this->ViewData['Filter']) and is_array($this->ViewData['Filter']))
			{
				$Conditions = '';
				foreach ($this->ViewData['Filter'] as $FilterRec)
				{
					$Conditions = $Conditions.($Conditions==''?'':' and ').$this->CreateQueryFilter($FilterRec);
				}
				if ($Conditions != '') $sql .= ' where '.$Conditions;
			}
				
			$hSql = DBMySQL::Query($sql);   
			$ClassName = $this->_ValueType;
			while ($fetch = DBMySQL::FetchObject($hSql)) 
			{
				//$this->add($ClassName::GetObject($null,$fetch->ID));
			}
		}
	}  
?>
