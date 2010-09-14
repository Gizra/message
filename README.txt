// $Id: README.txt,v 1.6 2010/06/07 10:54:11 amitaibu Exp $

Message is an API module. Here is a code example of how to create a message 
instance and assign it to a realm.

/**
 * Implementation of hook_nodeapi().
 *
 * Assuming our message name is "create_content" and it's text is: 
 * "created <a href="@link">@title</a>."
 */
function foo_nodeapi(&$node, $op, $a3 = NULL, $a4 = NULL) {
  global $user;
  if ($op == 'insert') {
    // Set the arguments that would be replaced on run-time.
    $arguments = array(
      // The link will be replaced with the url of the node using url() upon 
      // display. Even if the node alias changes, then the link will always be 
      // displayed correctly.  
      '@link' => array(
        'callback' => 'url', 
        'callback arguments' => array('node/' . $node->nid),
      );
      // The title of the node will be sanitized using check_plain() upon 
      // display.
      '@title' => $node->title,
    );
    
    message_save_message_to_realms('create_content', 'node', $node->nid, $arguments);
  }
}