<?php
	class WorkBlockList extends CollectionBasic
	{
		public static  $Forms = array(
		'gant_header' => 'objects/model/workblock/gh_wblist.html',
		'task_status' => 'objects/model/workblock/task_status.html',
		'project_status' => 'objects/model/workblock/project_status.html',
		);
        public static  $Cashable = false;      
		public $ProjectID;
		
		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,'WorkBlock');
			//print_R($ProcessData);                //
			$Data = array();
			@ $this->ProjectID = $Data['ProjectID'] = $ProcessData['ProjectID'];
			@ $Data['TaskID'] = $ProcessData['TaskID'];
			//echo "<hr>";
			$this->add(WorkBlock::GetObject($Data,1));
			$this->add(WorkBlock::GetObject($Data,2));
			$this->add(WorkBlock::GetObject($Data,3));
			$this->add(WorkBlock::GetObject($Data,4));
			$this->add(WorkBlock::GetObject($Data,5));
			$this->add(WorkBlock::GetObject($Data,6));
			$this->add(WorkBlock::GetObject($Data,7));
			$this->add(WorkBlock::GetObject($Data,8));
			$this->add(WorkBlock::GetObject($Data,9));
			$this->add(WorkBlock::GetObject($Data,10));
			$this->add(WorkBlock::GetObject($Data,11));
			$this->add(WorkBlock::GetObject($Data,12));
			$this->add(WorkBlock::GetObject($Data,13));
			$this->add(WorkBlock::GetObject($Data,14));
			$this->add(WorkBlock::GetObject($Data,15));
			$this->add(WorkBlock::GetObject($Data,16));
		}

		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}

	}
?>
