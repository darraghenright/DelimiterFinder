#DelimiterFinder

A little utility class that reads a .csv or .dat (or whatever) file and tries to guess the delimiter

##Usage

Include the file and create a new instance; the 
constructor takes the filepath as a required argument:

    require_once __DIR__ . '/DelimiterFinder.php';
    
    try {
        $finder = new DelimiterFinder('path/to/data.csv');
    } catch (InvalidArgumentException $e) {
        // file does not exist :o 
    } catch (RuntimeException $e) {
        // file is not readable :(
    }

The object will try to open a file handle on the filepath provided.
If the file is unreadable/non-existent exceptions will be thrown.

Assuming all went smoothly to this point, try to guess the delimiter:

    $delimiter = $finder->find();
    
