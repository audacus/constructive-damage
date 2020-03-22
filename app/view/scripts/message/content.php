<?php
$message = $this->getData('message');
?>
<h1><?php echo isset($message['title']) ? $message['title'] : ''; ?></h1>
<p><?php echo isset($message['firstline']) ? $message['firstline'] : ''; ?></p>
<p><?php echo isset($message['secondline']) ? $message['secondline'] : ''; ?></p>
<p><?php echo isset($message['text']) ? $message['text'] : ''; ?></p>
