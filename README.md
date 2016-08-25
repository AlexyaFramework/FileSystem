# FileSystem
Alexya's filesystem components

## Contents

- [File Operations](#file_operations)
    - [Instancing File Objects](#instancing_file_objects)
    - [File permissions](#file_permissions)
    - [File information](#file_information)
    - [Reading/Writing to a file](#reading_writing_to_a_file)
- [Directory Operations](#directory_operations)
    - [Instancing Directory Objects](#instancing_irectory_objects)
    - [Directory files](#irectory_files)
    - [Directory subdirectories](#irectory_subdirectories)
    - [Directory information](#irectory_information)

<a name="file_operations"></a>
## File Operations
Reading/Writing to a file is really easy with the class `\Alexya\FileSystem\File`.

<a name="instancing_file_objects"></a>
### Instancing File objects

The constructor accepts as parameter the path to the file that will be used for I/O operations, if the file doesn't
exist it will throw an exception of type `\Alexya\FileSystem\Exceptions\FileDoesntExist`.

You can check if a file exists with the method `\Alexya\FileSystem\File::exists` which accepts as parameter the path
to the file and returns `true` if the path exists and is a file (or `false` if it doesn't).

To make a file use the method `\Alexya\FileSystem\File::make` which accepts as parameter the path to the file that
will be created and returns an instance of the file object. If the file already exists it will throw an exception of
type `\Alexya\FileSystem\Exceptions\FileAlreadyExists`, however, you can change this behavior with the second parameter
that is an integer and tells what to do if the path already exists:

 * `\Alexya\FileSystem\File::MAKE_FILE_EXISTS_THROW_EXCEPTION`: throws an exception (default).
 * `\Alexya\FileSystem\File::MAKE_FILE_EXISTS_OVERWRITE`: deletes the file and recreates it.
 * `\Alexya\FileSystem\File::MAKE_FILE_EXISTS_OPEN`: opens the file.

Example:

```php
<?php

if(\Alexya\FileSystem\File::exists("/tmp/test.txt")) {
    $file = new \Alexya\FileSystem\File("/tmp/test.txt");
} else {
    $file = \Alexya\FileSystem\File::make("/tmp/test.txt");
}
```

Or a shorter way:

```php
<?php

$file = \Alexya\FileSystem\File::make("/tmp/test.txt", \Alexya\FileSystem\File::MAKE_FILE_EXISTS_OPEN);
```

Once you've instanced the file object you can retrieve information of the file such as permissions and path information
and read/write to the file.

<a name="file_permissions"></a>
### File permissions
You can check file permissions with the following methods:

 * `isReadable`, returns `true` if the file has read permissions.
 * `isWritable`, returns `true` if the file has write permissions.
 * `isExecutable`, returns `true` if the file has execution permissions.

Example:

```php
<?php
/**
 * File permissions, the same way as executing `ls -l`
 */
$permissions = "-";

$file = \Alexya\FileSystem\File::make("/tmp/test.txt", \Alexya\FileSystem\File::MAKE_FILE_EXISTS_OPEN);

if($file->isReadable()) {
    $permissions .= "r";
} else {
    $permissions .= "-";
}
if($file->isWritable()) {
    $permissions .= "w";
} else {
    $permissions .= "-";
}
if($file->isExecutable()) {
    $permissions .= "x";
} else {
    $permissions .= "-";
}
```

<a name="file_information"></a>
### File information
You can use the followin methods for retrieving file information:

 * `getName`: Returns file name, without extension.
 * `getExtension`: Returns file extension.
 * `getBasename`: Returns file name + extension.
 * `getPath`: Returns full path to the file (location + name + extension).
 * `getLocation`: Returns path to the directory that contains the file.

The methods can be accessed statically, but you must send the path to the file as the parameter.

To change the information of a file use the following methods:

 * `setName`: Renames a file.
 * `setExtension`: Changes file's extension.
 * `setBasename`: Changes file name and extension.
 * `setPath`: Changes full path to the file.
 * `setLocation`: Changes the location to the file.

Example:

```php
<?php

// Static calls
$name      = \Alexya\FileSystem\File::name("/tmp/test.txt");      // $name      = "test"
$extension = \Alexya\FileSystem\File::extension("/tmp/test.txt"); // $extension = "txt"
$basename  = \Alexya\FileSystem\File::basename("/tmp/test.txt");  // $basename  = "test.txt"
$path      = \Alexya\FileSystem\File::path("/tmp/test.txt");      // $path      = "/tmp/test.txt"
$location  = \Alexya\FileSystem\File::location("/tmp/test.txt");  // $location  = "/tmp"

// Object calls
$file = \Alexya\FileSystem\File::make("/tmp/test.txt", \Alexya\FileSystem\File::MAKE_FILE_EXISTS_OPEN);

$name      = $file->getName();      // $name      = "test"
$extension = $file->getExtension(); // $extension = "txt"
$basename  = $file->getBasename();  // $basename  = "test.txt"
$path      = $file->getPath();      // $path      = "/tmp/test.txt"
$location  = $file->getLocation();  // $location  = "/tmp"

$file->setName("foo");            // File path: /tmp/foo.txt
$file->setExtension("bar");       // File path: /tmp/foo.bar
$file->setBasename("bar.foo");    // File path: /tmp/bar.foo
$file->setPath("/test/test.txt"); // File path: /test/test.txt
$file->setLocation("/home");      // File path: /home/test.txt
```

<a name="reading_writing_to_a_file"></a>
### Reading/Writing to a file
Writing to a file is as easy as using any of the following methods:

 * `write`: Overwrites file contents with given parameter.
 * `append`: Appends given parameter to the file.

The `\Alexya\FileSystem\File` class offers numerous ways for reading from a file, you can use the following methods:

 * `getContent`: Returns the content of the file.
 * `read`: Reads from a file, the first parameter is the amount of bytes to read, the second is the offset of bytes to wait before reading.
 * `readBetween`: Reads some lines of the file, the first parameter is the starting line, the second is the last line.
 * `readLine`: Reads a single line, the parameter is the line number to read.
 * `readLinesBetween`: Same as `readBetween` but instead of returning a `string` returns an `array`.

Example:

```php
<?php

$file = \Alexya\FileSystem\File::make("/tmp/test.txt", \Alexya\FileSystem\File::MAKE_FILE_EXISTS_OPEN);

$file->write("Foo
Bar
test
Test");
$file->append("Bar");

$content = $file->getContent();
/*
$content = "Foo
Bar
test
TestBar"
 */

$firstThreeBytes = $file->read(3, 0);
/*
$firstThreeBytes = "Foo";
 */

$nextThreeBytes = $file->read(3, 3);
/*
$nextThreeBytes = "
Ba"
 */

$between = $file->readBetween(2, 4);
/*
$between = "Bar
test"
 */

$thirdLine = $file->readLine(3);
/*
$thirdLine = "test"
 */

$linesBetween = $file->readLinesBetween(2, 4);
/*
$linesBetween = [
    "Bar",
    "test"
]
 */
```

<a name="directory_operations"></a>
## Directory Operations
The class `\Alexya\FileSystem\Directory` offers a clean way to interact with directories.

<a name="instancing_directory_objects"></a>
### Instancing Directory objects

The constructor accepts as parameter the path to the directory, if the directory doesn't exist it will throw an
exception of type `\Alexya\FileSystem\Exceptions\DirectoryDoesntExist`.

You can check if a directory exists with the method `\Alexya\FileSystem\Directory::exists` which accepts as parameter the path
to the directory and returns `true` if the path exists and is a directory (or `false` if it doesn't).

To make a directory use the method `\Alexya\FileSystem\Directory::make` which accepts as parameter the path to the directory that
will be created and returns an instance of the directory object. If the directory already exists it will throw an exception of
type `\Alexya\FileSystem\Exceptions\DirectoryAlreadyExists`, however, you can change this behavior with the second parameter
that is an integer and tells what to do if the path already exists:

 * `\Alexya\FileSystem\Directory::MAKE_DIRECTORY_EXISTS_THROW_EXCEPTION`: throws an exception (default).
 * `\Alexya\FileSystem\Directory::MAKE_DIRECTORY_EXISTS_OVERWRITE`: deletes the directory and recreates it.
 * `\Alexya\FileSystem\Directory::MAKE_DIRECTORY_EXISTS_OPEN`: opens the directory.

Example:

```php
<?php

if(\Alexya\FileSystem\Directory::exists("/tmp")) {
    $directory = new \Alexya\FileSystem\Directory("/tmp");
} else {
    $directory = \Alexya\FileSystem\Directory::make("/tmp");
}
```

Or a shorter way:

```php
<?php

$directory = \Alexya\FileSystem\Directory::make("/tmp", \Alexya\FileSystem\Directory::MAKE_DIRECTORY_EXISTS_OPEN);
```

Once you've instanced the directory object you can retrieve information of the directory such as files and subdirectories.

<a name="directory_files"></a>
### Directory files
You can check if a file exists in the directory with the method `fileExists`, the parameter is the basename of
the file (name + extension) and returns `true` if the file exists (or `false` if not).

If you want to retrieve a file from the directory use the method `getFile`, the parameter is the basename of
the file (name + extension) and returns a `\Alexya\FileSystem\File` object of the file. If the file doesn't exists
it will throw an exception of type `\Alexya\FileSystem\Exceptions\FileDoesntExists`, however, you can change this
behavior with the second parameter that is an integer and tells what to do if the file doesn't exists:

 * `\Alexya\FileSystem\Directory::GET_FILE_NOT_EXISTS_THROW_EXCEPTION`, throws an exception (default).
 * `\Alexya\FileSystem\Directory::GET_FILE_NOT_EXISTS_CREATE`, creates the file.

The method `getFiles` returns an array of `\Alexya\FileSystem\File` objects with all files in the directory.

Example:

```php
<?php
/*
 * Directory /tmp:
 * test     (directory)
 * foo      (directory)
 * bar      (file)
 * test.txt (file)
 */

$directory = \Alexya\FileSystem\Directory::make("/tmp", \Alexya\FileSystem\Directory::MAKE_DIRECTORY_EXISTS_OPEN);

$fileExists = $directory->fileExists("file"); // $fileExists = false
$bar        = $directory->getFile("bar"); // $bar = new \Alexya\FileSystem\File("/tmp/bar")
$files      = $directory->getFiles();
/*
$files = [
    new \Alexya\FileSystem\File("/tmp/bar"),
    new \Alexya\FileSystem\File("/tmp/test.txt")
]
 */

```

<a name="directory_subdirectories"></a>
### Directory subdirectories
You can check if a directory exists in the directory with the method `directoryExists`, the parameter is the name of
the directory and returns `true` if the directory exists (or `false` if not).

If you want to retrieve a directory from the directory use the method `getDirectory`, the parameter is the name of
the directory and returns a `\Alexya\FileSystem\Directory` object of the directory. If the directory doesn't exists
it will throw an exception of type `\Alexya\FileSystem\Exceptions\DirectoryDoesntExists`, however, you can change this
behavior with the second parameter that is an integer and tells what to do if the directory doesn't exists:

 * `\Alexya\FileSystem\Directory::GET_DIRECTORY_NOT_EXISTS_THROW_EXCEPTION`, throws an exception (default).
 * `\Alexya\FileSystem\Directory::GET_DIRECTORY_NOT_EXISTS_CREATE`, creates the directory.

The method `getDirectories` returns an array of `\Alexya\FileSystem\Directory` objects with all directories in the directory.

Example:

```php
<?php
/*
 * Directory /tmp:
 * test     (directory)
 * foo      (directory)
 * bar      (file)
 * test.txt (file)
 */

$directory = \Alexya\FileSystem\Directory::make("/tmp", \Alexya\FileSystem\Directory::MAKE_DIRECTORY_EXISTS_OPEN);

$directoryExists = $directory->directoryExists("dir"); // $directoryExists = false
$test            = $directory->getDirectory("test"); // $test = new \Alexya\FileSystem\Directory("/tmp/test")
$directories     = $directory->getDirectories();
/*
$directories = [
    new \Alexya\FileSystem\Directory("/tmp/test"),
    new \Alexya\FileSystem\Directory("/tmp/foo")
]
 */

```

<a name="directory_information"></a>
### Directory information
You can use the followin methods for retrieving directory information:

 * `getName`: Returns directory name.
 * `getPath`: Returns full path to the directory (location + name).
 * `getLocation`: Returns path to the directory that contains the directory.

The methods can be accessed statically, but you must send the path to the directory as the parameter.

To change the information of a directory use the following methods:

 * `setName`: Renames a directory.
 * `setPath`: Changes full path to the directory.
 * `setLocation`: Changes the location to the directory.

Example:

```php
<?php

// Static calls
$name      = \Alexya\FileSystem\Directory::name("/tmp/test");     // $name      = "test"
$path      = \Alexya\FileSystem\Directory::path("/tmp/test");     // $path      = "/tmp/test"
$location  = \Alexya\FileSystem\Directory::location("/tmp/test"); // $location  = "/tmp"

// Object calls
$file = \Alexya\FileSystem\Directory::make("/tmp/test", \Alexya\FileSystem\Directory::MAKE_DIRECTORY_EXISTS_OPEN);

$name      = $file->getName();      // $name      = "test"
$path      = $file->getPath();      // $path      = "/tmp/test"
$location  = $file->getLocation();  // $location  = "/tmp"

$file->setName("foo");        // Directory path: /tmp/foo
$file->setPath("/test/test"); // Directory path: /test/test
$file->setLocation("/home");  // Directory path: /home/test
```
