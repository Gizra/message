<?php
/**
 * @file
 * message_og_example.features.inc
 */

/**
 * Implements hook_views_api().
 */
function message_og_example_views_api() {
  list($module, $api) = func_get_args();
  if ($module == "views" && $api == "views_default") {
    return array("version" => "3.0");
  }
}

/**
 * Implements hook_default_message_type().
 */
function message_og_example_default_message_type() {
  $items = array();
  $items['example_og_post_in_group'] = entity_import('message_type', '{
    "name" : "example_og_post_in_group",
    "description" : "Example - OG post in group",
    "argument_keys" : [ "!teaser", "!groups" ],
    "category" : "message_type",
    "language" : "en",
    "arguments" : null,
    "data" : { "purge" : { "override" : 0, "enabled" : 0, "quota" : "", "days" : "" } },
    "message_text" : { "und" : [
        {
          "value" : "[message:user:picture]",
          "format" : "filtered_html",
          "safe_value" : "\\u003cp\\u003e[message:user:picture]\\u003c\\/p\\u003e\\n"
        },
        {
          "value" : "\\u003ca href=\\"[message:user:url]\\"\\u003e[message:user:name]\\u003c\\/a\\u003e posted \\u003ca href=\\"[message:field-node-ref:url]\\"\\u003e[message:field-node-ref:title]\\u003c\\/a\\u003e in !groups.",
          "format" : "full_html",
          "safe_value" : "\\u003cp\\u003e\\u003ca href=\\"[message:user:url]\\"\\u003e[message:user:name]\\u003c\\/a\\u003e posted \\u003ca href=\\"[message:field-node-ref:url]\\"\\u003e[message:field-node-ref:title]\\u003c\\/a\\u003e in !groups.\\u003c\\/p\\u003e\\n"
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
  $items['example_og_user_membership'] = entity_import('message_type', '{
    "name" : "example_og_user_membership",
    "description" : "Example - OG user membership",
    "argument_keys" : [],
    "category" : "message_type",
    "language" : "en",
    "arguments" : null,
    "data" : { "purge" : { "override" : 0, "enabled" : 0, "quota" : "", "days" : "" } },
    "message_text" : { "und" : [
        {
          "value" : "[message:user:picture]",
          "format" : "filtered_html",
          "safe_value" : "\\u003cp\\u003e[message:user:picture]\\u003c\\/p\\u003e\\n"
        },
        {
          "value" : "\\u003ca href=\\"[message:user:url]\\"\\u003e[message:user:name]\\u003c\\/a\\u003e joined \\u003ca href=\\"[message:field-node-groups-ref:0:url]\\"\\u003e[message:field-node-groups-ref:0:title]\\u003c\\/a\\u003e group.",
          "format" : "full_html",
          "safe_value" : "\\u003cp\\u003e\\u003ca href=\\"[message:user:url]\\"\\u003e[message:user:name]\\u003c\\/a\\u003e joined \\u003ca href=\\"[message:field-node-groups-ref:0:url]\\"\\u003e[message:field-node-groups-ref:0:title]\\u003c\\/a\\u003e group.\\u003c\\/p\\u003e\\n"
        }
      ]
    },
    "rdf_mapping" : []
  }');
  return $items;
}
