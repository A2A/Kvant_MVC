<?php
	class CollectionDB extends CollectionBasic
	{
		protected $DBTableName;


		protected function CreateQueryFilter($FilterRec)
		{
			$Condition = '';
			if (isset($FilterRec['Oper']) and array_key_exists ('Val',$FilterRec) and isset($FilterRec['Field']))
			{
				$ClassName = $this->_ValueType;
				$this->EncodingNedeed = 0;
				// TODO 1 -o Nata -c Ошибки: проверить перекодирование полученых параметров.
				$Val = $this->EncodingNedeed ? (iconv("windows-1251","UTF-8",  $FilterRec['Val'])) : $FilterRec['Val'];

				if ($SQLField = $ClassName::GetSQLField($FilterRec['Field']))
				{
					if (is_null($Val) or (trim($Val)===''))
					{
						switch ($FilterRec['Oper'])
						{
							case 'eq': $Condition = ' '.$SQLField. ' IS NULL ' ; break;
							case '!eq': $Condition = ' '.$SQLField. ' IS NOT NULL '; break;
							case 'lt': $Condition = '  false ' ; break;
							case '!lt': $Condition = '  false ' ; break;
							case '!lt': $Condition = '  false ' ; break;
							case 'gt': $Condition = '  false ' ; break;
							case '!gt': $Condition = '  false ' ; break;
							case 'like': $Condition = ' true ' ; break;
							case '!like': $Condition = ' false ' ; break;
							default : $Condition = '';
						}
					}
					else
					{
						switch ($FilterRec['Oper'])
						{
							case 'eq': $Condition = ' '.$SQLField. ' = "'.$Val.'" ' ; break;
							case '!eq': $Condition = ' '.$SQLField. ' != "'.$Val.'" ' ; break;
							case 'lt': $Condition = ' '.$SQLField. ' < "'.$Val.'" ' ; break;
							case '!lt': $Condition = ' '.$SQLField. ' >= "'.$Val.'" ' ; break;
							case '!lt': $Condition = ' '.$SQLField. ' >= "'.$Val.'" ' ; break;
							case 'gt': $Condition = ' '.$SQLField. ' > "'.$Val.'" ' ; break;
							case '!gt': $Condition = ' '.$SQLField. ' <= "'.$Val.'" ' ; break;
							case 'like': $Condition = ' '.$SQLField. ' like ("%'.$Val.'%") ' ; break;
							case '!like': $Condition = ' '.$SQLField. ' not like ("%'.$Val.'%") ' ; break;
							default : $Condition = '';
						}
					}

				} 
			}
			return $Condition;

		}

		protected function Refresh()
		{
			
			$null = null;
			$sql = 'Select ID from '.$this->DBTableName;
			if (isset($this->ProcessData['Filter']) and is_array($this->ProcessData['Filter']))
			{
				$Conditions = '';
				foreach ($this->ProcessData['Filter'] as $FilterRec)
				{
					$Conditions = $Conditions.($Conditions==''?'':' and ').$this->CreateQueryFilter($FilterRec); 
				}
				if ($Conditions != '') $sql .= ' where '.$Conditions; 
				
				
				  
			
			}
			
			// TODO 10 -o N -c Сообщение для отладки: SQL    		
			ErrorHandle::ErrorHandle($sql);	
			
			//$sql .= " limit 4";
			$hSql = DBMySQL::Query($sql);
			while ($fetch = DBMySQL::FetchObject($hSql)) 
			{
				$ClassName = $this->_ValueType;
				$this->add($ClassName::GetObject($null,$fetch->ID));
			}
		}

	}
?>
