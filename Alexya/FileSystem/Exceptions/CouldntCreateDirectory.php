<?php
namespace Alexya\FileSystem\Exceptions;

/**
 * Couldn't create exception.
 *
 * This exception is thrown when you try to create a directory and an error happened.
 *
 * Example:
 *
 * ```php
 * try {
 *     $dir = Directory::make("/test");
 * } catch(CouldntCreateDirectory $e) {
 *     echo "Couldn't create directory '/test'";
 * }
 * ```
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class CouldntCreateDirectory extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $path Path to directory.
     */
    public function __construct(string $path)
    {
        parent::__construct("Couldn't create directory '{$path}'");
    }
}
