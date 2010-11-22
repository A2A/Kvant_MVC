<?php
	class TPETypeList extends CollectionDB
	{
		protected $DBTableName = 'tpe_types';    
		public static $Forms = array(
		'list_select' => 'objects/model/tpe_type/select.html',
		'list' => 'objects/model/tpe_type/tpe_type.html',
		);

		
		
		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,'TPEType');
			$this->Refresh();
	 
		}
		
		
		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}
	  
	}  
?>
