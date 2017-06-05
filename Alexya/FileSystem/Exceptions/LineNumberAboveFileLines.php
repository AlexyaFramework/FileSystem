<?php
namespace Alexya\FileSystem\Exceptions;

/**
 * Line number above file lines exception.
 *
 * This exception is thrown when you try to read more lines than
 * the file has.
 *
 * Example:
 *
 * ```php
 * // File test.txt:
 * // Line number 1
 * // Line number 2
 * // Line number 3
 * $file = new File("test.txt");
 *
 * for($i = 0; $i < 5; $i++) {
 *     try {
 *         echo $file->getLine($i)."\n";
 *     } catch(LineNumberAboveFileLines $e) {
 *         echo "Line {$i} doesn't exist!";
 *     }
 * }
 *
 * // Output:
 * // Line number 1
 * // Line number 2
 * // Line number 3
 * // Line 4 doesn't exist!
 * ```
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class LineNumberAboveFileLines extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $path       Path to file.
     * @param int    $line       Requested line.
     * @param int    $totalLines File's total lines.
     */
    public function __construct(string $path, int $line, int $totalLines)
    {
        parent::__construct("Line {$line} doesn't exist on '{$path}', total lines: {$totalLines}.");
    }
}
