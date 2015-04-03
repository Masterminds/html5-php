From 2.x to 3.x
===============

- The class `Masterminds\HTML5` has been renamed into `Masterminds\Html5`

Before:
```php    
use Masterminds\HTML5;

$html5 = new HTML5();
```
        
After:
```php  
use Masterminds\Html5;
        
$html5 = new Html5();
```        

From 1.x to 2.x
=================

- All classes uses `Masterminds` namespace.
- All public static methods has been removed from `HTML5` class and the general API to access the HTML5 functionalities has changed. 

    Before:
    
        $dom = \HTML5::loadHTML('<html>....');
        \HTML5::saveHTML($dom);
        
    After:

        use Masterminds\HTML5;
        
        $html5 = new HTML5();
        
        $dom = $html5->loadHTML('<html>....');
        echo $html5->saveHTML($dom);


