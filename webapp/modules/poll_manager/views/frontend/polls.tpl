{if !empty($choices|smarty:nodefaults)}
    {arag_block}
        <div style="padding-bottom: 5px; text-direction:{dir}">
            {capture assign=msg}_("%s votes"){/capture}
            {$poll->quiz}{if $vote_before || $show_result} <span style="font-size:10px">({$msg|sprintf:$total_votes})</span>{/if}
        </div>
    {/arag_block}
    {if !$vote_before && !$show_result}
        {arag_validation_errors}
        {arag_form uri="poll_manager/frontend/show/index"}
            {arag_block}
                <div style="padding-bottom: 5px;">
                    {foreach from=$choices item=choice}
                        <div>
                            <label><input type="radio" value="{$choice.id}" name="poll_choice"> {$choice.title}</label>
                        </div>
                    {/foreach}
                </div>
            {/arag_block}
            {arag_block}
                <div style="padding-bottom: 5px;">
                    {arag_captcha}
                </div>
                <div style="padding-bottom: 5px;">
                    <input type="hidden" name="poll_id" value="{$poll->id}" />
                    <input type="submit" name="submit" value={quote}_("Vote"){/quote} />
                </div>
            {/arag_block}
            <div style="padding-bottom: 5px;">
                <div style="float:{left}; font-size: 10px">
                    {capture assign=title}_("Show Result"){/capture}
                    {kohana_helper function="html::anchor" uri="poll_manager/frontend/show/index/`$poll->id`/true" title=$title}
                </div>
                <div style="float:{right}; font-size: 10px">
                    {capture assign=title}_("Archive"){/capture}
                    {kohana_helper function="html::anchor" uri="poll_manager/frontend/show/list/" title=$title}
                </div>
                <div style="clear:both">
                </div>
            </div>
        {/arag_form}
    {else}
        {arag_block}
            <div style="padding-bottom: 5px;">
                {capture assign=msg}_("%s votes"){/capture}
                {foreach from=$choices item=choice}
                    {if $total_votes != 0}
                        {math assign=percentage equation="(x * 100) / z" x=$choice.number z=$total_votes}
                    {else}
                        {assign var=percentage value=0}
                    {/if}
                    <div>
                        <label>{$choice.title} <span style="font-size:10px">({$msg|sprintf:$choice.number}, {$percentage|string_format:"%.2f"}%)</span></label>
                    </div>
                    <div style="width:100%; border: 1px solid #000; height: 10px;">
                        <div style="background-color:{$choice.color}; width:{$percentage}%;height:10px;"></div>
                    </div>
                {/foreach}
            </div>
            <div style="padding-bottom: 5px;">
                {if !$vote_before}
                    <div style="float:{left}; font-size: 10px">
                        {capture assign=title}_("Vote"){/capture}
                        {kohana_helper function="html::anchor" uri="poll_manager/frontend/show/index/`$poll->id`" title=$title}
                    </div>
                {/if}
                <div style="float:{right}; font-size: 10px">
                    {capture assign=title}_("Archive"){/capture}
                    {kohana_helper function="html::anchor" uri="poll_manager/frontend/show/list/" title=$title}
                </div>
                <div style="clear:both">
                </div>
            </div>
        {/arag_block}
    {/if}
{else}
    _("There are no polls avaiable")
{/if}
