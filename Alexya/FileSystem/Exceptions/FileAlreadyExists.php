<?php
namespace Alexya\FileSystem\Exceptions;

/**
 * File already exists exception.
 *
 * This exception is thrown when you try to create a file that already exists.
 *
 * Example:
 *
 * ```php
 * try {
 *     $file = File::make("test.txt");
 * } catch(FileAlreadyExist $e) {
 *     echo "File 'test.txt' already exists!";
 * }
 * ```
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class FileAlreadyExists extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $path Path to file.
     */
    public function __construct(string $path)
    {
        parent::__construct("File '{$path}' already exists!");
    }
}
