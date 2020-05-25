<?php
/**
 * File class
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   MIT, GPL v2 or later
 */
namespace Intermatics;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * General purpose methods for processing a single file or set of folders and files
 *
 * Thanks http://Twitter.com/CoreyHaines for your feedback and assistance! https://gist.github.com/4677586
 * 
 * 1. Copy or Move a file or folders/files:
 * 
 * $files = new Files();
 * $results = $files->copyOrMove('source/folder/optional-file', 
 *      'target/path', 'target-file-name-for-single-file.only', 'copy');
 * 
 * 2. Delete a single file or all files and folders in a path:
 * 
 * $files = new Files();
 * $results = $files->delete('source/folder/optional-file.name');
 * 
 * 3. Calculate the size of a single file or a set of files in a set of folders:
 * 
 * $files = new Files();
 * $results = $files->calculateSize('source/folder/optional-file.name');
 *
 */
class Files
{
    protected $path;
    protected $options;
    protected $filesystem;
    protected $directories;
    protected $files;

    /**
     * Get the file size of a given file or for the aggregate number of bytes in all directory files
     *
     * Recursive handling of files - uses file arrays created in Discovery
     *
     * @param   string  $path
     *
     * @return  int
     * @since   1.0
     */
    public function calculateSize($path)
    {
        $size = 0;

        $this->discovery($path);

        foreach ($this->files as $file) {
            $size = $size + filesize($file);
        }

        return $size;
    }

    /**
     * Recursive Copy or Delete - uses Directory and File arrays created in Discovery
     *
     * Copy uses Directory array first to create folders, then copies the files
     *
     * @param   string  $path
     * @param   string  $target
     * @param   string  $target_name
     * @param   string  $copyOrMove
     *
     * @return  void
     * @since   1.0
     */
    function copyOrMove($path, $target, $target_name = '', $copyOrMove = 'copy')
    {
        if (file_exists($path)) {
        } else {
            return;
        }

        if (file_exists($target)) {
        } else {
            return;
        }

        $new_path = $target . '/' . $target_name;

        $this->discovery($path);

        if (count($this->directories) > 0) {
            asort($this->directories);
            foreach ($this->directories as $directory) {

                $new_directory = $new_path . '/' . substr($directory, strlen($path), 99999);

                if (basename($new_directory) == '.' || basename($new_directory) == '..') {

                } elseif (file_exists($new_directory)) {

                } else {
                    mkdir($new_directory);
                }
            }
        }

        if (count($this->files) > 0) {
            foreach ($this->files as $file) {
                $new_file = $new_path . '/' . substr($file, strlen($path), 99999);
                \copy($file, $new_file);
            }
        }

        if ($copyOrMove == 'move') {

            if (count($this->files) > 0) {
                foreach ($this->files as $file) {
                    unlink($file);
                }
            }

            if (count($this->directories) > 0) {
                arsort($this->directories);
                foreach ($this->directories as $directory) {
                    if (basename($directory) == '.' || basename($directory) == '..') {
                    } else {
                        rmdir($directory);
                    }
                }
            }
        }

        return;
    }

    /**
     * Recursive Delete, uses discovery Directory and File arrays to first delete files
     *  and then remove the folders
     *
     * @param   $path
     *
     * @return  int
     * @since   1.0
     */
    function delete($path)
    {
        if (file_exists($path)) {
        } else {
            return;
        }

        $this->discovery($path);

        if (count($this->files) > 0) {
            foreach ($this->files as $file) {
                unlink($file);
            }
        }

        if (count($this->directories) > 0) {

            arsort($this->directories);

            foreach ($this->directories as $directory) {

                if (basename($directory) == '.' || basename($directory) == '..') {
                } else {
                    rmdir($directory);
                }
            }
        }

        return;
    }

    /**
     * Discovery retrieves folder and file names, useful for other file/folder operations
     *
     * @param   $path
     *
     * @return  void
     * @since   1.0
     */
    public function discovery($path)
    {
        $this->directories = array();
        $this->files       = array();

        if (is_file($path)) {
            $this->files[] = $path;
            return;
        }

        if (is_dir($path)) {
        } else {
            return;
        }

        $this->directories[] = $path;

        $objects = new RecursiveIteratorIterator (
            new RecursiveDirectoryIterator($path),
            RecursiveIteratorIterator::SELF_FIRST);

        foreach ($objects as $name => $object) {

            if (is_file($name)) {
                $this->files[] = $name;

            } elseif (is_dir($name)) {

                if (basename($name) == '.' || basename($name) == '..') {
                } else {
                    $this->directories[] = $name;
                }
            }
        }

        return;
    }
}

