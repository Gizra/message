<?php
/**
 * @file
 * message_example.features.inc
 */

/**
 * Implements hook_views_api().
 */
function message_example_views_api() {
  list($module, $api) = func_get_args();
  if ($module == "views" && $api == "views_default") {
    return array("version" => "3.0");
  }
}

/**
 * Implements hook_default_message_type().
 */
function message_example_default_message_type() {
  $items = array();
  $items['example_create_comment'] = entity_import('message_type', '{
    "name" : "example_create_comment",
    "description" : "Example - Create comment",
    "argument_keys" : [
      "@user-url",
      "!user-picture",
      "@user-name",
      "@comment-url",
      "@node-title",
      "!teaser"
    ],
    "category" : "message_type",
    "language" : "en",
    "arguments" : null,
    "data" : { "purge" : { "override" : 0, "enabled" : 0, "quota" : "", "days" : "" } },
    "message_text" : { "und" : [
        {
          "value" : "\\u003ca href=\\"@user-url\\"\\u003e!user-picture\\u003c\\/a\\u003e",
          "format" : "filtered_html",
          "safe_value" : "\\u003cp\\u003e\\u003ca href=\\"@user-url\\"\\u003e!user-picture\\u003c\\/a\\u003e\\u003c\\/p\\u003e\\n"
        },
        {
          "value" : "\\u003ca href=\\"@user-url\\"\\u003e@user-name\\u003c\\/a\\u003e \\u003ca href=\\"@comment-url\\"\\u003ecommented\\u003c\\/a\\u003e on @node-title",
          "format" : "filtered_html",
          "safe_value" : "\\u003cp\\u003e\\u003ca href=\\"@user-url\\"\\u003e@user-name\\u003c\\/a\\u003e \\u003ca href=\\"@comment-url\\"\\u003ecommented\\u003c\\/a\\u003e on @node-title\\u003c\\/p\\u003e\\n"
        },
        {
          "value" : "!teaser",
          "format" : "filtered_html",
          "safe_value" : "\\u003cp\\u003e!teaser\\u003c\\/p\\u003e\\n"
        }
      ]
    },
    "rdf_mapping" : []
  }');
  $items['example_create_node'] = entity_import('message_type', '{
    "name" : "example_create_node",
    "description" : "Example - Create node",
    "argument_keys" : [
      "@user-url",
      "!user-picture",
      "@user-name",
      "@node-url",
      "@node-title",
      "!teaser"
    ],
    "category" : "message_type",
    "language" : "en",
    "arguments" : null,
    "data" : { "purge" : { "override" : 0, "enabled" : 0, "quota" : "", "days" : "" } },
    "message_text" : { "und" : [
        {
          "value" : "\\u003ca href=\\"@user-url\\"\\u003e!user-picture\\u003c\\/a\\u003e",
          "format" : "filtered_html",
          "safe_value" : "\\u003cp\\u003e\\u003ca href=\\"@user-url\\"\\u003e!user-picture\\u003c\\/a\\u003e\\u003c\\/p\\u003e\\n"
        },
        {
          "value" : "\\u003ca href=\\"@user-url\\"\\u003e@user-name\\u003c\\/a\\u003e created \\u003ca href=\\"@node-url\\"\\u003e@node-title\\u003c\\/a\\u003e",
          "format" : "filtered_html",
          "safe_value" : "\\u003cp\\u003e\\u003ca href=\\"@user-url\\"\\u003e@user-name\\u003c\\/a\\u003e created \\u003ca href=\\"@node-url\\"\\u003e@node-title\\u003c\\/a\\u003e\\u003c\\/p\\u003e\\n"
        },
        {
          "value" : "!teaser",
          "format" : "filtered_html",
          "safe_value" : "\\u003cp\\u003e!teaser\\u003c\\/p\\u003e\\n"
        }
      ]
    },
    "rdf_mapping" : []
  }');
  $items['example_user_register'] = entity_import('message_type', '{
    "name" : "example_user_register",
    "description" : "Example - User register",
    "argument_keys" : [ "@user-url", "!user-picture", "@user-name" ],
    "category" : "message_type",
    "language" : "en",
    "arguments" : null,
    "data" : { "purge" : { "override" : 0, "enabled" : 0, "quota" : "", "days" : "" } },
    "message_text" : { "und" : [
        {
          "value" : "\\u003ca href=\\"@user-url\\"\\u003e!user-picture\\u003c\\/a\\u003e",
          "format" : "filtered_html",
          "safe_value" : "\\u003cp\\u003e\\u003ca href=\\"@user-url\\"\\u003e!user-picture\\u003c\\/a\\u003e\\u003c\\/p\\u003e\\n"
        },
        {
          "value" : "\\u003ca href=\\"@user-url\\"\\u003e@user-name\\u003c\\/a\\u003e registered.",
          "format" : "filtered_html",
          "safe_value" : "\\u003cp\\u003e\\u003ca href=\\"@user-url\\"\\u003e@user-name\\u003c\\/a\\u003e registered.\\u003c\\/p\\u003e\\n"
        }
      ]
    },
    "rdf_mapping" : []
  }');
  return $items;
}
