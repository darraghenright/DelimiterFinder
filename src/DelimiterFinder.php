<?php

class DelimiterFinder
{
    public function __construct($file)
    {
        // TODO: refactor into validator method(s) once unit tested
        
        if (!is_file($file)) {
            throw new InvalidArgumentException(sprintf(
                'The filepath %s does not exist', $file
            ));
        }
        
        if (!is_readable($file)) {
            throw new InvalidArgumentException(sprintf(
                'The filepath %s cannot be read', $file
            ));
        }
        
        if (0 === filesize($file)) {
            throw new RuntimeException(sprintf(
                'The filepath %s is an empty file', $file
            ));
        }
    }
}
