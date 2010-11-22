<?php
	class IntervalList extends CollectionDB
	{
		protected $DBTableName = '';
		public static $Forms = array(
		'list' => 'objects/model/interval/interval_list.html',
		);

		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,'Interval');
			$this->add(Interval::GetObject($null,1));
			$this->add(Interval::GetObject($null,2));
			$this->add(Interval::GetObject($null,3));
			$this->add(Interval::GetObject($null,4));
			$this->add(Interval::GetObject($null,5));
		}
		
		
		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}
	}

?>
