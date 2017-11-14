# F1024 Framework
Another one simple MVC framework.
## Requirements
The minimum requirement is web server supporting PHP 5.5

## Installation

* [Install composer](https://getcomposer.org/download/) (if for some reason you haven't done so already)
* ```composer require koz1024/f1024```
* ```php -r "readfile('https://koz1024.net/framework-installer');" > finstaller && php -f finstaller```

## Configuring

We automatically generate config file for you. But, it is more than likely, you want to change something there
Your config file has *sections*:

**Global section**

* ```prettyurl = false | db``` if *db* router will use database table *url* for user-friendly routing
* ```disableSegmentRouting = false | true``` if *false* router will try to parse URI by segments (like www.example.com/controllername/actionname/other/params/here)
* ```disableRestRouting = false | true``` if *false* router will try to parse request method and URI by segments. Needs for REST applications
* ```rules``` associative array where key regular expression matching to route and value is array of (see next *routing*)
* ```routing``` associative array where key is matched route and value is array of *controller* (controller name with namespace), *action* and optional *params* (will be passed to action), *status* (HTTP status code) and *status_text* (additional HTTP headers). Example: 
```
'routing' => [
    '/test' => [
        'controller' => '\\controllers\\MainController',
        'action'     => 'test',
        //next items are optional:
        'params'     => ['foo', 'bar'],
        'status'     => '302',
        'status_text' => 'Location: /some',
    ],
]
```

**Database section**
* ```type``` please use value **"mysql_pdo"**
* ```connection``` array of values *dsn*, *server*, *user*, *password* and *name* (of database)

**Cache section**
* ```type = redis | memcache``` 
* ```settings``` storage settings array

## Using Framework

First of all, you should make changes to your config file (at least, database credentials). Then you can enjoy using all advantages of this framework.
More detailed documentation will be written later.