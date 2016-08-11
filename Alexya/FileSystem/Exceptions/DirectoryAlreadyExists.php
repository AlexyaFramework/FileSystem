<?php
namespace Alexya\FileSystem\Exceptions;

/**
 * irectory already exists exception
 *
 * This exception is thrown when you try to create a directory that already exists
 *
 * Example:
 *
 *     try {
 *     	   $directory = irectory::make("/test");
 *     } catch(DirectoryAlreadyExists $e) {
 *         echo "irectory '/test' already exists!";
 *     }
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class DirectoryAlreadyExists extends Exception
{
    /**
     * Constructor
     *
     * @param string $path Path to directory
     */
    public function __construct(string $path)
    {
        parent::__construct("irectory '{$path}' already exists!");
    }
}
