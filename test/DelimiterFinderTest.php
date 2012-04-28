<?php

require_once __DIR__ . '/../src/DelimiterFinder.php';
require_once 'PHPUnit/Autoload.php';

class DelimiterFinderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // @TODO: look at vfsStream for mocking files
           
        chmod(__DIR__ . '/files/non_readable.csv', 0000);
    }
    
    public function tearDown()
    {
        chmod(__DIR__ . '/files/non_readable.csv', 0444);
    }
    
    /**
     * Omitting filepath constructor argument to 
     * the constructor raises a standard PHP Error
     *
     * @group Constructor Tests
     * @expectedException PHPUnit_Framework_Error
     */
    public function testMissingFilepathInConstructorRaisesError()
    {
        $finder = new DelimiterFinder();        
    }
    
    /**
     * Providing a non-existent filepath as an argument to 
     * the constructor raises an InvalidArgumentException
     *
     * @group Constructor Tests
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The filepath files/path/to/non/existent/file.csv does not exist
     */
    public function testNonExistentFilepathInConstructorThrowsException()
    {
        $filepath = 'files/path/to/non/existent/file.csv';
        $this->assertFalse(is_file($filepath));
        $finder = new DelimiterFinder($filepath);
    }
    
    /**
     * Providing a non-readable filepath as an argument to 
     * the constructor throws an InvalidArgumentException
     *
     * @group Constructor Tests
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The filepath files/non_readable.csv cannot be read
     */
    public function testNonReadableFilepathInConstructorThrowsException()
    {   
        $filepath = 'files/non_readable.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
    }   

    /**
     * @group Constructor Tests
     * @expectedException RuntimeException
     * @expectedExceptionMessage The filepath files/empty.csv is an empty file
     */
    public function testEmptyFileThrowsException()
    {
        $filepath = 'files/empty.csv';
        $this->assertFileExists($filepath);
        $this->assertTrue(filesize($filepath) === 0);
        $finder = new DelimiterFinder($filepath);
    }

    /**
     * @group Constructor Tests
     */
    public function testValidFileInConstructorCreatesDelimiterFinder()
    {    
        $filepath = 'files/valid.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
        $this->assertInstanceOf('DelimiterFinder', $finder);
    }

    /**
     * @group Constructor Tests    
     */
    public function testFileHasAtLeastTwoLines()
    {
        // implement
    }
}