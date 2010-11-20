<?php
	function __autoload($ClassName) 
	{
		if (class_exists($ClassName)) return true;

		$result = true;


		switch ($ClassName)
		{
			// case ''                   : include_once(''); break;
			case 'Singleton'            : $ClassFileName = 'objects/framework/singleton.class.php'; break;
			case 'ErrorHandle'			: $ClassFileName = 'objects/framework/errorhandle.class.php'; break;
			case 'Controller'			: $ClassFileName = 'objects/framework/controller.class.php'; break;
			case 'BaseClass'            : $ClassFileName = 'objects/framework/baseclass.class.php'; break;
			case 'Form'            		: $ClassFileName = 'objects/framework/form.class.php'; break;
			case 'Entity'            	: $ClassFileName = 'objects/framework/entity.class.php'; break;
			case 'CollectionBasic'      : $ClassFileName = 'objects/framework/collection.basic.class.php'; break;
			case 'CollectionDB'         : $ClassFileName = 'objects/framework/collection.db.class.php'; break;

			case 'System'               : $ClassFileName = 'objects/system/system.class.php'; break;
			case 'DBMySQL'            	: $ClassFileName = 'objects/dbmysql.class.php'; break;

			case 'Role'            	    : $ClassFileName = 'objects/model/role/role.class.php'; break;
			case 'User'                 : $ClassFileName = 'objects/model/user/user.class.php'; break;
			
			
			case 'Object_template'      : $ClassFileName = 'objects/model/object_template/object_template.class.php'; break;
			case 'CollectionBasic'      : $ClassFileName = 'objects/abstract/collection.basic.class.php'; break;
			case 'CollectionDBTemplated': $ClassFileName = 'objects/abstract/collection.template.db.class.php'; break;
			case 'CollectionTemplated'  : $ClassFileName = 'objects/abstract/collection.template.class.php'; break;

			case 'Project'              : $ClassFileName = 'objects/model/project/project.class.php'; break;
			case 'ProjectList'          : $ClassFileName = 'objects/model/project/project.list.class.php'; break;

			case 'Task'                 : $ClassFileName = 'objects/task/task.class.php'; break;
			case 'TaskList'             : $ClassFileName = 'objects/task/task_list.class.php'; break;

			case 'Event'                : $ClassFileName = 'objects/event/event.class.php'; break;
			case 'EventList'            : $ClassFileName = 'objects/event/event_list.class.php'; break;

			case 'UserList'             : $ClassFileName = 'objects/user/user_list.class.php'; break;
			
			case 'Role'                 : $ClassFileName = 'objects/model/role/role.class.php'; break;
		
			case 'RolesUsers'           : $ClassFileName = 'objects/model/role/role_user_list.class.php'; break;
			case 'RoleList'             : $ClassFileName = 'objects/model/role/role.list.class.php'; break;
			case 'UsersAndRoles'        : $ClassFileName = 'objects/model/role/users.roles.class.php'; break;
			
			case 'Filter'        	    : $ClassFileName = 'objects/model/filter/filter.class.php'; break;
			case 'FilterList'           : $ClassFileName = 'objects/model/filter/filter.list.class.php'; break;
			
			case 'TpeClass'        		: $ClassFileName = 'objects/model/tpe_class/tpe_class.class.php'; break;
			case 'TpeClassList'         : $ClassFileName = 'objects/model/tpe_class/tpe_class.list.class.php'; break;
		
		
			default                     : $ClassFileName = 'objects/'.strtolower($ClassName).'.class.php';
		}
		

		if (!file_exists($ClassFileName))
		{
			ErrorHandle::ErrorHandle('Не найден файл програмного класса "'.$ClassName.'""',3);
			$result = false;
		} 

		elseif (!is_readable($ClassFileName))
		{
			$ErrorDescription[] = 'Нет доступа на чтение к файлу програмного класса "'.$ClassName.'". Файл найден."';
			$result = false;
		}
		else
		{ 
			try 
			{
				include_once($ClassFileName);
			}
			catch (Exception $e) 
			{
				$ErrorDescription[] = 'Ошибка подключения програмного класса "'.$ClassName.'""';
				$result = false;
			}
		}
		if ($result and !class_exists($ClassName)) 
		{
			$ErrorDescription[] = 'Ошибка инициализации програмного класса "'.$ClassName.'". Файл найден и загружен."';
			$result = false;
		} 

		return $result;
	}

	function IncludeClass($ClassName,$ErrorDescription)
	{
		global $ErrorDescription;

		if (class_exists($ClassName)) return true;

		$result = true;


		switch ($ClassName)
		{
			// case ''                   : include_once(''); break;
			case 'AbstractBasicClass'   : $ClassFileName = 'objects/abstract/abstract.basic.class.php'; break;
			case 'AbstractRoot'         : $ClassFileName = 'objects/abstract/abstract.root.class.php'; break;
			case 'AbstractTemplate'     : $ClassFileName = 'objects/abstract/abstract.template.class.php'; break;
			case 'CollectionBasic'      : $ClassFileName = 'objects/abstract/collection.basic.class.php'; break;
			case 'CollectionDBTemplated': $ClassFileName = 'objects/abstract/collection.template.db.class.php'; break;
			
			default                     : $ClassFileName = 'objects/'.strtolower($ClassName).'.class.php';
		}

		if (!file_exists($ClassFileName))
		{
			$ErrorDescription[] = 'Не найден файл програмного класса "'.$ClassName.'""';
			$result = false;
		} 

		elseif (!is_readable($ClassFileName))
		{
			$ErrorDescription[] = 'Нет доступа на чтение к файлу програмного класса "'.$ClassName.'". Файл найден."';
			$result = false;
		}
		else
		{ 
			try 
			{
				include_once($ClassFileName);
			}
			catch (Exception $e) 
			{
				$ErrorDescription[] = 'Ошибка подключения програмного класса "'.$ClassName.'""';
				$result = false;
			}
		}
		if ($result and !class_exists($ClassName)) 
		{
			$ErrorDescription[] = 'Ошибка инициализации програмного класса "'.$ClassName.'". Файл найден и загружен."';
			$result = false;
		} 

		return $result;
	}
	
	function GetBeginOfDay($Date)
	{
		$D = 24*3600;
		return ($Date - $Date % $D);
	}

	function GetBeginOfWorkDay($Date)
	{
		$D = 24*3600;
		return ($Date - $Date % $D + 9* 3600);
	}

	function GetEndOfWorkDay($Date)
	{
		$D = 24*3600;
		return ($Date - $Date % $D+ 18* 3600);        
	}
	
	function AddDay($Date , $Count = 1)
	{
		return $Date + $Count * 24 * 3600;
	}
	
	function DateTimeToStr($Date)
	{
		return date("d.m.Y H:i:s",$Date);
	}
	
	function DateTimeToMySQL($Date)
	{
		return gmdate("Y-m-d H:i:s",$Date);
	}

?>