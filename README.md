# FileSystem
Alexya's filesystem components

## Contents

- [File operations](#file_operations)
    - [Instancing File Objects](#instancing_file_objects)
    - [File permissions](#file_permissions)
    - [File information](#file_information)
    - [Reading/Writing to a file](#reading_writing_to_a_file)

## File Operations
Reading/Writing to a file is really easy with the class `\Alexya\FileSystem\File`.

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
$name      = \Alexya\FileSystem\File::getName("/tmp/test.txt");      // $name      = "test"
$extension = \Alexya\FileSystem\File::getExtension("/tmp/test.txt"); // $extension = "txt"
$basename  = \Alexya\FileSystem\File::getBasename("/tmp/test.txt");  // $basename  = "test.txt"
$path      = \Alexya\FileSystem\File::getPath("/tmp/test.txt");      // $path      = "/tmp/test.txt"
$location  = \Alexya\FileSystem\File::getLocation("/tmp/test.txt");  // $location  = "/tmp"

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
