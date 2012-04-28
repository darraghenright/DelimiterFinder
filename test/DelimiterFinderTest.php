<?php

require_once __DIR__ . '/../src/DelimiterFinder.php';
require_once 'PHPUnit/Autoload.php';

class DelimiterFinderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Set up before tests
     */
    public function setUp()
    {           
        // make sure non-readable file is non-readable!
        chmod(__DIR__ . '/files/not_readable.csv', 0000);
    }
    
    /**
     * Clean up after tests
     */
    public function tearDown()
    {
        // change perms so we can commit the file ;)
        chmod(__DIR__ . '/files/not_readable.csv', 0444);
    }
    
    /**
     * Omitting a filepath as an argument to 
     * the constructor raises a PHP Error
     *
     * @group                       Constructor Tests
     * @expectedException           PHPUnit_Framework_Error
     */
    public function testCreateObjectWithMissingArgument()
    {
        $finder = new DelimiterFinder();        
    }
    
    /**
     * Providing a non-existent filepath as an argument to 
     * the constructor raises an InvalidArgumentException
     *
     * @group                       Constructor Tests
     * @expectedException           InvalidArgumentException
     * @expectedExceptionMessage    The file files/non_existent_file.csv does not exist
     */
    public function testCreateObjectWithNonExistentFile()
    {
        $filepath = 'files/non_existent_file.csv';
        $this->assertFalse(is_file($filepath));
        $finder = new DelimiterFinder($filepath);
    }
    
    /**
     * Providing a non-readable filepath as an argument to 
     * the constructor throws an InvalidArgumentException
     *
     * @group                       Constructor Tests
     * @expectedException           InvalidArgumentException
     * @expectedExceptionMessage    The file files/not_readable.csv cannot be read
     */
    public function testCreateObjectWithNonReadableFile()
    {   
        $filepath = 'files/not_readable.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
    }   

    /**
     * Providing a filepath to an empty file as an argument 
     * to the constructor throws an InvalidArgumentException
     *
     * @group                       Constructor Tests
     * @expectedException           RuntimeException
     * @expectedExceptionMessage    The file files/empty.csv is empty
     */
    public function testCreateObjectWithEmptyFile()
    {
        $filepath = 'files/empty.csv';
        $this->assertFileExists($filepath);
        $this->assertTrue(filesize($filepath) === 0);
        $finder = new DelimiterFinder($filepath);
    }
        
    /**
     * Providing a valid filepath as an argument to 
     * the constructor creates an instance
     *
     * @group                       Constructor Tests
     */
    public function testObjectCreateWithValidFile()
    {    
        $filepath = 'files/not_empty.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
        $this->assertInstanceOf('DelimiterFinder', $finder);
    }
}
