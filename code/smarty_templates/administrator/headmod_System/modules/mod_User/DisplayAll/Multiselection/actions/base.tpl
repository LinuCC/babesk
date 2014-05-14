{*
 * To add more actions for multiple selected users, add container and input for
 * it in this file (or include an additional template with the form), and add
 * the action based on that to the as a class into the dir ActionHandlers.
 * The class must extend the abstract class Action.
 * For more details see already existing actions.
 *}

{$path_action_tpl = "$path_smarty_tpl/administrator/headmod_System/modules/mod_User/DisplayAll/Multiselection/actions"}

{include file="$path_action_tpl/user-replace-religion.tpl"}
{include file="$path_action_tpl/user-replace-course.tpl"}