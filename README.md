# scss php builder

A ready to go server side scss builder. Based on the last time of filechange of your scss files, the script uses awesome [scssphp](https://github.com/scssphp/scssphp) to build your css code.

More or less its just a wrapper for scssphp.

## Install

Use composer to install the script:

```composer require basteyy/scss-php-builder```

## Usage

```php
<?php
// Get composers autoloader
require __DIR__ . '/vendor/autoload.php';

// Construct the class
$scss = new \basteyy\ScssPhpBuilder\ScssPhpBuilder();

// Add the input folder
$scss->addFolder(__DIR__.'/scss');

// Define the output file
$scss->addOutputeFile(__DIR__ . '/css/style.css');

// Define the scss starting point
$scss->addStartingFile(__DIR__ . '/scss/style.scss');

// Than compile the scss source
$scss->compileToOutputfile();
 ```

## More examples

### Expand the compiled source

Instead of expand the compiled css, you should use SourceMaps. Anyway, for expand the compiled:

```php
$scss->setOutputExpanded();
 ```

### Use SourceMap
```php
<?php
// Get composers autoloader
require __DIR__ . '/vendor/autoload.php';

// Construct the class
$scss = new \basteyy\ScssPhpBuilder\ScssPhpBuilder();

// Add the input folder
$scss->addFolder(__DIR__.'/scss');

// Define the output file
$scss->addOutputeFile(__DIR__ . '/css/style.css');

// Define the scss starting point
$scss->addStartingFile(__DIR__ . '/scss/style.scss');

// If you like, you can acticate sourcemap by setup the remote url
$scss->setSourcemapFolderUrl('/css/');

// Than compile the scss source
$scss->compileToOutputfile();
 ```
 
 Keep in mind, that you can the option, which is passed to the __construct will overwrite/control the sourcemap settings (see [__construct Options](https://github.com/basteyy/scss-php-builder#__construct-options))


### Force recompile
```php
$scss->compileToOutputfile(true);
 ```

### Compile to string
```php
$scss->getCompiledCode();
 ```

### Force recompile to string
```php
$scss->getCompiledCode(true);
 ```
 
### __construct Options

You can pass a few options to the __construct:
```php
// Construct the class
$scss = new \basteyy\ScssPhpBuilder\ScssPhpBuilder([
	'auto_create_folders' => true, 
	'auto_create_files' => true, 
	'write_source_map' => true
]);
```

_auto_create_folders_ if true, will create all folders
_auto_create_files_ if true, will create all foles
_write_source_map_ if true, will create the source map

## License

The MIT License (MIT). Please see [License File](https://github.com/basteyy/scss-php-builder/blob/master/LICENSE) for more information.
