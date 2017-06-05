<?php
namespace Alexya\FileSystem\Exceptions;

/**
 * Couldn't create exception.
 *
 * This exception is thrown when you try to create a file and an error happened.
 *
 * Example:
 *
 * ```php
 * try {
 *     $file = File::make("test.txt");
 * } catch(CouldntCreateFile $e) {
 *     echo "Couldn't create file 'test.txt'";
 * }
 * ```
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class CouldntCreateFile extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $path Path to file.
     */
    public function __construct(string $path)
    {
        parent::__construct("Couldn't create file '{$path}'");
    }
}
