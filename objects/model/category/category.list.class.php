<?php
	class CategoryList extends CollectionDB
	{
		protected $DBTableName = 'cards_types';
		public static $Forms = array(
		'list' => 'objects/model/category/list.html',
		);

		public static $SQLFields = array(
		'UserID' => 'UserID',
		'RoleID' => 'RoleID',
		'UserDescr' => 'UserDescr',
		'RoleDescr' => 'RoleDescr'
		);

		public function __construct($ProcessData)
		{

			parent::__construct($ProcessData,'Category');
			$this->Refresh();
			//print_r($this);
		}
		
		public function __get($FieldName)
		{
			/*switch($FieldName)
			{
				case "" : return "";
				case "" : return "";
				case "" : return "";
			} */
		}

		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}
	
	}  
?>
