<?php
	class ContractorList extends CollectionDB
	{
		protected $DBTableName = 'contractors';
		protected $Forms = array(
		'list' => 'objects/model/contractor/list.html',
		);

		protected static $SQLFields = array(
		);

		
		protected function __construct($ProcessData,$ViewData,$DataBase,$ID)
		{

			parent::__construct($ProcessData,$ViewData,$DataBase,'User');
			$this->Refresh();
		}

		static public function GetObject(&$ProcessData,&$ViewData,&$DataBase,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$ViewData,$DataBase,$id,__CLASS__);
		}
	
	}  
?>
