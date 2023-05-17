<?php

/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */
/* icingaweb2-module-scaffoldbuilder 2023 | GPLv2+ */
namespace Icinga\Module\Scaffoldbuilder\Clicommands;

use Icinga\Application\Logger;
use Icinga\Application\Modules\Manager;
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
     *   icingacli scaffoldbuilder generate [options]
     *
     * OPTIONS:
     *
     *   --name         The name of the new icingaweb-module
     *   --configs      The name of the IniRepository Configs comma seperated (jobs, tests)
     *
     *   --themename    The theme name inside the module, leave blank if you don't need that
     *
     *
     */
    public function defaultAction()
    {

        $name = $this->params->getRequired('name');
        $configs = $this->params->getRequired('configs');
        $configs = explode(",",str_replace(" ","",$configs));

        $prepConfigs=[];

        foreach ($configs as $config){

            $tmp =explode(":",$config);
            if(isset($tmp[1]) && $tmp[1] === "table"){
                $prepConfigs[$tmp[0]]= "table";
            }else{
                $prepConfigs[$tmp[0]]= "grid";
            }
        }
        $moduleName = strtolower($name);

        $libraryName = ucfirst($name);
        $themename = $this->params->get('theme');
        $icingaModulePath = "/usr/share/icingaweb2/modules";
        $modulePath="{$icingaModulePath}/{$moduleName}";
        $templatePath = "{$icingaModulePath}/{$this->moduleName}/templates";
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
        if (file_exists($modulePath)) {
            $this->fail(
                "Module {$name} already exists, exit now"
            );
        }
        foreach($dirs as $dir){
            mkdir("{$modulePath}/{$dir}",0755,true);
        }

        foreach (scandir($templatePath) as $template){
            if($template == "Theme" || $template == "Multiconfig" || $template == ".." || $template == "." || !is_dir($templatePath.DIRECTORY_SEPARATOR.$template)){
                continue;
            }
            $this->extracted($templatePath, $template, $modulePath, $moduleName, "", "", "");
        }
        if($themename != "" && $themename != null){
            $this->extracted($templatePath, "Theme", $modulePath, $moduleName, "", "", $themename);

        }


        foreach ($prepConfigs as $configname=>$kind){
            $this->extracted($templatePath, "Multiconfig", $modulePath, $moduleName, $configname, $kind, "");
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
    public function extracted(string $templatePath, $template, string $modulePath, string $moduleName, string $configname, string $configview, string $themename): void
    {
        $currentTemplatePath = $templatePath . DIRECTORY_SEPARATOR . $template;
        $rdi = new RecursiveDirectoryIterator($currentTemplatePath, RecursiveDirectoryIterator::KEY_AS_PATHNAME);
        foreach (new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST) as $file => $info) {
            if (is_file($file)) {
                $fileContent = file_get_contents($file);

                if (strpos($file, "configuration.php") !== false && strpos($file, "0BasicFiles") === false) {
                    if (file_exists("{$modulePath}/configuration.php")) {
                        $presentFileContent = file_get_contents("{$modulePath}/configuration.php");
                        $fileContent = $presentFileContent . "\n" . $fileContent;
                    }
                }

                if (strpos($file, "module.less") !== false) {
                    if (file_exists("{$modulePath}/public/css/module.less")) {
                        $presentFileContent = file_get_contents("{$modulePath}/public/css/module.less");
                        $fileContent = $presentFileContent . "\n" . $fileContent;
                    }
                }

                $destination = str_replace($currentTemplatePath, $modulePath, $file);

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

                $fileContent = str_replace("__themename__", $themename, $fileContent);
                $destination = str_replace("__themename__", $themename, $destination);

                $destination = str_replace("__configname__", $configname, $destination);
                $destination = str_replace("__Configname__", ucfirst($configname), $destination);
                $destination = str_replace("__CONFIGNAME__", strtoupper($configname), $destination);

                $destinationDir = dirname($destination);
                if (!file_exists($destinationDir)) {
                    mkdir($destinationDir, 0755, true);
                }

                file_put_contents($destination, $fileContent);


            }

        }
    }

}
