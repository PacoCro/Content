{if $tabType == 4}
    {*
    {formtabbedpanelset
    *}
{else}
<div class="row margin-bottom-20">
    <div class="col-md-12">
        <div{if !empty($tabStyle)} class="{$tabStyle}"{/if} role="tabpanel">
            {if $tabType == 1}
            <ul class="nav nav-tabs" role="tablist">
            {elseif $tabType == 2}
            <ul class="nav nav-pills" role="tablist">
            {elseif $tabType == 3}
            <div class="col-sm-3">
            <ul class="nav nav-pills nav-stacked" role="tablist">
            {/if}
            <!-- Nav tabs -->
            {foreach from=$itemsToTab item='itemToTab' name='itemToTab'}
                <li role="presentation"{if $smarty.foreach.itemToTab.first} class="active"{/if}><a href="#{$itemToTab.link}" aria-controls="{$itemToTab.link}" role="tab" data-toggle="tab">{$itemToTab.title}</a></li>
            {/foreach}
            </ul>
            {if $tabType == 3}
            </div>
            {/if}

            <!-- Tab panes -->
            {if $tabType == 3}
            <div class="col-sm-9">
            {/if}
            <div class="tab-content">
            {foreach from=$itemsToTab item='itemToTab' name='itemToTab'}
                <div role="tabpanel" class="tab-pane fade in{if $smarty.foreach.itemToTab.first} active{/if}" id="{$itemToTab.link}">{$itemToTab.display}</div>
            {/foreach}
            </div>
            {if $tabType == 3}
            </div>
            {/if}
        </div>
    </div>
</div>
{/if} {* tabType 1 *}
