<?php
	class Root extends AbstractTemplate 
	{
		protected $SystemObject;
		protected $ProcessObject;
		protected $ViewObject;

		protected $TemplateFileName;

		public function __construct(&$ProcessData,&$ViewData)  
		{
			$this->DataBase = new DBMySQL();
			parent::__construct($ProcessData,$ViewData,$this->DataBase);
			$this->SystemObject = System::GetObject($ProcessData,$ViewData,$this->DataBase);
		}
		public function ProcessMessage()
		{
			$null = null;
			$this->ActionStatus = false;

			if (isset($this->ProcessData['Object']))
			{
				$ClassName = $this->ProcessData['Object'];
				if (method_exists($ClassName,"ProcessMessage"))
				{
					$this->ProcessObject = $ClassName::GetObject($this->ProcessData,$null,$this->DataBase);
					$this->ActionStatus = $this->ProcessObject->ProcessMessage();
					$this->ImportState($this->ProcessObject);
				}
				else
				{
					ErrorHandle::ErrorHandle('Класс - '.$ClassName.' не имеет функции обработки событий.',3);
				}
			}
			else
			{
				ErrorHandle::ErrorHandle('Не передан объект для обработки запроса.',3);
			}

			$result = $this->SystemStatusOutput();
			return $result;
		}

		public function CreateView()
		{
			$null = null;
			if (isset($this->ViewData['Ajax']) and $this->ViewData['Ajax']=='1')
			{
				if (isset($this->ViewData['Object']))
				{
					$ClassName = $this->ViewData['Object'];
					if (($ClassName::$UnAuthUse == true) or $this->SystemObject->IsUserAuthorized())
					{
						$Object = $ClassName::GetObject($null,$this->ViewData,$this->DataBase);
						if (method_exists($ClassName,"CreateView"))
						{
							$result = $Object->CreateView();
							$this->ImportState($Object);
							$result = str_replace('<!--#error_field#-->',$this->SystemStatusOutput(),$result);
						}
						else
						{
							ErrorHandle::ErrorHandle('Класс - '.$ClassName.' не имеет функции обработки запроса.',3);
							$result = $this->SystemStatusOutput();
						}
					}
					else
					{
						ErrorHandle::ErrorHandle('Класс - '.$ClassName.' не может использоваться неавторизованным пользователем.',3);
						$result = $this->SystemStatusOutput();
					}
				}
				else
				{
					ErrorHandle::ErrorHandle('Не передан объект для обработки запроса.',3);
					$result = $this->SystemStatusOutput();
				}
			}
			elseif ($this->SystemObject->IsUserAuthorized())
			{
				if (isset($this->ViewData['Object']))
				{
					$ClassName    = $this->ViewData['Object'];
					$Object = $ClassName::GetObject($null,$this->ViewData,$this->DataBase);
					if (method_exists($ClassName,"CreateView"))
					{
						$result = $Object->CreateView();
						$this->ImportState($Object);
						$this->Request = str_replace('<!--#error_field#-->',$this->SystemStatusOutput(),$result);
					}
					else
					{
						ErrorHandle::ErrorHandle('Класс - '.$ClassName.' не имеет функции построения отображения.',3);
						$this->Request = $this->SystemStatusOutput();
					}
				}
				$result = $this->TemplateGetView('html/_index_full.html');
				$result = str_replace('<!--#error_field#-->',$this->SystemStatusOutput(),$result);  
			}
			else
			{
				$result = $this->TemplateGetView('html/index_login.html');
				$result = str_replace('<!--#error_field#-->',$this->SystemStatusOutput(),$result);  
			}
			return $result;
		}

		public static function GetObject(&$ProcessData,&$ViewData,&$DataBase,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$ViewData,$DataBase,$id,__CLASS__);
		}
	}
?>
