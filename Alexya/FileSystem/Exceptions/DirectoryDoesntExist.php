<?php
namespace Alexya\FileSystem\Exceptions;

/**
 * Directory doesn't exist exception.
 *
 * This exception is thrown when you try to read/write from a directory that doesn't exist.
 *
 * Example:
 *
 * ```php
 * try {
 *     $directory = new Directory("/test");
 * } catch(DirectoryDoesntExist $e) {
 *     echo "Directory '/test' doesn't exist!";
 * }
 * ```
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class DirectoryDoesntExist extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $path Path to directory.
     */
    public function __construct(string $path)
    {
        parent::__construct("Directory '{$path}' doesn't exist!");
    }
}
