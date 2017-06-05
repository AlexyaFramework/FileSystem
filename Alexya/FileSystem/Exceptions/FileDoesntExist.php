<?php
namespace Alexya\FileSystem\Exceptions;

/**
 * File doesn't exist exception.
 *
 * This exception is thrown when you try to read/write to a file that doesn't exist.
 *
 * Example:
 *
 * ```php
 * try {
 *     $file = new File("test.txt");
 * } catch(FileDoesntExist $e) {
 *     echo "File 'test.txt' doesn't exist!";
 * }
 * ```
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class FileDoesntExist extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $path Path to file.
     */
    public function __construct(string $path)
    {
        parent::__construct("File '{$path}' doesn't exist!");
    }
}
