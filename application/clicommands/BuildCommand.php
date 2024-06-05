<?php

/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */
/* icingaweb2-module-scaffoldbuilder 2023 | GPLv2+ */
namespace Icinga\Module\Scaffoldbuilder\Clicommands;

use Icinga\Application\Logger;
use Icinga\Application\Modules\Manager;
use Icinga\Application\Modules\Module;
use Icinga\Cli\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class BuildCommand extends Command
{
    /**
     *
     *
     * USAGE:
     *
     *   icingacli scaffoldbuilder build [options]
     *
     * OPTIONS:
     *
     *   --name         The name of the new icingaweb-module
     *   --iniconfigs   The name of the IniRepository Configs comma seperated (job, test) or (job:table, test:grid)
     *   --dbconfigs    The name of the Database model comma seperated (jobs, tests) or (job:job_table, tests:test_table)
     *   --tableprefix  The prefix for all the tables defaults to modulename_
     *
     *   --themename    The theme name inside the module, leave blank if you don't need that
     *   --filemanager  Generate a filemanger for uploading/downloading files (YES/NO)
     *   --modulepath  The modulepath to generate the modules in, defaults to /usr/share/icingaweb2/modules
     *   --sqlite (YES/NO) Render the sqlite database helpers
     *   --dev (YES/NO)
     *
     *
     */
    public function defaultAction()
    {
        $prepConfigs=[];
        $prepSqlConfigs=[];

        $name = $this->params->getRequired('name');
        $tableprefix = $this->params->get('tableprefix',$name."_");

        $configsParam = $this->params->get('iniconfigs');
        if(isset($configsParam) && $configsParam != "" && $configsParam != "1"){
            $configs = explode(",",str_replace(" ","",$configsParam));
            foreach ($configs as $config){

                $tmp =explode(":",$config);
                if(isset($tmp[1]) && $tmp[1] === "table"){
                    $prepConfigs[$tmp[0]]= "table";
                }else{
                    $prepConfigs[$tmp[0]]= "grid";
                }
            }
        }

        $configsParam = $this->params->get('dbconfigs');
        if(isset($configsParam) && $configsParam != "" && $configsParam != "1"){
            $configs = explode(",",str_replace(" ","",$configsParam));
            foreach ($configs as $config){

                $tmp =explode(":",$config);
                if(isset($tmp[1])){
                    $prepSqlConfigs[$tmp[0]]= $tmp[1];
                }else{
                    $prepSqlConfigs[$tmp[0]]= $tmp[0];
                }
            }
        }

        $moduleName = strtolower($name);
        $libraryName = ucfirst($name);
        $themename = $this->params->get('theme');
        $icingaModulePath = $this->params->get('modulepath',"/usr/share/icingaweb2/modules");
        $scaffoldBuilderPath = Module::get('scaffoldbuilder')->getBaseDir();
        $modulePath="{$icingaModulePath}/{$moduleName}_tmp";

        $withFilemanager = strtolower($this->params->get('filemanager','NO')) === "yes";
        $withSqliteHelpers = strtolower($this->params->get('sqlite','NO')) === "yes";
        $this->removeDir($modulePath);
        $deploy_modulePath="{$icingaModulePath}/{$moduleName}";
        $templatePath = "{$scaffoldBuilderPath}/templates";
        echo "Module Name will be $moduleName\n";
        $mapping = [];

        if ($themename != ""){
            echo "Theme will be $themename\n";
        }else{
            echo "No theme will be generated\n";
        }
        $dirs=[];
        array_push($dirs,"application/clicommands");
        array_push($dirs,"application/controllers");
        array_push($dirs,"application/forms");
        array_push($dirs,"application/locale");
        array_push($dirs,"application/views/helpers");
        array_push($dirs,"application/views/scripts/index");
        array_push($dirs,"doc/img");
        array_push($dirs,"library/{$libraryName}");
        array_push($dirs,"public/css");
        if ($themename != ""){
            $mapping["{$templatePath}/theme.less"]="{$modulePath}/public/css/themes/{$themename}.less";
            array_push($dirs,"public/css/themes");
        }
        array_push($dirs,"public/img");
        array_push($dirs,"public/js");
        array_push($dirs,"test/php");

        foreach($dirs as $dir){
            mkdir("{$modulePath}/{$dir}",0755,true);
        }

        foreach (scandir($templatePath) as $template){
            if($template == "Theme" || $template == "Multiconfig" || $template == "Model" || $template == "Database" || $template == "SqliteDatabase" || $template == ".." || $template == "." || !is_dir($templatePath.DIRECTORY_SEPARATOR.$template)){
                continue;
            }
            if($template == "File" && !$withFilemanager ){
                continue;
            }
            $this->extracted($templatePath, $template, $modulePath, $moduleName, "", "", "");
        }
        if($themename != "" && $themename != null){
            $this->extracted($templatePath, "Theme", $modulePath, $moduleName, "", "", $themename);

        }


        foreach ($prepConfigs as $configname=>$kind){
            if($configname != ""){
                echo "Config Name will be $configname, view will be $kind\n";
                $this->extracted($templatePath, "Multiconfig", $modulePath, $moduleName, $configname, $kind, "");
            }
        }
        if(count($prepSqlConfigs)>0){
            $this->extracted($templatePath, "Database", $modulePath, $moduleName, "", "", "", "","",$tableprefix);
        }
        if($withSqliteHelpers){
            $this->extracted($templatePath, "SqliteDatabase", $modulePath, $moduleName, "", "", "");
        }
        foreach ($prepSqlConfigs as $modelname=>$tablename){
            if($modelname != ""){
                echo "Model Name will be $modelname, Table will be $tablename\n";
                $this->extracted($templatePath, "Model", $modulePath, $moduleName,"", "", "", $modelname, $tablename, $tableprefix);
            }
        }

        if (file_exists($deploy_modulePath)) {
            echo "Module {$name} already exists\n";
            $choice = readline("Do you want to merge or replace (m/r)");
            if($choice === "r"){
                $this->removeDir($deploy_modulePath);
                rename($modulePath, $deploy_modulePath);
            }elseif ($choice === "m"){
                $this->mergeFolders($modulePath,$deploy_modulePath);
                $this->removeDir($modulePath);
            }
        }else{
            rename($modulePath, $deploy_modulePath);
        }

        $isDev = strtolower($this->params->get('dev','NO')) === "yes";
        if($isDev){
            $this->chmodDir($deploy_modulePath);
        }


    }
    private function chmodDir(string $dir): void {
        if(file_exists($dir) && is_dir($dir)){
            $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it,
                RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                chmod($file, 0777);
            }
            chmod($dir, 0777);
        }

    }

    private function mergeFolders($source, $destination) {
        // Loop through all files and sub-directories in the source folder

        $dir = opendir($source);
        while ($file = readdir($dir)) {
            if ($file != '.' && $file != '..') {
                $sourcePath = $source . '/' . $file;
                $destinationPath = $destination . '/' . $file;

                // If it's a directory, recursively merge it
                if (is_dir($sourcePath)) {
                    // If destination directory doesn't exist, create it
                    if (!is_dir($destinationPath)) {
                        mkdir($destinationPath);
                    }
                    $this->mergeFolders($sourcePath, $destinationPath);
                } else {
                    if(!file_exists($destinationPath)){
                        copy($sourcePath, $destinationPath);
                        continue;
                    }

                    if( file_get_contents($destinationPath) !== file_get_contents($sourcePath)){
                        // If it's a file, ask for confirmation before moving it to the destination folder
                        echo "File would be changed $destinationPath\n";
                        $choice = readline("Do you want to keep, replace or backup+replace (k/r/b)");

                        if ($choice == 'r') {
                            // Move the file to the destination folder
                            unlink($destinationPath);
                            copy($sourcePath, $destinationPath);
                            echo "File '$file' replaced successfully.\n";
                        } elseif($choice == 'b') {
                            $append = time();

                            $oldFile = $file."_".$append;
                            $destinationPath_old = $destinationPath."_".$append;
                            rename($destinationPath,$destinationPath_old);
                            copy($sourcePath, $destinationPath);
                            echo "File '$file' renamed to .$oldFile\n";
                        }else{
                            //do nothing
                            echo "File '$file' kept as it is.\n";
                        }
                    }

                }
            }
        }
        closedir($dir);
    }

    private function removeDir(string $dir): void {
        if(file_exists($dir) && is_dir($dir)){
            $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it,
                RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if ($file->isDir()){
                    rmdir($file->getPathname());
                } else {
                    unlink($file->getPathname());
                }
            }
            rmdir($dir);
        }

    }

    /**
     * @param string $templatePath
     * @param $template
     * @param string $modulePath
     * @param string $moduleName
     * @param string $configname
     * @param string $configview
     */
    public function extracted(string $templatePath, $template, string $modulePath, string $moduleName, string $configname, string $configview, string $themename, string $modelname ="", string $tablename="", string $tableprefix=""): void
    {
        $tablename = $tableprefix.$tablename;
        $currentTemplatePath = $templatePath . DIRECTORY_SEPARATOR . $template;
        $rdi = new RecursiveDirectoryIterator($currentTemplatePath, RecursiveDirectoryIterator::KEY_AS_PATHNAME);
        foreach (new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST) as $file => $info) {
            if (is_file($file)) {
                $merge=false;
                $destination = str_replace($currentTemplatePath, $modulePath, $file);
                $fileContent = file_get_contents($file);


                if (strpos($file, "configuration.php") !== false || strpos($file, "module.less") !== false  || strpos($file, "schema.sql") !== false ) {
                    $merge=true;
                }

                $fileContent = str_replace("__tableprefix__", $tableprefix, $fileContent);

                $fileContent = str_replace("__modulename__", $moduleName, $fileContent);
                $fileContent = str_replace("__Modulename__", ucfirst($moduleName), $fileContent);
                $fileContent = str_replace("__MODULENAME__", strtoupper($moduleName), $fileContent);

                $destination = str_replace("__modulename__", $moduleName, $destination);
                $destination = str_replace("__Modulename__", ucfirst($moduleName), $destination);
                $destination = str_replace("__MODULENAME__", strtoupper($moduleName), $destination);

                $fileContent = str_replace("__configname__", $configname, $fileContent);
                $fileContent = str_replace("__Configname__", ucfirst($configname), $fileContent);
                $fileContent = str_replace("__ConfignamePL__", ucfirst($configname."s"), $fileContent);
                $fileContent = str_replace("__CONFIGNAME__", strtoupper($configname), $fileContent);

                $fileContent = str_replace("__configview__", $configview, $fileContent);

                $fileContent = str_replace("__modelname__", $modelname, $fileContent);
                $fileContent = str_replace("__Modelname__", ucfirst($modelname), $fileContent);
                $fileContent = str_replace("__modelnamePL__", $modelname."s", $fileContent);
                $fileContent = str_replace("__ModelnamePL__", ucfirst($modelname."s"), $fileContent);
                $fileContent = str_replace("__MODELNAME__", strtoupper($modelname), $fileContent);

                $fileContent = str_replace("__tablename__", $tablename, $fileContent);


                $fileContent = str_replace("__themename__", $themename, $fileContent);
                $destination = str_replace("__themename__", $themename, $destination);

                $destination = str_replace("__configname__", $configname, $destination);
                $destination = str_replace("__Configname__", ucfirst($configname), $destination);
                $destination = str_replace("__CONFIGNAME__", strtoupper($configname), $destination);

                $destination = str_replace("__modelname__", $modelname, $destination);
                $destination = str_replace("__Modelname__", ucfirst($modelname), $destination);
                $destination = str_replace("__modelnamePL__", $modelname."s", $destination);
                $destination = str_replace("__ModelnamePL__", ucfirst($modelname."s"), $destination);
                $destination = str_replace("__MODELNAME__", strtoupper($modelname), $destination);

                $destinationDir = dirname($destination);
                if (!file_exists($destinationDir)) {
                    mkdir($destinationDir, 0755, true);
                }

                if (file_exists($destination) && $merge === true) {
                    $presentFileContent = file_get_contents($destination);
                    $fileContent = $presentFileContent . "\n" . $fileContent;
                }

                file_put_contents($destination, $fileContent);


            }

        }
    }

}
