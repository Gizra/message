<?php

/**
 * Implementation of hook_default_message_type().
 */
function message_example_default_message_type() {
  dpm('fooo');
  $items = array();
  $items['create_content'] = entity_import('message_type', '{
    "name" : "create_content",
    "description" : "Create content",
    "statusKey" : "status",
    "message_text" : { "und" : [
        {
          "value" : "created \\u003ca href=\\"@url\\"\\u003e@title\\u003c\\/a\\u003e",
          "format" : null,
          "safe_value" : "created \\u0026lt;a href=\\u0026quot;@url\\u0026quot;\\u0026gt;@title\\u0026lt;\\/a\\u0026gt;"
        }
      ]
    },
    "rdf_mapping" : []
  }');
  return $items;
}
