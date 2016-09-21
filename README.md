# Tools for work with filesystem

## FSTraverser
It is the class for recursive traverse your directories and
apply callback to each found file.

Usage example:

```php
$tr = new FSTraverser(
    // root dir
    '/home/user/lol',
    // callback
    function($path, $entry) {
        echo $entry, PHP_EOL;
    }
);
$tr->setExcludeExtensions(['php', 'js']);

$tr->go();

```
In this example, we print all files, that are not php or js.

Or:

```php
$tr = new FSTraverser(
    // root dir
    '/home/user/lol',
    // callback
    function($path, $entry) {
        echo $entry, PHP_EOL;
    },
    // exclude nodes
    ['.git', 'README.md'],
    // exclude extensions (have no point in this case, because allowed extensions are setted)
    ['zip', 'gz'],
    // allowed extensions (process only files with this extension)
    ['js', 'twig'],
    // maximal depth
    5
);

$tr->go();
```
In this example, we traverse `lol` directory, avoiding `.git` dir and `README.md`
file, process only js and twig files, and not traverse all nodes, deeper than 5
level.
