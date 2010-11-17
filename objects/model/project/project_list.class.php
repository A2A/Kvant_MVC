<?php
    class ProjectList extends CollectionDBTemplated
    {
        protected $DBTableName = 'projects';
        protected $Forms = array(
        'list_menu' => 'objects/project/list_menu.html',
        'gant_line' => 'objects/project/gant_line.html',
        );

        public function __construct($ProcessData,$ViewData,$DataBase)
        {

            parent::__construct($ProcessData,$ViewData,$DataBase,'Project');
            $this->Refresh();
        }

        static public function GetObject(&$ProcessData,&$ViewData,&$DataBase,$id=null)
        {
            return static::GetObjectInstance($ProcessData,$ViewData,$DataBase,$id,__CLASS__);
        }
    }
?>
