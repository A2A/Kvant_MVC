<?php
	class ScoreCardList extends CollectionDB
	{
		protected $DBTableName = 'cards';
		public static $Forms = array(
		'list' => 'objects/model/scorecard/list.html',
		);

		public static $SQLFields = array(
		'UserID' => 'UserID',
		'RoleID' => 'RoleID',
		'UserDescr' => 'UserDescr',
		'RoleDescr' => 'RoleDescr'
		);

		public function __construct($ProcessData)
		{

			parent::__construct($ProcessData,'ScoreCard');
			$this->Refresh();
		}

		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}
	
	}  
?>
