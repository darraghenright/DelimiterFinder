<?php


require_once 'PHPUnit/Autoload.php';
require_once 'vfsStream/vfsStream.php';

require_once __DIR__ . '/../src/DelimiterFinder.php';

class DelimiterFinderTest extends PHPUnit_Framework_TestCase
{    
    // test with different line endings.... dreaded mac excel CR line endings
    // ini_set('auto_detect_line_endings');
    // mock data files!
    
    /**
     * Setup mock filesystem elements
     */
    public function setUp()
    {           
        $root = vfsStream::newDirectory('files');
        
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot($root);       
        
        $root->addChild(vfsStream::newFile('non_readable_file.csv', 0000));
        $root->addChild(vfsStream::newFile('readable_file.csv', 0444));
        
        /*
           
        $root->addChild(vfsStream::newFile('writeable.csv', 0666));
        
        $file = vfsStream::url('files/writeable.csv');
        
        file_put_contents($file, 'hi');
            
        $a = file($file);
        var_dump($a);
        exit();
        */
    }
    
    /**
     * Omitting a filepath as an argument to 
     * the constructor raises a PHP Error
     *
     * @group                       Constructor
     * @expectedException           PHPUnit_Framework_Error
     */
    public function testCreateObjectWithMissingArgumentShouldRaiseError()
    {
        $finder = new DelimiterFinder();        
    }
    
    /**
     * Providing a non-existent file as an argument to 
     * the constructor throws an InvalidArgumentException
     *
     * @group                       Constructor
     * @expectedException           InvalidArgumentException
     * @expectedExceptionMessage    The file "vfs://files/non_existent_file.csv" does not exist
     */
    public function testCreateObjectWithNonExistentFileShouldRaiseException()
    {
        $file = vfsStream::url('files/non_existent_file.csv');
        $this->assertFalse(is_file($file));
        $finder = new DelimiterFinder($file);
    }
    
    /**
     * Providing a non-readable file as an argument 
     * to the constructor throws a RuntimeException
     *
     * @group                       Constructor
     * @expectedException           RuntimeException
     * @expectedExceptionMessage    The file "vfs://files/non_readable_file.csv" is not readable
     */
    public function testCreateObjectWithNonReadableFileShouldRaiseException()
    {      
        $finder = new DelimiterFinder(vfsStream::url('files/non_readable_file.csv'));
    }   
        
    /**
     * Providing a readable file as an argument 
     * to the constructor creates an instance
     *
     * @group                       Constructor
     */
    public function testObjectCreateWithReadableFileShouldPass()
    {    
        $finder = new DelimiterFinder(vfsStream::url('files/readable_file.csv'));
        $this->assertInstanceOf('DelimiterFinder', $finder);
    }

    /** 
     * Adding a single character to the list 
     * of registered delimiters should pass
     * 
     * @group                       Add Delimiter
     */
    public function testAddDelimiterWithSingleCharacterShouldPass()
    {
        $file = vfsStream::url('files/readable_file.csv');
        $finder = new DelimiterFinder($file);
        $finder->addDelimiter('|');
        $this->assertContains('|', $finder->getDelimiters());
    }
    
    /**
     * Adding an empty string to the list of registered 
     * delimiters should throw an UnexpectedValueException
     * 
     * @group                       Add Delimiter
     * @expectedException           UnexpectedValueException
     * @expectedExceptionMessage    The delimiter "" is not a single character
     */

    public function testAddDelimiterWithZeroLengthStringShouldRaiseException()
    {
        $file = vfsStream::url('files/readable_file.csv');
        $finder = new DelimiterFinder($file);
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
    public function testAddDelimiterWithStringLongerThanOneCharacterShouldRaiseException()
    {
        $file = vfsStream::url('files/readable_file.csv');
        $finder = new DelimiterFinder($file);
        $finder->addDelimiter('--');
    }
    
    /**
     * Find and return comma for comma-delimited file
     *
     * @group Find
     */
    public function testFindForCommaDelimitedFileShouldReturnComma()
    {
        $filepath = 'files/delim_comma.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
        $this->assertEquals($finder->find(), ',');
    }

    /**
     * Find and return tab for tab-delimited file
     *
     * @group Find
     */
    public function testFindForTabDelimitedFileShouldReturnTab()
    {
        $filepath = 'files/delim_tab.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
        $this->assertEquals($finder->find(), "\t");
    }

    /**
     * Find and return semicolon for semicolon-delimited file
     *
     * @group Find
     */
    public function testFindForSemicolonDelimitedFileShouldReturnSemicolon()
    {        
        $filepath = 'files/delim_semicolon.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
        $this->assertEquals($finder->find(), ';');
    }

    /**
     * Find and return pipe for pipe-delimited file    
     *
     * @group Find
     * @group Add Delimiter
     */
    public function testFindForPipeDelimitedFileShouldReturnPipe()
    {        
        $filepath = 'files/delim_custom.csv';
        $this->assertFileExists($filepath);
        $finder = new DelimiterFinder($filepath);
        $finder->addDelimiter('|');
        $this->assertEquals($finder->find(), '|');
    }    
}
