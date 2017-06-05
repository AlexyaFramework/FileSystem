<?php
namespace Alexya\FileSystem;

use Alexya\FileSystem\Exceptions\{
    CouldntCreateDirectory,
    CouldntCreateFile,
    DirectoryAlreadyExists,
    FileDoesntExist,
    DirectoryDoesntExist
};

/**
 * Directory helper class.
 *
 * This class offers helpers for directory related operations.
 *
 * You can check if a directory exists with the method `exists`, also you can
 * check if a directory contains an specific sub-directory or folder with the
 * methods `directoryExists` and `fileExists`.
 *
 * To iterate through the sub-directories use the method `getDirectories`.
 * To iterate through the files use the method `getFiles`.
 *
 * Example:
 *
 * ```php
 * if(Directory::exists("/tmp")) {
 *     $dir = new Directory("/tmp");
 * } else {
 *     $dir = Directory::make("/tmp");
 * }
 *
 * echo "Sub-directories of ". $dir->getPath() ."\n";
 * foreach($dir->getDirectories() as $subdir) {
 *     echo $subdir->getPath() ."\n";
 * }
 *
 * echo "Files of ". $dir->getPath() ."\n";
 * foreach($dir->getFiles() as $file) {
 *     echo $file->getPath() ."\n";
 * }
 * ```
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Directory
{
    ///////////////////////////////
    // Start Constant Definition //
    ///////////////////////////////
    const MAKE_DIRECTORY_EXISTS_THROW_EXCEPTION = 1;
    const MAKE_DIRECTORY_EXISTS_OVERWRITE       = 2;
    const MAKE_DIRECTORY_EXISTS_OPEN            = 3;

    const GET_FILE_NOT_EXISTS_THROW_EXCEPTION = 1;
    const GET_FILE_NOT_EXISTS_CREATE          = 2;

    const GET_DIRECTORY_NOT_EXISTS_THROW_EXCEPTION = 1;
    const GET_DIRECTORY_NOT_EXISTS_CREATE          = 2;
    /////////////////////////////
    // End Constant Definition //
    /////////////////////////////

    /////////////////////////////////////////
    // Start Static Methods and Properties //
    /////////////////////////////////////////
    /**
     * Checks whether a directory exists in the filesystem.
     *
     * @param string $path Path to the directory.
     *
     * @return bool Whether $path exists and is a directory.
     */
    public static function exists(string $path) : bool
    {
        return is_dir($path);
    }

    /**
     * Makes a directory.
     *
     * The second parameter specifies what to do in case directory already exists:
     *
     *  * `\Alexya\FileSystem\Directory::MAKE_DIRECTORY_EXISTS_THROW_EXCEPTION`, throws an exception (default).
     *  * `\Alexya\FileSystem\Directory::MAKE_DIRECTORY_EXISTS_OVERWRITE`, deletes the directory and recreates it.
     *  * `\Alexya\FileSystem\Directory::MAKE_DIRECTORY_EXISTS_OPEN`, opens the directory.
     *
     * @param string $path     Path to the directory.
     * @param int    $ifExists What to do if directory exists.
     *
     * @return Directory Directory object.
     *
     * @throws DirectoryAlreadyExists If $path already exists as a directory.
     * @throws CouldntCreateDirectory If the directory can't be created.
     */
    public static function make(string $path, int $ifExists = Directory::MAKE_DIRECTORY_EXISTS_THROW_EXCEPTION) : Directory
    {
        $exists = Directory::exists($path);

        if($exists) {
            if($ifExists === Directory::MAKE_DIRECTORY_EXISTS_OVERWRITE) {
                unlink($path);
            } else if($ifExists === Directory::MAKE_DIRECTORY_EXISTS_OPEN) {
                return new Directory($path);
            } else {
                throw new DirectoryAlreadyExists($path);
            }
        }

        if(!mkdir($path)) {
            throw new CouldntCreateDirectory($path);
        }

        return new Directory($path);
    }

    /**
     * Returns directory's name.
     *
     * @param string $path Path to the directory.
     *
     * @return string Directory's name.
     */
    public static function name(string $path) : string
    {
        return (pathinfo($path)["basename"] ?? "");
    }

    /**
     * Returns directory's location.
     *
     * @param string $path Directory path.
     *
     * @return string Directory's location.
     */
    public static function location(string $path) : string
    {
        return (pathinfo($path)["dirname"] ?? "");
    }
    ///////////////////////////////////////
    // End Static Methods and Properties //
    ///////////////////////////////////////

    /**
     * Path to the directory.
     *
     * @var string
     */
    private $_path = "";

    /**
     * Directory name.
     *
     * @var string
     */
    private $_name = "";

    /**
     * Directory location.
     *
     * @var string
     */
    private $_location = "";

    /**
     * Whether files and sub-directories have been loaded.
     *
     * @var bool
     */
    private $_isLoaded = false;

    /**
     * Array containing directories.
     *
     * @var Directory[]
     */
    private $_directories = [];

    /**
     * Array containing files.
     *
     * @var File[]
     */
    private $_files = [];

    /**
     * Constructor.
     *
     * @param string $path Path to the Directory.
     *
     * @throws DirectoryDoesntExist If `$path` doesn't exist.
     */
    public function __construct(string $path)
    {
        if(!Directory::exists($path)) {
            throw new DirectoryDoesntExist($path);
        }

        $this->_path     = $path;
        $this->_location = Directory::location($path);
        $this->_name     = Directory::name($path);
    }

    /**
     * Checks if a file exists in this directory.
     *
     * @param string $name File name.
     *
     * @return bool `true` if `$name` exists as a file in this directory, `false` if not.
     */
    public function fileExists(string $name)
    {
        if(!$this->_isLoaded) {
            $this->load();
        }

        return in_array($name, $this->_files);
    }

    /**
     * Returns a file from the directory.
     *
     * The second parameter specifies what to do in case file doesn't exist:
     *
     *  * `\Alexya\FileSystem\Directory::GET_FILE_NOT_EXISTS_THROW_EXCEPTION`, throws an exception (default).
     *  * `\Alexya\FileSystem\Directory::GET_FILE_NOT_EXISTS_CREATE`, creates the file.
     *
     * @param string $name         File name.
     * @param int    $ifNotExists What to do if file exists.
     *
     * @return File File object.
     *
     * @throws FileDoesntExist   If $path doesn't exist as a file.
     * @throws CouldntCreateFile If the file can't be created.
     */
    public function getFile(string $name, int $ifNotExists = Directory::GET_FILE_NOT_EXISTS_THROW_EXCEPTION) : File
    {
        $path = $this->getPath(). DIRECTORY_SEPARATOR .$name;

        if($this->fileExists($name)) {
            return new File($path);
        }

        if($ifNotExists === Directory::GET_FILE_NOT_EXISTS_CREATE) {
            $this->_files[$name] = $name;

            return File::make($path, File::MAKE_FILE_EXISTS_OPEN);
        }

        throw new FileDoesntExist($path);
    }

    /**
     * Returns an array with all files of the directory.
     *
     * @return File[] Array with `\Alexya\FileSystem\File` objects for each file on the directory.
     */
    public function getFiles() : array
    {
        if(!$this->_isLoaded) {
            $this->load();
        }

        return array_map(function($file) {
            return new File($this->getPath(). DIRECTORY_SEPARATOR .$file);
        }, $this->_files);
    }

    /**
     * Checks if a directory exists in this directory.
     *
     * @param string $name Directory name.
     *
     * @return bool `true` if `$name` exists as a directory in this directory, `false` if not.
     */
    public function directoryExists(string $name)
    {
        if(!$this->_isLoaded) {
            $this->load();
        }

        return in_array($name, $this->_directories);
    }

    /**
     * Returns a directory from the directory.
     *
     * The second parameter specifies what to do in case directory doesn't exist:
     *
     *  * `\Alexya\FileSystem\Directory::GET_DIRECTORY_NOT_EXISTS_THROW_EXCEPTION`, throws an exception (default).
     *  * `\Alexya\FileSystem\Directory::GET_DIRECTORY_NOT_EXISTS_CREATE`, creates the directory.
     *
     * @param string $name         Directory name.
     * @param int    $ifNotExists What to do if directory exists.
     *
     * @return Directory Directory object.
     *
     * @throws DirectoryDoesntExist   If $path doesn't exist as a directory.
     * @throws CouldntCreateDirectory If the directory can't be created.
     */
    public function getDirectory(string $name, int $ifNotExists = Directory::GET_DIRECTORY_NOT_EXISTS_THROW_EXCEPTION) : Directory
    {
        $path = $this->getPath(). DIRECTORY_SEPARATOR .$name;

        if($this->directoryExists($name)) {
            return new Directory($path);
        }

        if($ifNotExists === Directory::GET_DIRECTORY_NOT_EXISTS_CREATE) {
            return Directory::make($path);
        }

        throw new DirectoryDoesntExist($path);
    }

    /**
     * Returns an array with all directories of the directory.
     *
     * @return Directory[] Array with `\Alexya\FileSystem\Directory` objects for each directory on the directory.
     */
    public function getDirectories() : array
    {
        if(!$this->_isLoaded) {
            $this->load();
        }

        return array_map(function($directory) {
            return new Directory($this->getPath(). DIRECTORY_SEPARATOR .$directory);
        }, $this->_directories);
    }

    /**
     * Loads directories and files.
     */
    public function load()
    {
        $files = scandir($this->getPath());

        foreach($files as $file) {
            if(
                $file === "." ||
                $file === ".."
            ) {
                continue;
            }

            if(is_file($this->getPath().$file)) {
                $this->_files[$file] = $file;
            } else if(is_dir($this->getPath().$file)) {
                $this->_directories[$file] = $file;
            }
        }
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
        $new = $dir->getPath();

        if(rename($old, $new)) {
            $this->_location = $dir->getPath();
        }
    }

    /**
     * Renames the file.
     *
     * @param string $name New file name.
     */
    public function setName(string $name)
    {
        $old = $this->getPath();
        $new = $this->getLocation().DIRECTORY_SEPARATOR.$name;

        if(rename($old, $new)) {
            $this->_name = $name;
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
     * Returns file path.
     *
     * @return string File's path.
     */
    public function getPath() : string
    {
        return $this->getLocation().DIRECTORY_SEPARATOR.$this->getName();
    }
    /////////////////////////////
    // End Getters and Setters //
    /////////////////////////////
}
