// $Id$

Message is an API module. Here is a code example of how to create a message 
instance and assign it to a realm.

/**
 * Implementation of hook_nodeapi().
 */
function foo_nodeapi(&$node, $op, $a3 = NULL, $a4 = NULL) {
  global $user;
  if ($op == 'insert') {
    $message = message_load('bar'); // Replace 'bar' by your message name.

    $arguments = array(
      '@link' => array(
        'callback' => 'url',
        'callback arguments' => array($node->nid),
      ),
    );

    // Create message instance.
    $message_instance = new stdClass();
    $message_instance->name = $message->name;
    $message_instance->uid = $user->uid;
    $message_instance->entity_type = 'node';
    $message_instance->eid = $node->nid;
    $message_instance->arguments = $arguments;

    $message_instance = message_instance_save($message_instance);

    // Save to user realm.
    $realm = new stdClass();
    $realm->iid = $message_instance->iid;
    $realm->realm = 'user';
    $realm->realm_id = $user->uid;
    message_realm_save($realm);
  }
}