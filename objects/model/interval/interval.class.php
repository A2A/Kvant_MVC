<?php
	class Interval extends BaseClass
	{
		public $ID;
		public $Duration;
		public $Text;

		public static $Forms = array(
		'interval' => 'objects/model/interval/interval.html',
		);

		public function __construct(&$ProcessData,$ID)
		{
			parent::__construct($ProcessData,$ID);
			if      (isset($ID))                $this->ID = $ID;
			elseif  (isset($ProcessData['ID']))    $this->ID = $ProcessData['ID'];
			else                            $this->ID = 1;

			switch (intval($this->ID))
			{
				case 1: 
				{
					$this->Text = '15 мин';
					$this->Duration = 15*60;
					break;
				}
				case 2: 
				{
					$this->Text = '30 мин';
					$this->Duration = 30*60;
					break;
				}
				case 3: 
				{
					$this->Text = 'час';
					$this->Duration = 60*60;
					break;
				}
				case 4: 
				{
					$this->Text = 'день';
					$this->Duration = 24*60*60;
					break;
				}
				case 5: 
				{
					$this->Text = 'неделя';
					$this->Duration = 7*24*60*60;
					break;
				}

			}
		}

		public function __get($FieldName)
		{
			switch (strtolower($FieldName))
			{
				case 'state' : 
				{
					$res = (intval($_SESSION['CurrentIntID']) == $this->ID)?'active':'passive';
					break;
				}
				default: $res = null;
			}
			return $res;
		}
		
		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}
	} 
?>
