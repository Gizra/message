
A general logging utility that can be used as (yet another) activity module.

The main differences between message module and activity module are:

* In message module, the arguments of a sentence aren't hard-coded. This means 
  that the rendering time is slower than activity, on the other hand you can use 
  callback functions to render the final output (see message_example module).
* Thanks to the dependency on CTools, the messages are exportable (Features 
  integration is already in place).
* Message integrates with i18n, so you can translate your messages (enable 
  i18strings module).
* Message can use (but not as a dependency) the Rules module, to create message 
  instances.
* Messages are assigned to realms. So if for example a message is assigned to an 
  organic group realm, even users that join that group later on, may have access 
  to those messages.
