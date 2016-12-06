<?php
namespace Brainexploded\FSTools;

class FSTraverser
{
    protected $rootDir;
    protected $callback;
    protected $excludeNodes;
    protected $excludeExtensions = [];
    protected $allowedExtensions = [];
    protected $maxDepth;

    public function __construct(
        $rootDir = null,
        callable $callback = null,
        $excludeNodes = [],
        $allowedExtensions = [],
        $excludeExtensions = [],
        $maxDepth = null)
    {
        $this->rootDir = $rootDir;
        $this->callback = $callback;
        $this->excludeNodes = $excludeNodes;
        $this->excludeExtensions = $excludeExtensions;
        $this->allowedExtensions = $allowedExtensions;
        $this->maxDepth = $maxDepth;
    }

    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
        return $this;
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    public function setExcludeNodes($excludeNodes)
    {
        $this->excludeNodes = $this->excludeNodes;
        return $this;
    }

    public function setExcludeExtensions($excludeExtensions)
    {
        $this->excludeExtensions = $excludeExtensions;
        return $this;
    }

    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
        return $this;
    }

    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth;
        return $this;
    }

    public function go($returnContent = false)
    {
        if (!$this->rootDir) {
            throw new FSTraverserException('Root directory is not specified');
        }
        if (!$this->callback) {
            throw new FSTraverserException('Callback function is not specified');
        }

        $this->traverse($this->rootDir, $this->callback, $returnContent);
    }

    protected function traverse($path, callable $callback, $returnContent = false, $depth = 0)
    {
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                $fullpath = $path.'/'.$entry;
                if (in_array($entry, ['.', '..'])) {
                    continue;
                }

                if (is_dir($fullpath) && $this->validateDir($fullpath, $depth)) {
                    $this->traverse($fullpath, $callback, $returnContent, $depth+1);
                } elseif ($this->validateFile($fullpath)) {
                    if ($returnContent) {
                        $contentHandle = fopen($fullpath, 'rb');
                        if ($contentHandle === false) {
                            echo "[WARNING] cannot open file $fullpath", PHP_EOL;
                            continue;
                        }
                        $size = filesize($fullpath);
                        if ($size > 0) {
                            $content = fread($contentHandle, filesize($fullpath));

                            $callback($path, $entry, $content);

                            unset($content);
                        } else {
                            echo "[NOTICE] file $fullpath is empty!", PHP_EOL;
                        }
                        fclose($contentHandle);
                    } else {
                        $callback($path, $entry);
                    }
                }
            }
        }
    }

    protected function getExtension($filename)
    {
        $dotPos = strrpos($filename, '.');
        if ($dotPos === false) {
            return false;
        }
        return substr($filename, strrpos($filename, '.')+1);
    }

    protected function validateDir($dir, $depth)
    {
        if (in_array($dir, $this->excludeNodes)) {
            return false;
        }
        if ($this->maxDepth && ($depth >= $this->maxDepth)) {
            return false;
        }
        return true;
    }

    protected function validateFile($file)
    {
        if (!file_exists($file)) {
            return false;
        }
        if (in_array($file, $this->excludeNodes)) {
            return false;
        }
        $ext = $this->getExtension($file);

        if ($this->allowedExtensions) {
            if (!$ext || !in_array($ext, $this->allowedExtensions)) {
                return false;
            }
        }
        if ($this->excludeExtensions) {
            if (in_array($ext, $this->excludeExtensions)) {
                return false;
            }
        }
        return true;
    }
}

class FSTraverserException extends \Exception {}
