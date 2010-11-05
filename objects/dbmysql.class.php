<?php 
	class DBMySQL
	{
		private static $Instance;
		
		protected $TablePrefix;    

		protected $DB; 
		protected $DBName; 

		protected $HostName;
		protected $UserName;
		protected $Password;   


		static public function GetDB()
		{
			if (isset(static::$Instance))
			{
				return static::$Instance;
			}
			else
			{
				$ClassName = __CLASS__;
				return new $ClassName();
			}
		}
		
		protected function __construct($Host = "localhost", $User = "root", $Password = "19525Qi45", $DBName = "test", $Prefix = "") 
		{
			$this->HostName = $Host;
			$this->UserName = $User;
			$this->Password = $Password;
			$this->DBName = $DBName;
			$this->TablePrefix = $Prefix;

			try
			{
				$Hahdler = mysql_pconnect($this->HostName, $this->UserName, $this->Password);
				if ($Hahdler  == false)
				{
					//$this->Connected = false;
				}
				else
				{
					$this->DB = $Hahdler;
					//$this->Connected = true;
				}
				//$this->Connected = mysql_select_db($this->DBName, $this->DB);
				mysql_select_db($this->DBName, $this->DB);
			}
			catch (BasicException $excep)
			{
				throw new  BasicException(mysql_error($res), mysql_errno($res));
			}      
		}

		public function __destruct()
		{
			@ mysql_close($this->DB);
		}

		public function __toString() 
		{
			return $this->DB;
		}

		public function Query($Sql)
		{
			try
			{   
				$Resultat = mysql_query($Sql,$this->DB); 
				return $Resultat;
			}
			catch (BasicException $excep)
			{
				throw new  BasicException(mysql_error($Resultat), mysql_errno($Resultat));
			}      
		}

		public function InsertID($Resultat)
		{
			try
			{
				$Id = mysql_insert_id();   
				return $Id;
			}
			catch (BasicException $excep)
			{
				throw new  BasicException(mysql_error($res), mysql_errno($res));
			}      
		}

		public function CountRows($Resultat)
		{
			try
			{
				$Num = mysql_num_rows($Resultat);   
				return $Num;
			}
			catch (BasicException $excep)
			{
				throw new  BasicException(mysql_error($res), mysql_errno($res));
			}      
		}

		public function FetchObject($Resultat)
		{     

			try
			{
				$Rows = mysql_fetch_object($Resultat); 
				return $Rows;
			}
			catch (BasicException $excep)
			{
				throw new  BasicException(mysql_error($Resultat), mysql_errno($Resultat));
			}      
		} 

		public function FetchArray($Resultat)
		{
			try
			{
				$Rows = mysql_fetch_assoc($Resultat); 
				return $Rows;
			}
			catch (BasicException $excep)
			{
				throw new  BasicException(mysql_error($res), mysql_errno($res));
			}
			catch (Exception $excep)
			{

			}      
		}

		public function Ping()
		{
			return mysql_ping($this->DB);
		}
	}     
?>
