#DelimiterFinder

A little utility class that reads a .csv or .dat 
(or whatever) file and tries to guess the delimiter

##Usage

###Create an instance

Include the file and create a new instance in the 
usual way; the constructor takes a filepath as a 
required argument:

    require_once dirname(__FILE__) . '/DelimiterFinder.php';
    
    try {
        $finder = new DelimiterFinder('path/to/data.csv');
    } catch (InvalidArgumentException $e) {
        // file does not exist :o 
    } catch (RuntimeException $e) {
        // file is not readable :(
    }

The object will try to open a file handle on the 
filepath provided. If the file is unreadable or 
non-existent exceptions will be thrown.

###Find

Assuming all went smoothly to this point, 
it's time to try to guess the delimiter.

The class searches for a likely delimiter 
from the following lost of usual suspects: 

* comma (`,`), tab (`\t`) or semicolon (`;`)

Just call the find method:

    $delimiter = $finder->find();
    
If successful, `find()` stores and returns the 
matched delimiter. Subsequent calls to do not 
search the document again; they just return
the stored match.

###If at first you don't succeed...

If a match is not successful, the `find()` method 
returns `false`. Subsequent calls to `find()` will 
search the document again. You can add any number 
of custom delimiters to search, which makes repeat 
searches a little more useful ;)

    $finder->addDelimiter('|');
    $delimiter = $finder->find(); // pipe-delimited file? success!

