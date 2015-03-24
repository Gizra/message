<?php

$message = message_create('example_arguments', array('uid' => $this->user->uid));
$message->save();
