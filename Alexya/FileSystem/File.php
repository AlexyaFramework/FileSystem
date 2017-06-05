<?php
namespace Alexya\FileSystem;

use \Alexya\FileSystem\Exceptions\{
    CouldntCreateFile,
    FileDoesntExist,
    FileAlreadyExists,
    LineNumberAboveFileLines
};

/**
 * File helper class.
 *
 * This class offers helpers for file related operations.
 *
 * You can check if a file exists with the method `exists`, also you can
 * check file permissions with `isWritable`, `isReadable` and `isExecutable`.
 *
 * For reading/writing to a file you need to instance an object giving the path to
 * the file as parameter. Then you can use the methods `read`, `readLine`, `write` or `append`.
 *
 * Example:
 *
 * ```php
 * if(File::exists("/tmp/test.txt")) {
 *     $file = new File("/tmp/test.txt");
 * } else {
 *     $file = File::make("/tmp/test.txt");
 * }
 *
 * $file->write("Hello ");
 * $file->append("world!")
 * ```
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class File
{
    ///////////////////////////////
    // Start Constant Definition //
    ///////////////////////////////
    const MAKE_FILE_EXISTS_THROW_EXCEPTION = 1;
    const MAKE_FILE_EXISTS_OVERWRITE       = 2;
    const MAKE_FILE_EXISTS_OPEN            = 3;
    /////////////////////////////
    // End Constant Definition //
    /////////////////////////////

    /////////////////////////////////////////
    // Start Static Methods and Properties //
    /////////////////////////////////////////
    /**
     * Checks whether a file exists in the filesystem.
     *
     * @param string $path Path to the file.
     *
     * @return bool Whether $path exists and is a file.
     */
    public static function exists(string $path) : bool
    {
        return file_exists($path) && is_file($path);
    }

    /**
     * Makes a file.
     *
     * The second parameter specifies what to do in case file already exists:
     *
     *  * `\Alexya\FileSystem\File::MAKE_FILE_EXISTS_THROW_EXCEPTION`, throws an exception (default).
     *  * `\Alexya\FileSystem\File::MAKE_FILE_EXISTS_OVERWRITE`, deletes the file and recreates it.
     *  * `\Alexya\FileSystem\File::MAKE_FILE_EXISTS_OPEN`, opens the file.
     *
     * @param string $path     Path to the file.
     * @param int    $ifExists What to do if file exists.
     *
     * @return File File object.
     *
     * @throws FileAlreadyExists If $path already exists as a file.
     * @throws CouldntCreateFile If the file can't be created.
     */
    public static function make(string $path, int $ifExists = File::MAKE_FILE_EXISTS_THROW_EXCEPTION) : File
    {
        $exists = File::exists($path);

        if($exists) {
            if($ifExists === File::MAKE_FILE_EXISTS_OVERWRITE) {
                unlink($path);
            } else if($ifExists === File::MAKE_FILE_EXISTS_OPEN) {
                return new File($path);
            } else {
                throw new FileAlreadyExists($path);
            }
        }

        if(!fopen($path, "c")) {
            throw new CouldntCreateFile($path);
        }

        return new File($path);
    }

    /**
     * Returns file's extension. This only includes the last extension (eg, x.tar.gz -> gz).
     *
     * @param string $path Path to the file.
     *
     * @return string File's extension.
     */
    public static function extension(string $path) : string
    {
        return (pathinfo($path)["extension"] ?? "");
    }

    /**
     * Returns file's name.
     *
     * @param string $path Path to the file.
     *
     * @return string File's name.
     */
    public static function name(string $path) : string
    {
        return (pathinfo($path)["filename"] ?? "");
    }

    /**
     * Returns file's basename (name + extension).
     *
     * @param string $path Path to the file.
     *
     * @return string File's base name.
     */
    public static function basename(string $path) : string
    {
        return (pathinfo($path)["basename"] ?? "");
    }

    /**
     * Returns file's location.
     *
     * @param string $path File path.
     *
     * @return string File's location.
     */
    public static function location(string $path) : string
    {
        return (pathinfo($path)["dirname"] ?? "");
    }
    ///////////////////////////////////////
    // End Static Methods and Properties //
    ///////////////////////////////////////

    /**
     * Path to the file.
     *
     * @var string
     */
    private $_path = "";

    /**
     * File name.
     *
     * @var string
     */
    private $_name = "";

    /**
     * File location.
     *
     * @var string
     */
    private $_location = "";

    /**
     * File extension.
     *
     * @var string
     */
    private $_extension = "";

    /**
     * Constructor.
     *
     * @param string $path Path to the file.
     *
     * @throws FileDoesntExist If $path doesn't exist.
     */
    public function __construct(string $path)
    {
        if(!File::exists($path)) {
            throw new FileDoesntExist($path);
        }

        $this->_path      = $path;
        $this->_location  = File::location($path);
        $this->_name      = File::name($path);
        $this->_extension = File::extension($path);
    }

    /**
     * Checks whether the file has write permission or not.
     *
     * @return bool True if fie is writable, false if not.
     */
    public function isWritable() : bool
    {
        return is_writable($this->_path);
    }

    /**
     * Checks whether the file has read permission or not.
     *
     * @return bool True if fie is readable, false if not.
     */
    public function isReadable() : bool
    {
        return is_readable($this->_path);
    }

    /**
     * Checks whether the file has execution permission or not.
     *
     * @return bool True if fie is executable, false if not.
     */
    public function isExecutable() : bool
    {
        return is_executable($this->_path);
    }

    /**
     * Writes a string to the file.
     *
     * @param string $str String to write.
     */
    public function write(string $str)
    {
        $handler = fopen($this->_path, "w");
        fwrite($handler, $str);
        fclose($handler);
    }

    /**
     * Appends a string to the file.
     *
     * @param string $str String to append.
     */
    public function append(string $str)
    {
        $handler = fopen($this->_path, "a");
        fwrite($handler, $str);
        fclose($handler);
    }

    /**
     * Returns file's contents.
     *
     * @return string File's contents.
     */
    public function getContent() : string
    {
        return file_get_contents($this->_path);
    }

    /**
     * Returns a string of specified length from file.
     *
     * @param int $length Length to read.
     * @param int $offset Length to skip before reading.
     *
     * @return string String from file of given length.
     */
    public function read(int $length = 1, int $offset = 0) : string
    {
        return substr($this->getContent(), $offset, $length);
    }

    /**
     * Returns a string with the lines between $start and $end
     *
     * @param int $start Start line.
     * @param int $end   End line.
     *
     * @return string String with lines between $start and $end.
     */
    public function readBetween(int $start, int $end) : string
    {
        return implode("\n", $this->readLinesBetween($start, $end));
    }

    /**
     * Returns a single line from file.
     *
     * @param int $line Line number.
     *
     * @return string Line as string.
     *
     * @throws LineNumberAboveFileLines If the line number is bigger than the total number of lines
     */
    public function readLine(int $line = 1) : string
    {
        $lines = file($this->_path);

        if($line > count($lines)) {
            throw new LineNumberAboveFileLines($this->_path, $line, count($lines));
        }

        if($line <= 0) {
            $line = 1;
        }

        return $lines[($line - 1)];
    }

    /**
     * Returns an array containing the lines between $start and $end.
     *
     * @param int $start Start line.
     * @param int $end   End line.
     *
     * @return array Array with lines between $start and $end.
     */
    public function readLinesBetween(int $start, int $end) : array
    {
        $content = file($this->_path);
        $lines   = [];

        for($i = $start; $i < $end; $i++) {
            if(isset($content[$i])) {
                $lines[] = $content[$i];
            }
        }

        return $lines;
    }

    ///////////////////////////////
    // Start Getters and Setters //
    ///////////////////////////////
    /**
     * Moves the file to the specified location.
     *
     * @param string $location New file location.
     */
    public function setLocation(string $location)
    {
        $dir = Directory::make($location, Directory::MAKE_DIRECTORY_EXISTS_OPEN);

        $old = $this->getPath();
        $new = $dir->getPath().DIRECTORY_SEPARATOR.$this->getBasename();

        if(rename($old, $new)) {
            $this->_location = $dir->getPath();
        }
    }

    /**
     * Renames the file
     *
     * @param string $name New file name.
     */
    public function setName(string $name)
    {
        $old = $this->getPath();
        $new = $this->getLocation().DIRECTORY_SEPARATOR.$name;

        if(!empty($this->getExtension())) {
            $new .= ".".$this->getExtension();
        }

        if(rename($old, $new)) {
            $this->_name = $name;
        }
    }

    /**
     * Renames file extension.
     *
     * @param string $extension New file extension.
     */
    public function setExtension(string $extension)
    {
        $old = $this->getPath();
        $new = $this->getLocation().DIRECTORY_SEPARATOR.$this->getName();

        if(!empty($extension)) {
            $new .= ".{$extension}";
        }

        if(rename($old, $new)) {
            $this->_extension = $extension;
        }
    }

    /**
     * Sets new file location, name and extension.
     *
     * @param string $path New path to file (location + name + extension).
     */
    public function setPath(string $path)
    {
        $info = pathinfo($path);

        $this->setLocation($info["dirname"] ?? "");
        $this->setName($info["filename"] ?? "");
        $this->setExtension($info["extension"] ?? "");
    }

    /**
     * Returns file location.
     *
     * @return string File's location.
     */
    public function getLocation() : string
    {
        return $this->_location;
    }

    /**
     * Returns file name.
     *
     * @return string File's name.
     */
    public function getName() : string
    {
        return $this->_name;
    }

    /**
     * Returns file extension.
     *
     * @return string File's extension.
     */
    public function getExtension() : string
    {
        return $this->_extension;
    }

    /**
     * Returns file's base name (name + extension)
     *
     * @return string File's basename
     */
    public function getBasename() : string
    {
        $basename = $this->getName();

        if(!empty($this->getExtension())) {
            $basename .= ".".$this->getExtension();
        }

        return $basename;
    }

    /**
     * Returns file path.
     *
     * @return string File's path.
     */
    public function getPath() : string
    {
        return $this->getLocation().DIRECTORY_SEPARATOR.$this->getBasename();
    }
    /////////////////////////////
    // End Getters and Setters //
    /////////////////////////////
}
