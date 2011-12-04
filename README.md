Crocos\Notifier
================

Simple notifier for PHP.


Supported output:

* Tiarra socket


Example
---------

```php:
<?php

require_once dirname(__DIR__) . '/vendor/SplClassLoader.php';
$cl = new SplClassLoader('Crocos', dirname(__DIR__) . '/src');
$cl->register();

$config = array(
    'cache' => array(
        'driver' => 'Memcached',
        'options' => array(
            'host' => 'localhost',
            'port' => 11211,
        ),
    ),
    'notify' => array(
        'Tiarra' => array(
            'socket'  => 'crocosbot',
            'channel' => '#bot@crocos',
            'use_notice' => true,
        ),
    ),
);

$notifier = new Notifier($config);

$x = simplexml_load_file('http://url-for-redmine/activity.atom?key=api-key-xxxxxxxxx&show_documents=1&show_files=1&show_issues=1&show_news=1&show_wiki_edits=1');
foreach ($x->entry as $entry) {
    $link = (string)$entry->id;

    if (preg_match('/バグ/', $entry->title) && !preg_match('/\(終了\)/', $entry->title)) {
        $msg = sprintf("Redmine update: \x034%s\x03 (by %s) %s \x0315[%s]\x03", $entry->title, $entry->author->name, $link, $entry->updated);
    } else if (preg_match('/機能/', $entry->title)) {
        $msg = sprintf("Redmine update: \x033%s\x03 (by %s) %s \x0315[%s]\x03", $entry->title, $entry->author->name, $link, $entry->updated);
    } else if (preg_match('/(タスク|作業)/', $entry->title)) {
        $msg = sprintf("Redmine update: \x0310%s\x03 (by %s) %s \x0315[%s]\x03", $entry->title, $entry->author->name, $link, $entry->updated);
    } else {
        $msg = sprintf("Redmine update: %s (by %s) %s \x0315[%s]", $entry->title, $entry->author->name, $link, $entry->updated);
    }

    $notifier->message($link, $msg, $use_cache = true);
}
```

License
---------

    The BSD License

    Copyright (c) 2011, Sotaro Karasawa, Crocos, Inc.
    All rights reserved.

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions
    are met:

    - Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    - Redistributions in binary form must reproduce the above
      copyright notice, this list of conditions and the following
      disclaimer in the documentation and/or other materials provided
      with the distribution.
    - Neither the name of the author nor the names of its contributors
      may be used to endorse or promote products derived from this
      software without specific prior written permission.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
    "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
    LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
    A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
    OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
    SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
    LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
    DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
    THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
    (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
    OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

