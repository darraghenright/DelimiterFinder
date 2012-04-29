<?php

/**
 * Delimiter Finder
 *
 * Attempt to ascertain the delimiter used in a
 * character separated data file from a defined 
 * list of likely delimiters.
 *
 * @author Darragh Enright <darraghenright@gmail.com>
 */ 
class DelimiterFinder
{
    /**
     * @var string $delimiters
     */
    private $delimiters = array('\t', ',', ';');

    /**
     * @var string $file
     */
    private $file;

    /**
     * @var mixed $match
     */
    private $match = false;
    
    /**
     * Constructor
     *
     * @param string $file Path to the file to read
     */
    public function __construct($file)
    {
        $this->validateFile($file);
        $this->file = $file;
    }
    
    /**
     * Return an array of registered delimiters
     *
     * @return array
     */
    public function getDelimiters()
    {
        return $this->delimiters;
    }
    
    /**
     * Add a new delimiter to the array 
     * of registered delimiters
     *
     * @param  string
     * @throws UnexpectedValueException
     */
    public function addDelimiter($delimiter)
    {
        if (1 !== strlen($delimiter)) {
            throw new UnexpectedValueException(
                sprintf('The delimiter "%s" is not a single character', $delimiter)
            );
        }
    
        if (!in_array($delimiter, $this->delimiters)) {
            $this->delimiters[] = $delimiter;
        }
    }
    
    /**
     * Perform the delimiter search if a match 
     * has not been made and return the result
     *
     * @return mixed
     */
    public function find()
    {
        if (false === $this->match) {
            $this->search();
        } 
        
        return $this->match;
    }
    
    /**
     * Validate the file. Throw an exception if:
     *
     * * The file does not exist
     * * The file is not readable
     *
     * @param  string $file
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function validateFile($file)
    {
        if (!is_file($file)) {
            throw new InvalidArgumentException(
                sprintf('The file "%s" does not exist', $file)
            );
        }
        
        if (!is_readable($file)) {
            throw new RuntimeException(
                sprintf('The file "%s" is not readable', $file)
            );
        }
    }
    
    /**
     * Determine a likely delimiter.
     *
     * Add doc and refactor this code
     */    
    protected function search()
    {
        $handle = fopen($this->file, 'r');
        
        $regex  = sprintf('/[^%s]/', implode($this->delimiters));
        $lines  = array();
        $loops  = 0;
        
        while (!feof($handle)) {
        
            $line  = fgets($handle);
            $chars = preg_replace($regex, null, $line);
            $count = count_chars($chars, 1);
        
            $lines[] = $count;
            
            if ($loops++ > 1) {
                $result = call_user_func_array('array_intersect_assoc', $lines);
                $k = count($result);
            
                if ($k === 1) {
                    $this->match = chr(key($result));
                    break;
                }
            }
        }
        
        fclose($handle);
    }
}
