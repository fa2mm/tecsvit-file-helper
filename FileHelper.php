<?php

namespace \tecsvit;

/**
 * This is just an example.
 */
class FileHelper
{
    public static $errorInstance;

    public function __construct()
    {
        self::$errorInstance = new PublicErrors();
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
     * Creates a new directory.
     *
     * This method is similar to the PHP `mkdir()` function except that
     * it uses `chmod()` to set the permission of the created directory
     * in order to avoid the impact of the `umask` setting.
     *
     * @param string $path path of the directory to be created.
     * @param integer $mode the permission to be set for the created directory.
     * @param boolean $recursive whether to create parent directories if they do not exist.
     * @return boolean whether the directory is created successfully
     */
    public static function createDirectory($path, $mode = 0775, $recursive = true)
    {
        if (is_dir($path)) {
            return true;
        }
        $parentDir = dirname($path);
        // recurse if parent dir does not exist and we are not at the root of the file system.
        if ($recursive && !is_dir($parentDir) && $parentDir !== $path) {
            static::createDirectory($parentDir, $mode, true);
        }
        try {
            if (!mkdir($path, $mode)) {
                return false;
            }
        } catch (\Exception $e) {
            if (!is_dir($path)) {// https://github.com/yiisoft/yii2/issues/9288
                self::$errorInstance->addError('Failed to create directory "'. $path. '": ' . $e->getMessage());

                return false;
            }
        }
        try {
            return chmod($path, $mode);
        } catch (\Exception $e) {
            self::$errorInstance->addError(
                'Failed to change permissions for directory "' . $path . '": ' . $e->getMessage()
            );

            return false;
        }
    }
}
