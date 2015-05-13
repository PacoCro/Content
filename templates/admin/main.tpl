{ajaxheader modname='Content' filename='ajax.js'}

{* include jstree *}
{pageaddvar name="stylesheet" value="modules/Content/lib/vendor/jstree/dist/themes/default/style.min.css"}
{pageaddvar name="javascript" value="jquery"}
{*pageaddvar name="javascript" value="modules/Content/lib/vendor/jstree/dist/libs/jquery.js"*}
{pageaddvar name="javascript" value="modules/Content/lib/vendor/jstree/dist/jstree.min.js"}

{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="view" size="small"}
    <h3>{gt text="Page list and content structure"}</h3>
</div>

{form cssClass='z-form'}

<p>{gt text="This is where you manage all your pages. You can add/edit/delete/translate pages (depending on permission rights) as well as drag them around to get the content structure right."}</p>
{include file='admin/tocedit.tpl'}

{if $access.pageEditAllowed}
<p class="z-sub">{gt text="Click on the arrow right of a page title for the options of that page. To drag and drop a page in the hierachy you select the move icon left of the title and drop it on the title of another page to form a subpage. A page with sub-pages can be expanded or contracted by clicking on the plus or minus left of the page title. To activate/deactive pages or make pages (not) available in the menu click on the led icons."}</p>
{/if}

<script type="text/javascript">
{{foreach from=$pages item=page}}
  content.addDraggablePage({{$page.id}});
{{/foreach}}
</script>

<div>
    <input type="hidden" name="contentTocDragSrcId" id="contentTocDragSrcId"/>
    <input type="hidden" name="contentTocDragDstId" id="contentTocDragDstId"/>
</div>

{formpostbackfunction function="content.pageListDrag" commandName='pageDrop'}

{/form}
{adminfooter}
