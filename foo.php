<?php

$message = message_create('example_arguments', array('uid' => 1));
$message->save();

$message = message_create('foo', array());
$message->save();

$message = message_create('example_arguments', array('uid' => 1));
$message->save();
