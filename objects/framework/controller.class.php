<?php
	class Controller extends Singleton
	{

		protected function __construct(&$ProcessData)  
		{   
			parent::__construct($ProcessData,null);
		}

		public static function CheckActionPermissions($RequestedAction,$ClassName)
		{
			return true;
		}

		public static function CheckFormPermissions($RequestedForm,$ClassName)
		{
			return true;
		}

		public static function CheckClassPermissions($ClassName)
		{
			return true;
		}

		public static function CheckActionAccess($RequestedAction,$ClassName)
		{
			$AccessGranted = true;

			if (!Controller::CheckClassAccess($ClassName))
			{
					ErrorHandle::ErrorHandle('Нарушение прав доступа к объекту класса '.$ClassName.' при обработке события.',3);
					$AccessGranted = false;
			}
			elseif (!static::IsUserAuthorized())
			{
				if ((!isset($ClassName::$UnAuthActions[$RequestedAction])) or (!$ClassName::$UnAuthActions[$RequestedAction]))
				{
					ErrorHandle::ErrorHandle('Нарушение прав доступа к модулю обработки события "'.$RequestedAction.'".',3);
					$AccessGranted = false;
				}
			}
			else
			{
				$AccessGranted = static::CheckActionPermissions($RequestedAction,$ClassName);
			}

			return $AccessGranted;
		}

		public static function CheckFormAccess($RequestedForm,$ClassName)
		{
			$AccessGranted = true;

			if (!Controller::CheckClassAccess($ClassName))
			{
					ErrorHandle::ErrorHandle('Нарушение прав доступа к объекту класса '.$ClassName.' при построении отображения.',3);
					$AccessGranted = false;
			}
			elseif (!static::IsUserAuthorized())
			{
				if ((!isset($ClassName::$UnAuthForms[$RequestedForm])) or (!$ClassName::$UnAuthForms[$RequestedForm]))
				{
					ErrorHandle::ErrorHandle('Нарушение прав доступа к форме "'.$RequestedForm.'" класса "'.$ClassName.'".',3);
					$AccessGranted = false;
				}
			}
			else
			{
				$AccessGranted = static::CheckFormPermissions($RequestedForm,$ClassName);
			}

			return $AccessGranted;
		}

		public static function CheckClassAccess($ClassName)
		{
			$AccessGranted = true;
			if (!class_exists($ClassName))
			{
					ErrorHandle::ErrorHandle('Попытка создания несуществующего класса '.$ClassName.'.',3);
					$AccessGranted = false;
			}
			elseif (!static::IsUserAuthorized())
			{
				if (!$ClassName::$UnAuthUse)
				{
					ErrorHandle::ErrorHandle('Нарушение прав доступа к объекту класса '.$ClassName.'.',3);
					$AccessGranted = false;
				}
			}
			else
			{
				$AccessGranted = static::CheckClassPermissions($ClassName);
			}

			return $AccessGranted;
		}

		public static function ProcessMessage(&$Data)
		{
			$Result = false;
			
			if (!isset($Data['Object']))
			{
				ErrorHandle::ActionErrorHandle("Не указан класс объекта для обработки события.", 0);
			}
			elseif (!isset($Data['Action']))
			{
				ErrorHandle::ActionErrorHandle("Не задано событие для обработки объектом ".$Data['Object'].".", 0);
			}
			else
			{
				$ClassName = $Data['Object'];
				$Action = strtolower($Data['Action']);
				if (!class_exists($ClassName))
				{
					ErrorHandle::ErrorHandle('Отсутствует класс '.$ClassName.' для обработки события '.$Action.'.', 0);					
				}
				elseif (!isset($ClassName::$Actions[$Action]))
				{
					ErrorHandle::ErrorHandle('В классе '.$ClassName.' отсутствует модуль обработки события '.$Action,3);
				}
				else
				{
					$MethodName = $ClassName::$Actions[$Action];

					if (!method_exists($ClassName,$MethodName))
					{
						ErrorHandle::ActionErrorHandle('Метод '.$MethodName.' обработки события '.$Action.' не определен в объекте '.$ClassName.'.', 0);
					}
					elseif (!Controller::CheckActionAccess($Action,$ClassName))
					{
						ErrorHandle::ActionErrorHandle('Нарушение прав доступа к обработчику события '.$Action.' класса '.$ClassName.'.', 0);
					}
					else
					{
						$Object = $ClassName::GetObject($Data,null);
						if (!call_user_func(array($Object,$MethodName)))
						{
							ErrorHandle::ActionErrorHandle('Ошибка при вызове обработчика события '.$Action.' класса '.$ClassName.'.', 1);
						}
						else
						{
							$Result = true;
						}
					}
				}
			}
			unset($_GET);
			
			$_GET['Object'] = 'System';
			$_GET['Form'] = 'ErrorHandle';

			return $Result;
			// DONE 5 -o Molev  -c Output: Form base output Object - ErrorHandle Form - ActionResultForm
		}

		public static function CreateView(&$Data)
		{
			$Result = null;
			if (!isset($Data['Object']))
			{
				ErrorHandle::ErrorHandle("Не указан класс объекта для построения отображения.", 0);
			}
			elseif (!isset($Data['Form']))
			{
				ErrorHandle::ErrorHandle("Не задана форма построения отображения объекта ".$Data['Object'].".", 0);
			}
			else
			{
				$ClassName = $Data['Object'];
				$FormName = strtolower($Data['Form']);
				
				if (!class_exists($ClassName))
				{
					ErrorHandle::ErrorHandle('Отсутствует класс '.$ClassName.' для построения отображения '.$FormName.'.', 0);
				}
				elseif (!Controller::CheckFormAccess($FormName,$ClassName))
				{
					ErrorHandle::ErrorHandle('Нарушение прав доступа к форме '.$FormName.' класса '.$ClassName.'.', 0);
				}

				elseif (!isset($ClassName::$Forms[$FormName]))
				{
					ErrorHandle::ErrorHandle('В классе '.$ClassName.' отсутствует форма отображения '.$FormName,3);
				}
				else
				{
					$FileName = $ClassName::$Forms[$FormName];

					if (!file_exists($FileName))
					{
						ErrorHandle::ErrorHandle('Файл '.$FileName.' шаблона формы '.$FormName.' объекта '.$ClassName.' не найден.', 0);
					}
					elseif (!is_readable($FileName))
					{
						ErrorHandle::ErrorHandle('Отсутствуют права на чтение файла '.$FileName.' шаблона формы '.$FormName.' объекта '.$ClassName.'.', 0);
					}
					else
					{
						$View = new Form($Data,$ClassName,$FormName);
						$Result = $View->CreateView();
						unset($View);
					}
				}
			}
			return $Result;
		}

		public static function IsUserAuthorized()
		{
			$system = System::GetObject();
			return $system->IsUserAuthorized();
			return false;
		}

		public static function GetObject(&$ProcessData)
		{
			return static::GetObjectInstance($ProcessData,null,__CLASS__);
		}

		public static function Run()
		{
			$Controller = Controller::GetObject($_POST);
			
			if (count($_POST) > 0) 
			{
				$result = Controller::ProcessMessage($_POST);
				$result = ErrorHandle::SystemStatusOutput();
			}
			elseif ((!isset($_GET['Ajax'])) or ($_GET['Ajax'] == 0))
			{ 
				$Temp_GET = array('Object'=>'System','Form'=>('Default'. (Controller::IsUserAuthorized()?'Auth':'UnAuth')));
				$Template = Controller::CreateView($Temp_GET);
				
				if (isset($_GET['Object']) and isset($_GET['Form'])) $result = Controller::CreateView($_GET);
				else $result = null;
				
				if (!is_null($Template) and ($Template !=''))
				{
					$result = str_replace('<!--#work_field#-->',$result,$Template);  
				}
				elseif (is_null($result) or ($result ==''))
				{
					$result = ErrorHandle::SystemStatusOutput();
				}
			}
			else
			{
				$result = Controller::CreateView($_GET);
			}
			

			return $result;
		}

	}         
?>
