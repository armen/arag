{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

{* assign var=columns value=$plist->getColumns() *}
{if $category->getMessage()}
    {arag_block template="info"}
        {$category->getMessage()|smarty:nodefaults}
    {/arag_block}
{/if}
{arag_validation_errors}
{arag_breadcrumb config=$category->path show_next_steps=true}
{arag_plist name=$category->plistName}
{arag_block}
{arag_block}
    {arag_block template="info"}
        {capture assign="info_msg"}
            _("Fields marked with a %s are required.")
        {/capture}
        {asterisk message=$info_msg}
    {/arag_block}
    {arag_form uri=$category->currentCategoryURI method="post"}
    <table border="0" dir="{dir}">
        <tr>
            <td style="color:green;">_("Edit current category"):</td>
        </tr>
        <tr>
            {if !$name_edit|smarty:nodefaults|default:null}{assign var=name_edit value=$category->currentCategory.name}{/if}
            {if !$label_edit|smarty:nodefaults|default:null}{assign var=label_edit value=$category->currentCategory.label}{/if}
            {if $category->currentCategory.parent|smarty:nodefaults}
            <td>{asterisk}_("Name"):&nbsp;<input type="text" name="name_edit" value="{$name_edit|smarty:nodefaults|default:''}" /></td>
            {/if}
            <td>{asterisk}_("Label"):&nbsp;<input type="text" name="label_edit" value="{$label_edit|smarty:nodefaults|default:''}" /></td>
            <td>
                <input type="hidden" name="action" value="edit" />
                <input type="submit" value={quote}_("Edit"){/quote} />
            </td>
        </tr>
    </table>
    {/arag_form}
    {arag_form uri=$category->currentCategoryURI method="post"}
    <table border="0" dir="{dir}">
        <tr>
            <td style="color:green;">_("Or add new category inside it"):</td>
        </tr>
        <tr>
            <td>{asterisk}_("Name"):&nbsp;<input type="text" name="name_add" value="{$label_add|smarty:nodefaults|default:''}" /></td>
            <td>{asterisk}_("Label"):&nbsp;<input type="text" name="label_add" value="{$label_add|smarty:nodefaults|default:''}" /></td>
            <td>
                <input type="hidden" name="action" value="add" />
                <input type="submit" value={quote}_("Add"){/quote} />
            </td>
        </tr>
    </table>
    {/arag_form}
{/arag_block}
{/arag_block}
