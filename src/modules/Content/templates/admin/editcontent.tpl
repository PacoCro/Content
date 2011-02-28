{include file="admin/menu.tpl"}

<div class="z-admincontainer z-clearfix">
    <div class="z-adminpageicon">{icon type="view" size="large"}</div>
    <h2>{gt text="Edit content item"}</h2>
    <h3>{gt text="Edit %s" tag1=$contentType.title}</h3>
    
    <p class="content-page-help">{$contentType.description}</p>

    {formerrormessage id='error'}

    {form enctype="multipart/form-data" cssClass='z-form'} {* Always allow plugins to use upload *}

    {contentformframe}
    {formtabbedpanelset}

    {formtabbedpanel __title='Content'}
    <fieldset>
        <legend>{$contentType.title} [{gt text="ID"}{$content.id}]</legend>
        {include file=$contentTypeTemplate}
        <div class="z-formrow">
            {formlabel for='active' __text='Active'}
            {formcheckbox id='active' checked=$content.active}
        </div>
        <div class="z-formrow">
            {formlabel for='visiblefor' __text='Visible for'}
            {formdropdownlist id='visiblefor' items=$visiblefors group='content'}
        </div>
    </fieldset>
    {/formtabbedpanel}

    {formtabbedpanel __title='Layout'}
    <fieldset>
        <legend>{gt text="Layout"}</legend>
        {contentlabelhelp __text='Positioning works only with fixed width!'}
        <div class="z-formrow">
            {formlabel for='stylePosition' __text='Position'}
            {contentpositionselector id='stylePosition' group='content'}
        </div>
        <div class="z-formrow">
            {formlabel for='styleWidth' __text='Width'}
            {contentwidthselector id='styleWidth' group='content'}
        </div>
        <div class="z-formrow">
            {formlabel for='styleClass' __text='Styling'}
            {contentclassselector id='styleClass' group='content'}
        </div>
    </fieldset>
    {/formtabbedpanel}

    {/formtabbedpanelset}

    <div class="z-buttons">
        {formbutton class="z-bt-save z-btgreen" commandName="save" __text="Save"}
        {if $access.pageDeleteAllowed}
        {formbutton class="z-bt-delete z-btred" commandName="delete" __text="Delete" __confirmMessage='Delete'}
        {/if}
        {if $content.isTranslatable && $multilingual==1}
        {formbutton class="z-bt-icon con-bt-translate" commandName="translate" __text="Translate"}
        {/if}
        {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
    </div>

    {/contentformframe}

    {/form}
</div>