<?php
	abstract class Singleton
	{
		private static $Cash;
		protected $ProcessData;
        public static  $Cashable = false;

		protected function __construct(&$ProcessData,$id=null)  
		{   
			$this->ProcessData = &$ProcessData;

			if (property_exists(get_class($this),'ID'))
			{
				if (is_numeric($id)) $this->ID = $id;
				elseif (isset($ProcessData['ID']) and is_numeric($ProcessData['ID'])) $this->ID = $ProcessData['ID'];
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
				elseif ((!isset(Singleton::$Cash[$ClassName])) or (!isset(Singleton::$Cash[$ClassName][$TmpID])))
				{
                    $Obj = new $ClassName($ProcessData,$TmpID);                    
                    Singleton::$Cash[$ClassName][$TmpID] = &$Obj;
				}
				else
				{
					$Obj = Singleton::$Cash[$ClassName][$TmpID];
				}
			}
			elseif  ($ClassName::$Cashable)
            {
                if ((!isset(Singleton::$Cash[$ClassName])) or (!isset(Singleton::$Cash[$ClassName][null])))
                {
                    $Obj = new $ClassName($ProcessData);                    
                    Singleton::$Cash[$ClassName][null] = &$Obj;
                }
                else
                {
                    $Obj = Singleton::$Cash[$ClassName][null];
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
