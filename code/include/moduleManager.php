<?php
/**
 * Manages the modules of the administrator frontend.
 *
 * The available modules are extracted from modules.php and the appropriate path is calculated.
 * The ModuleManager also checks the users rights before including a module.
 */    

    class ModuleManager {

        function __construct($modules) {
            $this->modules = $modules;
        }
        
        function checkForSingleModule() {
            $count = 0;
            foreach ($this->modules as $module) {
                if($_SESSION['modules'][$module] == True) {
                    $count += 1;                       //Kann ab 2 gefundenen modulen abbrechen!
                    $last_fount = $module;
                }
            }
            if($count == 1) {
                $this->execute($last_fount);
                return true;
            }
            else {
                return false;
            }
        }
        
        function executeWeb($mod_name) {
            $mod_path ="modules/mod_".$mod_name."/".$mod_name.".php";
            foreach ($this->modules as $module) {
                if ($module == $mod_name AND file_exists($mod_path)) {
                    require $mod_path;
                    return;
                }
                //the module was not found
                elseif ($module == end($this->modules)) {
                    //the loop finished so the module was not found
                    echo MODULE_NOT_FOUND;
                }
            }
        }
    
		  
        function execute($mod_name) {
            $mod_path ="modules/mod_".$mod_name."/".$mod_name.".php";
            foreach ($this->modules as $module) {
                if ($module == $mod_name AND file_exists($mod_path)) {
                    //check for correct rights
                    if($_SESSION['modules'][$mod_name]) {
                        require $mod_path;
                        return;
                    }
                    else {
                        echo MODULE_FORBIDDEN;
                    }
                }
                //the module was not found
                elseif ($module == end($this->modules)) {
                    //the loop finished so the module was not found
                    echo MODULE_NOT_FOUND;
                }
            }
        }
    }  
    
    
?>