<?php

require_once __DIR__ . '/../src/DelimiterFinder.php';
require_once 'PHPUnit/Autoload.php';

class DelimiterFinderTest extends PHPUnit_Framework_TestCase
{    
    // find should match delimiter for files.. tab, comma, semicolon etc.
    // find should match delimiter for custom delim
    // find should return false for non match... files with no pattern, files with one line etc.
    // find should throw exception for above if exception flag is set
    // test with different line endings.... dreaded mac excel CR line endings
    //    ini_set('auto_detect_line_endings');
    // replace files with file mocking
    // use setup and teardown to implement mock files
    
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
     * @group                       Constructor
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
     * @group                       Constructor
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
     * @group                       Constructor
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
     * @group                       Constructor
     * @expectedException           RuntimeException
     * @expectedExceptionMessage    The file files/empty.csv is empty
     */
    public function testCreateObjectWithEmptyFile()
    {
        $filepath = 'files/empty.csv';
        $this->assertFileExists($filepath);
        $this->assertTrue(0 === filesize($filepath));
        $finder = new DelimiterFinder($filepath);
    }
        
    /**
     * Providing a valid filepath as an argument 
     * to the constructor creates an instance
     *
     * @group                       Constructor
     */
    public function testObjectCreateWithValidFile()
    {    
        $filepath = 'files/not_empty.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
        $this->assertInstanceOf('DelimiterFinder', $finder);
    }

    /** 
     * Adding a single character to the list 
     * of registered delimiters should pass
     * 
     * @group                       Add Delimiter
     */
    public function testAddDelimiterWithSingleCharacter()
    {
        
        $filepath = 'files/not_empty.csv';
        $this->assertFileExists($filepath);
        
        $delimiter = '|';
        $finder = new DelimiterFinder($filepath);
        $finder->addDelimiter($delimiter);
        $this->assertContains($delimiter, $finder->getDelimiters());
    }
    
    /**
     * Adding an empty string to the list of registered 
     * delimiters should throw an UnexpectedValueException
     * 
     * @group                       Add Delimiter
     * @expectedException           UnexpectedValueException
     * @expectedExceptionMessage    The delimiter "" is not a single character
     */

    public function testAddDelimiterWithZeroLengthString()
    {
        $filepath = 'files/not_empty.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
        $finder->addDelimiter(''); 
    }

    /**
     * Adding a string longer than one character to the list of 
     * registered delimiters should throw an UnexpectedValueException
     *
     * @group                       Add Delimiter
     * @expectedException           UnexpectedValueException
     * @expectedExceptionMessage    The delimiter "--" is not a single character
     */

    public function testAddDelimiterWithStringsLongerThanOneCharacter()
    {
        $filepath = 'files/not_empty.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
        $finder->addDelimiter('--'); 
    }
    
    /**
     * Find comma
     *
     * @group                       Find
     */
    public function testFinderShouldReturnComma()
    {
        $filepath = 'files/delim_comma.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
        $this->assertEquals($finder->find(), ',');
    }

    /**
     * Find tab
     *
     * @group                       Find
     */
    
    public function testFinderShouldReturnTab()
    {
        $filepath = 'files/delim_tab.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
        $this->assertEquals($finder->find(), "\t");
    }

    /**
     * Find semicolon
     *
     * @group                       Find
     */
    public function testFinderShouldReturnSemicolon()
    {        
        $filepath = 'files/delim_semicolon.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
        $this->assertEquals($finder->find(), ';');
    }

    /**
     * Find custom
     *
     * @group                       Find
     */
    public function testFinderShouldReturnPipe()
    {        
        $filepath = 'files/delim_custom.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
        $finder->addDelimiter('|');
        $this->assertEquals($finder->find(), '|');
    }    
}
