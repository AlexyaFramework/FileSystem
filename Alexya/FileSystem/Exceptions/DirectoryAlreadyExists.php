<?php
namespace Alexya\FileSystem\Exceptions;

/**
 * Directory already exists exception.
 *
 * This exception is thrown when you try to create a directory that already exists.
 *
 * Example:
 *
 * ```php
 * try {
 *     $directory = Directory::make("/test");
 * } catch(DirectoryAlreadyExists $e) {
 *     echo "Directory '/test' already exists!";
 * }
 * ```
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class DirectoryAlreadyExists extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $path Path to directory.
     */
    public function __construct(string $path)
    {
        parent::__construct("irectory '{$path}' already exists!");
    }
}
