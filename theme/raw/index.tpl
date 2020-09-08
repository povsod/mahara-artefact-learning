{include file="header.tpl"}
<div class="btn-group btn-group-top">
    <a class="btn btn-secondary" href="{$WWWROOT}artefact/learning/edit.php">
        <span class="icon icon-plus icon-lg left" role="presentation" aria-hidden="true"></span>
        {str section="artefact.learning" tag="newlearning"}</a>
</div>
{if !$learning.data}
    <div class="no-results">{$strnolearningaddone|safe}</div>
{else}
    <div id="planswrap" class="view-container">
        <div id="planslist">
            {$learning.tablerows|safe}
        </div>
       {$learning.pagination|safe}
    </div>
{/if}
{include file="footer.tpl"}
