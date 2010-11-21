<?php
	class WorkBlockList extends CollectionDB
	{
		public static  $Forms = array(
		'gant_header' => 'objects/model/workblock/gh_wblist.html',
		'task_status' => 'objects/model/workblock/task_status.html',
		'project_status' => 'objects/model/workblock/project_status.html',
		);

		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,'WorkBlock');
		
			$this->add(WorkBlock::GetObject($null,1));
			$this->add(WorkBlock::GetObject($null,2));
			$this->add(WorkBlock::GetObject($null,3));
			$this->add(WorkBlock::GetObject($null,4));
			$this->add(WorkBlock::GetObject($null,5));
			$this->add(WorkBlock::GetObject($null,6));
			$this->add(WorkBlock::GetObject($null,7));
			$this->add(WorkBlock::GetObject($null,8));
			$this->add(WorkBlock::GetObject($null,9));
			$this->add(WorkBlock::GetObject($null,10));
			$this->add(WorkBlock::GetObject($null,11));
			$this->add(WorkBlock::GetObject($null,12));
			$this->add(WorkBlock::GetObject($null,13));
			$this->add(WorkBlock::GetObject($null,14));
			$this->add(WorkBlock::GetObject($null,15));
			$this->add(WorkBlock::GetObject($null,16));
		}

		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}

	}
?>
