<?php
	class TPEClassList extends CollectionDB
	{
		protected $DBTableName = 'tpe_class';    
		public static $Forms = array(
		'list_select' => 'objects/model/tpe_class/select.html',
		'list' => 'objects/model/tpe_class/tpe_class.html',
		);

		
		
		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,'TPEClass');
			$this->Refresh();
	 
		}
		
		
		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}
	  
	}  
?>
