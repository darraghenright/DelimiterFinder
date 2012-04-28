<?php

class DelimiterFinder
{
    public function __construct($file)
    {
        // TODO: refactor into validator method(s) once unit tested
        
        if (!is_file($file)) {
            throw new InvalidArgumentException(sprintf(
                'The file %s does not exist', $file
            ));
        }
        
        if (!is_readable($file)) {
            throw new InvalidArgumentException(sprintf(
                'The file %s cannot be read', $file
            ));
        }
        
        if (0 === filesize($file)) {
            throw new RuntimeException(sprintf(
                'The file %s is empty', $file
            ));
        }
    }
}
