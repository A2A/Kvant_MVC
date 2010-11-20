<?php
	class ProjectList extends CollectionDB
	{
		protected $DBTableName = 'projects';
		public static $Forms = array(
		'list_menu' => 'objects/model/project/list_menu.html',
		'gant_line' => 'objects/model/project/gant_line.html',
		);

		  public function __construct($ProcessData)
		{

			parent::__construct($ProcessData,'Project');
			$this->Refresh();
		}

		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}
	}
?>
