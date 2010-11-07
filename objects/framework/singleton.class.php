<?php
	abstract class Singleton
	{
		private static $Cash;
		protected $ProcessData;

		protected function __construct(&$ProcessData,$id=null)  
		{   
			$this->ProcessData = &$ProcessData;
			$this->DataBase = DBMySQL::GetDB();

			if (property_exists(get_class($this),'ID'))
			{

				if (is_numeric($id)) $this->ID = $id;
				elseif (isset($ProcessData['ID']) and is_numeric($ProcessData['ID'])) $this->ID = $ProcessData['ID'];

				if (is_null($this->ID)) Singleton::$Cash[get_class($this)][$this->ID] = &$this;
			}
			else
			{
				Singleton::$Cash[get_class($this)][null] = &$this;
			}
		}    

		final function GetObjectInstance(&$ProcessData,$id,$ClassName)
		{
			if (property_exists($ClassName,'ID'))
			{
				if (is_numeric($id)) $TmpID = $id;
				elseif (isset($ProcessData['ID']) and is_numeric($ProcessData['ID'])) $TmpID = $ProcessData['ID'];
				elseif (isset($ViewData['ID']) and is_numeric($ViewData['ID'])) $TmpID = $ViewData['ID'];
				else $TmpID = null;

				if (is_null($TmpID))
				{
					$Obj = new $ClassName($ProcessData,$TmpID);
				}
				elseif ((!isset(self::$Cash[$ClassName])) or (!isset(self::$Cash[$ClassName][$TmpID])))
				{
					$Obj = new $ClassName($ProcessData,$TmpID);
				}
				else
				{
					$Obj = self::$Cash[$ClassName][$TmpID];
				}
			}
			else
			{
				$Obj = new $ClassName($ProcessData);
			}
			return $Obj ;
		}

		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}

	}

?>
