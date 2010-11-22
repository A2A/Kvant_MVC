<?php
    class Form
    {
        protected $ViewData;                                                                         // текущий GET запрос от окружения

        protected $Form;
        protected $ClassName;
        protected $Object;
        protected $ObjectID;

        protected $Body;                                                                    // Результат шаблонирования

        protected $Content = array('header'=>'','element'=>'','footer'=>'');          // Содержимое шаблона
        protected $RectCollection = array('header'=>array(),'element'=>array(),'footer'=>array());   // Коллекция прямоугольных областей в шаблоне

        public $Forms = array();

        /*  
        *   Загрузка содержимого файла шаблона для дальнейшей обработки
        *   
        *   Параметры
        *       $FileName   - <string>  - имя файла шаблона, который будет разбираться  
        *   Возврат 
        *                   - <bool>    - true - содержимое загружено, false - при ошибке загрузки.
        *   Изменяемые поля:
        *       FileName    - имя файла (если файл сеществует и доступен на чение) или пустая строка;
        *       Content     - содержимое файла (если файл сеществует и доступен на чение) или пустая строка;
        * 
        */
        protected function Load($FileName)
        {
            $Result = true;  
            if (!file_exists($FileName))
            {
                ErrorHandle::ErrorHandle("Oтсутсвует файл шаблона ".$FileName.".",2);
                $this->Content = array('header'=>'','element'=>'','footer'=>'');
                $Result = false;
            } 
            elseif (!(is_readable($FileName)))
            {
                ErrorHandle::ErrorHandle("Нет доступа на чтение к файлу шаблона ".$FileName.".",2);
                $this->Content = array('header'=>'','element'=>'','footer'=>'');
                $Result = false;
            }
            elseif (($Content = file_get_contents($FileName)) === false)
            {
                ErrorHandle::ErrorHandle("Ошибка загрузки файла шаблона ".$FileName.".",2);
                $this->Content = array('header'=>'','element'=>array(),'footer'=>'');
                $Result = false;
            }
            else
            {
                // TODO 5 -o Molev  -c Output: Str -> split
                $this->Content['header'] = strstr($Content,'<!-- Element Begin -->',true);  
                if ($this->Content['header'] === false)
                {
                    $this->Content['header'] = $Content;
                    $this->Content['footer'] = '';
                    $this->Content['element'] = array();
                }
                else
                {
                    $this->Content['footer'] = strstr($Content,'<!-- Element End -->',false); 

                    $ElementContent = strstr(strstr($Content,'<!-- Element End -->',true),'<!-- Element Begin -->',false);
                    $LoopBound = $this->Object->count();
                    for ($i = 0;$i<$LoopBound;$i++)
                    {
                        $this->Content['element'][$i] = $ElementContent;
                    }
                }
            }
            return $Result;
        }

        protected function PrepareRect($SourceBlock,$Object)
        {
            $_RegExpression = '|\{\?(.+)\}|U';
            $_BlockIndex    = 0;
            $_RectIndex     = 1;

            $BlocksList         = array();
            $FieldThisObject    = array();
            $WhiteSpaces        = array(" ","\t","\n","\r","\0","\x0B");

            $BlockRecords=array();

            if (preg_match_all($_RegExpression, $SourceBlock, $BlocksList)) 
            {  
                foreach($BlocksList[$_BlockIndex] as $key=>$value)
                {
                    $BlockStr = str_replace($WhiteSpaces,"",$BlocksList[$_RectIndex][$key]);
                    parse_str($BlockStr,$ParamArr);

                    foreach ($ParamArr as $ParamKey => $ParamValue)
                    {
                        $ParamArr[$ParamKey] = $this->EvalExpr($ParamValue,$Object);
                    }

                    $Record = array();
                    if ($ParamArr['Object'] != 'this')
                    {
                        $Record['ClassName']       =$ParamArr['Object'];    // название класса объектов
                        $Record['Params']          =$ParamArr;              // параметры метода
                    }
                    else
                    {
                        $Record['Var']             =$ParamArr["Var"];   // параметры метода
                        $Record['Params']          = NULL;              // параметры метода
                    }  
                    $Record['Pattern']         	=$BlocksList[$_BlockIndex][$key];   // параметры метода
                    $BlockRecords[]             = $Record;
                }
            }
            return $BlockRecords;
        }

        //   Формирование коллекции прямоугольных областей и объектов, связанных с ними
        /*   
        *   Параметры
        *       <void>
        *   Возврат 
        *                   - <bool>    - true - коллекция проинциализирована, false - при возникновении ошибок.
        *   Изменяемые поля:
        *       RectCollection      - коллекция прямоугольных областей шаблона
        */
        protected function InitRects()
        {
            $this->RectCollection['header'] =  $this->PrepareRect($this->Content['header'],$this->Object);
            $this->RectCollection['footer'] =  $this->PrepareRect($this->Content['footer'],$this->Object);

            if (count($this->Content['element']) > 0)
            {
                $LoopBound = $this->Object->count();
                for ($i = 0;$i<$LoopBound;$i++)
                {
                    $this->RectCollection['element'][$i] = $this->PrepareRect($this->Content['element'][$i],$this->Object->get($i));
                }
            }

            return true;
        }

        protected function EvalExpr($Expr,$Object)
        {
            if (is_array($Expr))
            {
                foreach ($Expr as $key=>$Value)
                {
                    $Expr[$key] = $this->EvalExpr($Value,$Object);
                }
                return $Expr;

            }
            else
            { 
                if (($OpenBracketPos = stripos($Expr,"(",0)) === false)
                {
                    return $Expr;
                }
                elseif (($CloseBracketPos = stripos($Expr,")",$OpenBracketPos+1)) === false)
                {
                    return substr($Expr,0,$OpenBracketPos-1);
                }
                else
                {
                    $FuncName   = strtoupper(substr($Expr,0,$OpenBracketPos));
                    $ArgName    = substr($Expr,$OpenBracketPos+1,$CloseBracketPos-$OpenBracketPos-1);
                    switch ($FuncName)
                    {
                        case "POST":    return $this->ProcessData[$ArgName]; 
                        case "GET":     return $this->ViewData[$ArgName];
                        case "COOKIE":  return $_COOKIE[$ArgName];
                        case "SESSION": return $_SESSION[$ArgName];
                        case "INDEX":   return $this->Index;
                        case "COUNT":   return $this->count();
                        case "THIS":    
                        {
                            return $Object->$ArgName; 
                        }
                        default :       return $ArgName;
                    }
                }
            }
        }

        protected function GetRectResult(&$Rect)	
        {
            if (isset($Rect["ClassName"]) and (!is_null($Rect["ClassName"])) and ($Rect["ClassName"]!= ""))
            {
                $ClassName = &$Rect["ClassName"];
                if (Controller::CheckClassAccess($ClassName))
                {
                    $SubObject = $ClassName::GetObject($Rect['Params'],null);
                    if (array_key_exists('Params',$Rect) and array_key_exists('Var',$Rect['Params'])) 
                    {
                        if (property_exists($SubObject,$Rect['Params']['Var'])) 
                        {
                            $result = $SubObject->$Rect['Params']['Var'];
                        }
                        else 
                        {
                            $result = '';
                        }

                    }
                    else
                    {
                        $result = Controller::CreateView($Rect['Params']);

                    }
                }
                else
                {
                    $result = '';
                }

            }
            else
            {
                $result = $Rect["Var"];
            }
            return $result;
        }
        //   Заполнение коллекции прямоугольных областей конкретными результатми отображения объектов
        /*   
        *   Параметры
        *       <void>
        * 
        *   Возврат 
        *       <void>
        * 
        *   Изменяемые поля:
        *       RectCollection      - коллекция прямоугольных областей шаблона
        */
        protected function FillRectResults()
        {

            foreach($this->RectCollection['header'] as $key => $Rect)
            {
                $this->RectCollection['header'][$key]["Result"] = $this->GetRectResult($Rect);
            }

            foreach($this->RectCollection['footer'] as $key => $Rect)
            {
                $this->RectCollection['footer'][$key]["Result"] = $this->GetRectResult($Rect);
            }

            $LoopBound =count($this->RectCollection['element']);
            for ($i = 0;$i<$LoopBound;$i++)
            {
                foreach($this->RectCollection['element'][$i] as $key => $Rect)
                {
                    $this->RectCollection['element'][$i][$key]["Result"] = $this->GetRectResult($Rect);
                }
            }
        } 

        //   Заполнение тела шаблона результатми отображения объектов
        /*   
        *   Параметры
        *       <void>
        * 
        *   Возврат 
        *       <void>
        * 
        *   Изменяемые поля:
        *       Body
        */        
        protected function InsertRects()
        {
            $this->Body ='';

            $Cont = $this->Content['header'];

            foreach($this->RectCollection['header'] as $Rect)
            {
                $Cont = str_replace($Rect["Pattern"], $Rect["Result"], $Cont);
            }
            $this->Body .= $Cont;



            $LoopBound =count($this->Content['element']);
            for ($i = 0;$i<$LoopBound;$i++)
            {
                if (isset($this->Content['element'][$i]))
                {
                    $Cont = $this->Content['element'][$i];
                }
                else
                {   $Cont = '';
                    $qqq='test';
                }

                foreach($this->RectCollection['element'][$i] as $Rect)
                {
                    $Cont = str_replace($Rect["Pattern"], $Rect["Result"], $Cont);
                }
                $this->Body .= $Cont;
                // TODO 5 -o Molev  -c Output: Проверить логику работы . М. б. выводится 1 элемент.
            }



            $Cont = $this->Content['footer'];

            foreach($this->RectCollection['footer'] as $Rect)
            {
                $Cont = str_replace($Rect["Pattern"], $Rect["Result"], $Cont);
            }
            $this->Body .= $Cont;
        }

        /* 
        *   Создает отображение шаблона
        *   Параметры
        *       FileName - <string>     - Имя файла шаблона
        *         
        * 
        *   Возврат 
        *       <string>    - результат заполнения шаблона
        *       
        *   Изменяемые поля:
        *       Body                - Результат заполнения шаблона
        *       RectCollection      - коллекция прямоугольных областей шаблона
        */     
        public function GetView($FileName)
        {
            if ($this->Load($FileName)) 
            {
                $this->InitRects();
                $this->FillRectResults();
                $this->InsertRects();
            }
            else
            {
                $this->Body = '';
            }
            return $this->Body;
        }

        public function CreateView()
        {             
            $ClassName = $this->ClassName;
            if ((!is_null($this->Form)) and isset($ClassName::$Forms[$this->Form]))
            {
                $FileName = $ClassName::$Forms[$this->Form];
                return $this->GetView($FileName);
            }
            else
            {
                return '';
            }
        }

        public function __construct(&$ViewData,$ClassName,$RequestedForm)  
        {   
            $null = null;
            $this->ViewData = $ViewData;
            $this->ClassName = $ClassName;
            $this->Form = $RequestedForm;


            if (isset($ViewData['ID']) and is_numeric($ViewData['ID'])) $this->ObjectID = $ViewData['ID'];

            if (class_exists($ClassName))
            {
                if (is_subclass_of($ClassName, 'CollectionDB'))
                {
                    if (isset($this->ProcessData['Filter']) and is_array($this->ProcessData['Filter']))
                    {
                        $PD['Filter'] = $this->ProcessData['Filter'];
                        $this->Object = $ClassName::GetObject($PD,$this->ObjectID);
                    }
                    else
                    {
                        $this->Object = $ClassName::GetObject($PD,$this->ObjectID);
                    }
                }
                else
                {
                    $this->Object = $ClassName::GetObject($null,$this->ObjectID);
                }
            }
        }    

    }
?>
