<?php

namespace tecsvit;

/**
 * Class FileHelper
 *
 * Date: 30.11.2017
 *
 * @static PublicErrors $errorInstance
 *
 * @use \tecsvit\PublicErrors
 */
class FileHelper
{
    public static $errorInstance;

    /**
     * @return void
     */
    public static function initErrors()
    {
        if (null === self::$errorInstance) {
            self::$errorInstance = new PublicErrors();
        }
    }

    /**
     * @return PublicErrors
     */
    public static function errorInstance()
    {
        self::initErrors();
        return self::$errorInstance;
    }

    /**
     * @return FileHelper
     */
    public static function init()
    {
        return new self();
    }

    /**
     * @param string $filePath
     * @param string $content
     * @return void
     */
    public function createFile($filePath, $content = null)
    {
        if (!file_exists($filePath)) {
            if ($this->createDirectory(dirname($filePath), '0777')) {
                file_put_contents($filePath, $content);
            }
        }
    }

    /**
     * @param string    $path
     * @param integer   $mode
     * @param boolean   $recursive
     * @return boolean  whether
     */
    public function createDirectory($path, $mode = 0775, $recursive = true)
    {
        if (is_dir($path)) {
            return true;
        }

        $parentDir = dirname($path);

        if ($recursive && !is_dir($parentDir) && $parentDir !== $path) {
            $this->createDirectory($parentDir, $mode, true);
        }

        try {
            if (!mkdir($path, $mode)) {
                return false;
            }
        } catch (\Exception $e) {
            if (!is_dir($path)) {
                self::errorInstance()->addError('Failed to create directory "'. $path. '": ' . $e->getMessage());

                return false;
            }
        }

        try {
            return chmod($path, $mode);
        } catch (\Exception $e) {
            self::errorInstance()->addError(
                'Failed to change permissions for directory "' . $path . '": ' . $e->getMessage()
            );

            return false;
        }
    }
}
