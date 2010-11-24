<?php
	class ContractorList extends CollectionDB
	{
		protected $DBTableName = 'contractors';
		protected $_Collection = array();
		public static $count;
		
		public static $Forms = array(
		'list' => 'objects/model/contractor/list.html',
		'selectbox' => 'objects/model/contractor/selectbox.html',
		);

		
		protected function __construct($ProcessData)
		{

			parent::__construct($ProcessData,'Contractor');
			$this->Refresh();
			$this->count = count($this->_Collection,0); 
		}

		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}
	
	}  
?>
