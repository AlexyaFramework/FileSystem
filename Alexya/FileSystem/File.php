<?php
namespace Alexya\FileSystem;

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
 *     if(File::exists("/tmp/test.txt")) {
 *         $file = new File("/tmp/test.txt");
 *     } else {
 *         $file = File::make("/tmp/test.txt");
 *     }
 *
 *     $file->write("Hello ");
 *     $file->append("world!")
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class File
{
    /////////////////////////////////////////
    // Start Static Methods and Properties //
    /////////////////////////////////////////
    /**
     * Checks wether a file exists in the filesystem.
     *
     * @param string $path Path to the file.
     *
     * @return bool Wether $path exists and is a file.
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
     * @return \Alexya\FileSystem\File File object.
     *
     * @throws \Alexya\FileSystem\Exceptions\FileAlreadyExists If $path already exists as a file.
     * @throws \Alexya\FileSystem\Exceptions\CouldntCreateFile If the file can't be created.
     */
    public static function make(string $path) : File
    {
        $exists = File::exists($path);

        if($ifExists == File::MAKE_FILE_EXISTS_THROW_EXCEPTION) {
            throw new FileAlreadyExists($path);
        } else if($ifExists == File::MAKE_FILE_EXISTS_OVERWRITE) {

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
    public static function getExtension(string $path) : string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Returns file's name.
     *
     * @param string $path Path to the file.
     *
     * @return string File's name.
     */
    public static function getName(string $path) : string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Returns file's basename (name + extension).
     *
     * @param string $path Path to the file.
     *
     * @return string File's base name.
     */
    public static function getBasename(string $path) : string
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Returns file's location.
     *
     * @param string $path File path.
     *
     * @return string File's location.
     */
    public static function getLocation(string $path) : string
    {
        return pathinfo($path, PATHINFO_DIRNAME);
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
     * File handler resource.
     *
     * @var resource
     */
    private $_handler = null;

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
     * @throws \Alexya\FileSystem\Exceptions\FileDoesntExist If $path doesn't exist.
     */
    public function __construct(string $path)
    {
        if(!File::exists($path)) {
            throw new FileDoesntExist($path);
        }

        $this->_path      = $path;
        $this->_location  = File::getLocation($path);
        $this->_name      = File::getName($path);
        $this->_extension = File::getExtension($path);
    }

    // TODO: add is(Writable|Readable|Executable), write, append, getLine, getAllLines, move, delete,
}
