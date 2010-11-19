<?php
	class FilterList extends CollectionDBTemplated
	{
		protected $DBTableName = '';    
		protected $Forms = array(
		'list_select' => 'objects/filter/select.html',
		'tpe_types' => 'objects/filter/tpe_types.html',
		'tpe_class' => 'objects/filter/tpe_class.html',
		);

		
		public function __construct($ProcessData,$ViewData,$DataBase)
		{
			parent::__construct($ProcessData,$ViewData,$DataBase,'Filter');
			$this->Refresh();
		}
		
		
		static public function GetObject(&$ProcessData,&$ViewData,&$DataBase,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$ViewData,$DataBase,$id,__CLASS__);
		}
		
		
		protected function Refresh()
		{
			$null = null;
			$this->DBTableName = $this->ViewData['SubObject'];
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
			// TODO 10 -o N -c Сообщение для отладки: SQL
				$this->ErrorHandle($sql);    
				
			$hSql = $this->DataBase->Query($sql);   
			$ClassName = $this->_ValueType;
				 //$this->ErrorHandle($ClassName);    
			while ($fetch = $this->DataBase->FetchObject($hSql)) 
			{
				//print_r($fetch);
				$this->add($ClassName::GetObject($null,$this->ViewData,$this->DataBase,$fetch->ID));
			}
		}
	}  
?>
