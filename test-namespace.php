<?php
// test-namespace.php

// Include Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

// Define a simple test class in the Exorgroup\Apex namespace
class_exists('ExorGroup\Apex\ApexServiceProvider')
    ? print("Class exists!\n")
    : print("Class not found!\n");

// Print the actual file location that should contain the class
$reflection = new ReflectionClass('Composer\Autoload\ClassLoader');
$loader = $reflection->newInstance();
$classMap = $loader->getClassMap();
$prefixes = $loader->getPrefixesPsr4();

print("PSR-4 Prefixes:\n");
var_dump($prefixes);

// Try to find the file for the class
$possibleFile = str_replace('\\', '/', 'ExorGroup/Apex/ApexServiceProvider.php');
print("\nChecking if file exists: {$possibleFile}\n");
file_exists($possibleFile)
    ? print("File exists!\n")
    : print("File not found!\n");

// Check actual directory path
print("\nDirectory listing:\n");
$directories = glob('*', GLOB_ONLYDIR);
var_dump($directories);
