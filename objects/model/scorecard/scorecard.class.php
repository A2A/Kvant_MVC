<?php
	class ScoreCard extends Entity
	{
		protected $DBTableName = 'cards';    
		public static $Forms = array(
		'edit' => 'objects/model/scorecard/edit.html',
		'view' => 'objects/model/scorecard/view.html',
		);

		protected static $SQLFields = array(
		'Description' => 'DESCRIPTION',
		'CategoryID' => 'CATID',
		);
		
		public $ColorValue = "yellow";
		public $DRUID;
		
		
		
		static public function GetSQLField($Field)
		{
			return @ ScoreCard::$SQLFields[$Field];
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
	
