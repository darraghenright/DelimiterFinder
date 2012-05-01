<?php


require_once 'PHPUnit/Autoload.php';
require_once 'vfsStream/vfsStream.php';

require_once __DIR__ . '/../src/DelimiterFinder.php';

class DelimiterFinderTest extends PHPUnit_Framework_TestCase
{    
    /**
     * Setup mock filesystem elements
     */
    public function setUp()
    {   
        // test with different line endings.... dreaded mac excel CR line endings
        // ini_set('auto_detect_line_endings');
        
        $root = vfsStream::newDirectory('files');
        
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot($root);
        
        // create some files
        $root->addChild(vfsStream::newFile('non_readable_file.csv', 0000));
        $root->addChild(vfsStream::newFile('readable_file.csv', 0444));           
        $root->addChild(vfsStream::newFile('delimited_comma.csv', 0666));
        $root->addChild(vfsStream::newFile('delimited_pipe.csv', 0666));
        $root->addChild(vfsStream::newFile('delimited_semicolon.csv', 0666));
        $root->addChild(vfsStream::newFile('delimited_tabbed.csv', 0666));
        
        // populate some files
        $comma = <<<EOF
Leonardo,blue,Katana
Raphael,red,sai
Michelangelo,orange,nunchaku
Donatello,purple,bō staff
EOF;

        $pipe = <<<EOF
Leonardo|blue|Katana
Raphael|red|sai
Michelangelo|orange|nunchaku
Donatello|purple|bō staff
EOF;

        $semicolon = <<<EOF
Leonardo;blue;Katana
Raphael;red;sai
Michelangelo;orange;nunchaku
Donatello;purple;bō staff
EOF;
        
        $tabbed = <<<EOF
Leonardo\tblue\tKatana
Raphael\tred\tsai
Michelangelo\torange\tnunchaku
Donatello\tpurple\tbō staff
EOF;

        file_put_contents(vfsStream::url('files/delimited_comma.csv'), $comma);
        file_put_contents(vfsStream::url('files/delimited_pipe.csv'), $pipe);
        file_put_contents(vfsStream::url('files/delimited_semicolon.csv'), $semicolon);
        file_put_contents(vfsStream::url('files/delimited_tabbed.csv'), $tabbed);        
    }
    
    /**
     * Omitting a filepath as an argument to 
     * the constructor triggers a PHP Error
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
        $finder = new DelimiterFinder(vfsStream::url('files/readable_file.csv'));
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
        $finder = new DelimiterFinder(vfsStream::url('files/readable_file.csv'));
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
        $finder = new DelimiterFinder(vfsStream::url('files/readable_file.csv'));
        $finder->addDelimiter('--');
    }
    
    /**
     * Find and return comma for comma-delimited file
     *
     * @group Find
     */
    public function testFindForCommaDelimitedFileShouldReturnComma()
    {
        $finder = new DelimiterFinder(vfsStream::url('files/delimited_comma.csv'));        
        $this->assertEquals($finder->find(), ',');
    }

    /**
     * Find and return tab for tab-delimited file
     *
     * @group Find
     */
    public function testFindForTabDelimitedFileShouldReturnTab()
    {
        $finder = new DelimiterFinder(vfsStream::url('files/delimited_tabbed.csv'));
        $this->assertEquals($finder->find(), "\t");
    }

    /**
     * Find and return semicolon for semicolon-delimited file
     *
     * @group Find
     */
    public function testFindForSemicolonDelimitedFileShouldReturnSemicolon()
    {        
        $finder = new DelimiterFinder(vfsStream::url('files/delimited_semicolon.csv'));
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
        $finder = new DelimiterFinder(vfsStream::url('files/delimited_pipe.csv'));
        $finder->addDelimiter('|');
        $this->assertEquals($finder->find(), '|');
    }    
}
