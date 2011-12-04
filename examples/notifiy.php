<?php
/**
 *
 */

set_include_path(dirname(__DIR__) . '/src' . PATH_SEPARATOR . get_include_path());
require_once dirname(__DIR__) . '/vendor/SplClassLoader.php';

$cl = new SplClassLoader('Crocos', dirname(__DIR__) . '/src');
$cl->register();

use Crocos\Notifier\Notifier;

$config = array(
    'log_level' => Notifier::LOG_DEBUG,
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

