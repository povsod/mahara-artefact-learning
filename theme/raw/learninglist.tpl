<div class="list-group list-group-lite">
{foreach from=$learning.data item=item}
    <div class="list-group-item">
        <div class="clearfix">
            <h3 class="list-group-item-heading">
                <a href="{$WWWROOT}artefact/learning/details.php?id={$item->id}">{$item->title}
                </a>
            </h3>
            <div class="list-group-item-controls">
                <div class="btn-group btn-group-top">
                    <span class="btn btn-secondary btn-sm">
                        <span class="icon icon-lg icon-paperclip" role="presentation" aria-hidden="true"></span>
                        {$item->count}
                    </span>
                    <a href="{$WWWROOT}artefact/learning/edit.php?id={$item->id}" title="{str(tag=editspecific arg1=$item->title)|escape:html|safe}" class="btn btn-secondary btn-sm">
                        <span class="icon icon-pencil-alt icon-lg" role="presentation" aria-hidden="true"></span>
                        <span class="sr-only">{str tag=edit}</span>
                    </a>
                    <a href="{$WWWROOT}artefact/learning/delete.php?id={$item->id}" title="{str(tag=deletespecific arg1=$item->title)|escape:html|safe}" class="btn btn-secondary btn-sm">
                        <span class="icon icon-trash-alt text-danger icon-lg" role="presentation" aria-hidden="true"></span>
                        <span class="sr-only">{str tag=delete}</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="postdescription">
            {$item->description|clean_html|safe}
        </div>

        {if $item->tags}
        <div class="tags">
            <strong>{str tag=tags}</strong>:
            {list_tags tags=$item->tags owner=$item->owner}
        </div>
        {/if}
    </div>
{/foreach}
</div>
