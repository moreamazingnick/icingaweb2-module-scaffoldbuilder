<?php

namespace Icinga\Module\__Modulename__;

use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SqliteFileHelper
{
    protected $path = '';

    public function __construct($path)
    {
        $this->path=$path;
    }

    public function fetchFileList(){
        $result = [];
        if (!is_dir($this->path)) {
            echo "$this->path is not a directory";
            return;
        }

        // Create a RecursiveDirectoryIterator
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $result[] = $file->getPathname();
        }

        return $result;
    }

    public function getFile($filePath){

        if (strpos(realpath($filePath), $this->path) !== false && file_exists($filePath)) {
            return ['realPath'=>$filePath, 'size'=>filesize($filePath), 'name'=>$filePath];
        }
        return false;
    }

}
