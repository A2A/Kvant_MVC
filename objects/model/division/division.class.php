<?php
    class Division extends Entity
    {
        protected $DBTableName = 'division';    

        protected $ParentID;
        protected $ManagerID;
        public static $Forms = array(
        'edit' => 'objects/model/division/edit.html'
        );

        public function __construct(&$ProcessData,$ID=null)  
        {   
            parent::__construct($ProcessData,$ID);
            $this->Refresh();     
        }

        public function SaveAction()
        {
            if (!intval($this->ID))
            {
                $sql = 'select OBJECTID from ur_division where 
                ID = "'.intval($_SESSION['CurrentUserID']).'" and  OBJECTID = "'.intval($this->ParentID).'" and `CREATE`';

                $hSql = DBMySQL::Query($sql);
                if (!$hSql)
                {
                    ErrorHandle::ActionErrorHandle('Ошибка при проверке прав на запись нового объекта "Подразделение"',2);
                    return false;
                }
                elseif (DBMySQL::FetchObject($hSql))
                {
                    ErrorHandle::ActionErrorHandle('Недостаточно прав для записи нового объекта "Подразделение"',2);
                    return false;
                }

                $sql = 'insert into division (ID, DESCRIPTION,PARENTID,MANAGERID)
                values (NULL,
                "'.$this->Description.'",
                '.(is_numeric($this->ParentID)?$this->ParentID:'null').',
                '.(is_numeric($this->ManagerID)?$this->ManagerID:'null').')';

                // TODO 10 -o N -c Сообщение для отладки: SQL
                $hSql = DBMySQL::Query($sql);
                if ($hSql)
                {
                    $this->ID = DBMySQL::InsertID($hSql);
                    $this->ChangedFields[] = array('name' => 'ID','value' => $this->ID);
                    ErrorHandle::ActionErrorHandle('Объект типа '.get_class($this).' успешно создан.',0);
                    $Result = true;
                }
                else
                {
                    ErrorHandle::ActionErrorHandle('Ошибка создания объекта типа '.get_class($this).'.',2);
                    $Result = true;
                }
            }
            else
            {
                $sql = 'select OBJECTID from ur_division where 
                ID = "'.intval($_SESSION['CurrentUserID']).'" and OBJECTID = "'.intval($this->ID).'" and `WRITE`';

                $hSql = DBMySQL::Query($sql);
                if (!$hSql)
                {
                    ErrorHandle::ActionErrorHandle('Ошибка при проверке прав на запись нового объекта "Подразделение"',2);
                    return false;
                }
                elseif (DBMySQL::FetchObject($hSql))
                {
                    ErrorHandle::ActionErrorHandle('Недостаточно прав для записи нового объекта "Подразделение"',2);
                    return false;
                }
                //$this->Pause = (mktime() - $this->FinishDate);  

                $sql = 'update '.$this->DBTableName.' set 
                `DESCRIPTION`        = "'.$this->Description.'",
                `PARENTID`        = '.(intval($this->ParentID)?intval($this->ParentID):'null').',
                `MANAGERID`        = '.(intval($this->ManagerID)?intval($this->ManagerID):'null');

                $hSql = DBMySQL::Query($sql);
                if ($hSql)
                {
                    ErrorHandle::ErrorHandle('Объект подразделение успешно сохранен.',0);
                    $Result = true;
                }
                else
                {
                    ErrorHandle::ErrorHandle('Ошибка сохранения подразделение.',2);
                    $Result = true;
                }
            }
        }      


        protected function Refresh()
        {
            if (intval($this->ID) >0)
            {
            $System = System::GetObject();
                
                $this->Modified = false;

                $sql_base = 'Select * from division  where ID = '.intval($this->ID);
                $sql_filter = 'select OBJECTID from ur_division where ID = "'.$System->CurrentUserID.'" and `READ` and OBJECTID = '.intval($this->ID);
                $sql = 'Select buf.* from ('.$sql_base.') as buf cross join  ('.$sql_filter.') as perms on buf.ID =  perms.OBJECTID';          

                if (!($hSql = DBMySQL::Query($sql)))
                {
                    ErrorHandle::ErrorHandle('Ошибка при получении данных о подразделении № '.$this->ID,1);
                }
                elseif (!($fetch = DBMySQL::FetchObject($hSql)))
                {
                    ErrorHandle::ErrorHandle('Попытка получения несуществующего подразделения или недостаточно прав на просмотр получаемого подразделения №'.$this->ID,1);
                }
                else
                {
                    $this->Description = $fetch->DESCRIPTION;
                    $this->ParentID = $fetch->PARENTID;
                    $this->ManagerID = $fetch->MANAGERID;
                }
            }
        }

        public function __get($FieldName)
        {
            $null = null;
            switch ($FieldName)
            {
                case 'Parent' : $result = Division::GetObject($null,$this->ParentID); break;
                case 'Manager' : $result = User::GetObject($null,$this->ManagerID); break;
                default: $result = parent::__get($FieldName);
            }
            return $result;
        }

        static public function GetObject(&$ProcessData,$ID=null)
        {
            return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
        }
    }  
?>
	
