<div class="list-group">
	<div class="list-group-item">
		{if $tags}<div>{str tag=tags}: {list_tags tags=$tags owner=$owner}</div>{/if}
		<p>{$description|clean_html|safe}<p>

		{if $learning->files.stage0}
		<div class="has-attachment card collapsible" id="learningfiles_stage0_{$id}">
			<div class="card-header">
				<a class="text-left collapsed" data-toggle="collapse" href="#learning-attach-{$id}" aria-expanded="false">
					<span class="icon icon-paperclip left icon-sm" role="presentation" aria-hidden="true"></span>
					<span class="text-small"> {str tag=attachedfiles section=artefact.blog} </span>
					 <span class="metadata">
						({$learning->files.stage0|count})
					</span>
					<span class="icon icon-chevron-down collapse-indicator float-right" role="presentation" aria-hidden="true"></span>
				</a>
			</div>
			<div class="collapse" id="learning-attach-{$id}">
				<ul class="list-group list-unstyled">
				{foreach from=$learning->files.stage0 item=file}
					<li class="list-group-item">
					{if $file->icon}
						<img class="file-icon" src="{$file->icon}" alt="">
					{else}
						<span class="icon icon-{$file->artefacttype} icon-lg text-default left" role="presentation" aria-hidden="true"></span>
					{/if}
					{if !$options.editing}
					<span class="title">
						<a class="modal_link" data-toggle="modal-docked" data-target="#configureblock" href="#" data-blockid="{$options.blockid}" data-artefactid="{$file->id}">
							<span class="text-small">{$file->title}</span>
						</a>
					</span>
					{else}
						<span class="title">
							<span class="text-small">{$file->title}</span>
						</span>
					{/if}
						<a href="{$WWWROOT}artefact/file/download.php?file={$file->id}&amp;view={$options.viewid}">
							<span class="sr-only">{str tag=downloadfilesize section=artefact.file arg1=$file->title arg2=$file->size|display_size}</span>
							<span class="icon icon-download icon-lg float-right text-watermark icon-action" role="presentation" aria-hidden="true" data-toggle="tooltip" title="{str tag=downloadfilesize section=artefact.file arg1=$file->title arg2=$file->size|display_size}"></span>
						</a>
						{if $file->description}
						<div class="file-description text-small">
							{$file->description|clean_html|safe}
						</div>
						{/if}
					</li>
				{/foreach}
				</ul>
			</div>
		</div>
		{/if}
	</div>

	{if $learning->priorknowledge}
	<div class="list-group-item">
		<h4>{str tag=stage1title section=artefact.learning}</h4>
		<p>{$learning->priorknowledge|clean_html|safe}</p>
		{if $learning->files.stage1}
		<div class="has-attachment card collapsible" id="learningfiles_stage1_{$id}">
			<div class="card-header">
				<a class="text-left collapsed" data-toggle="collapse" href="#learning-attach-{$id}" aria-expanded="false">
					<span class="icon icon-paperclip left icon-sm" role="presentation" aria-hidden="true"></span>
					<span class="text-small"> {str tag=attachedfiles section=artefact.blog} </span>
					 <span class="metadata">
						({$learning->files.stage1|count})
					</span>
					<span class="icon icon-chevron-down collapse-indicator float-right" role="presentation" aria-hidden="true"></span>
				</a>
			</div>
			<div class="collapse" id="learning-attach-{$id}">
				<ul class="list-group list-unstyled">
				{foreach from=$learning->files.stage1 item=file}
					<li class="list-group-item">
					{if $file->icon}
						<img class="file-icon" src="{$file->icon}" alt="">
					{else}
						<span class="icon icon-{$file->artefacttype} icon-lg text-default left" role="presentation" aria-hidden="true"></span>
					{/if}
					{if !$options.editing}
					<span class="title">
						<a class="modal_link" data-toggle="modal-docked" data-target="#configureblock" href="#" data-blockid="{$options.blockid}" data-artefactid="{$file->id}">
							<span class="text-small">{$file->title}</span>
						</a>
					</span>
					{else}
						<span class="title">
							<span class="text-small">{$file->title}</span>
						</span>
					{/if}
						<a href="{$WWWROOT}artefact/file/download.php?file={$file->id}&amp;view={$options.viewid}">
							<span class="sr-only">{str tag=downloadfilesize section=artefact.file arg1=$file->title arg2=$file->size|display_size}</span>
							<span class="icon icon-download icon-lg float-right text-watermark icon-action" role="presentation" aria-hidden="true" data-toggle="tooltip" title="{str tag=downloadfilesize section=artefact.file arg1=$file->title arg2=$file->size|display_size}"></span>
						</a>
						{if $file->description}
						<div class="file-description text-small">
							{$file->description|clean_html|safe}
						</div>
						{/if}
					</li>
				{/foreach}
				</ul>
			</div>
		</div>
		{/if}
	</div>
	{/if}

	{if $learning->goals}
	<div class="list-group-item">
		<h4>{str tag=stage2title section=artefact.learning}</h4>
		<p>{$learning->goals|clean_html|safe}</p>
		{if $learning->files.stage2}
		<div class="has-attachment card collapsible" id="learningfiles_stage2_{$id}">
			<div class="card-header">
				<a class="text-left collapsed" data-toggle="collapse" href="#learning-attach-{$id}" aria-expanded="false">
					<span class="icon icon-paperclip left icon-sm" role="presentation" aria-hidden="true"></span>
					<span class="text-small"> {str tag=attachedfiles section=artefact.blog} </span>
					 <span class="metadata">
						({$learning->files.stage2|count})
					</span>
					<span class="icon icon-chevron-down collapse-indicator float-right" role="presentation" aria-hidden="true"></span>
				</a>
			</div>
			<div class="collapse" id="learning-attach-{$id}">
				<ul class="list-group list-unstyled">
				{foreach from=$learning->files.stage2 item=file}
					<li class="list-group-item">
					{if $file->icon}
						<img class="file-icon" src="{$file->icon}" alt="">
					{else}
						<span class="icon icon-{$file->artefacttype} icon-lg text-default left" role="presentation" aria-hidden="true"></span>
					{/if}
					{if !$options.editing}
					<span class="title">
						<a class="modal_link" data-toggle="modal-docked" data-target="#configureblock" href="#" data-blockid="{$options.blockid}" data-artefactid="{$file->id}">
							<span class="text-small">{$file->title}</span>
						</a>
					</span>
					{else}
						<span class="title">
							<span class="text-small">{$file->title}</span>
						</span>
					{/if}
						<a href="{$WWWROOT}artefact/file/download.php?file={$file->id}&amp;view={$options.viewid}">
							<span class="sr-only">{str tag=downloadfilesize section=artefact.file arg1=$file->title arg2=$file->size|display_size}</span>
							<span class="icon icon-download icon-lg float-right text-watermark icon-action" role="presentation" aria-hidden="true" data-toggle="tooltip" title="{str tag=downloadfilesize section=artefact.file arg1=$file->title arg2=$file->size|display_size}"></span>
						</a>
						{if $file->description}
						<div class="file-description text-small">
							{$file->description|clean_html|safe}
						</div>
						{/if}
					</li>
				{/foreach}
				</ul>
			</div>
		</div>
		{/if}
	</div>
	{/if}

	{if $learning->strategies}
	<div class="list-group-item">
		<h4>{str tag=stage3title section=artefact.learning}</h4>
		<p>{$learning->strategies|clean_html|safe}</p>
		{if $learning->files.stage3}
		<div class="has-attachment card collapsible" id="learningfiles_stage3_{$id}">
			<div class="card-header">
				<a class="text-left collapsed" data-toggle="collapse" href="#learning-attach-{$id}" aria-expanded="false">
					<span class="icon icon-paperclip left icon-sm" role="presentation" aria-hidden="true"></span>
					<span class="text-small"> {str tag=attachedfiles section=artefact.blog} </span>
					 <span class="metadata">
						({$learning->files.stage3|count})
					</span>
					<span class="icon icon-chevron-down collapse-indicator float-right" role="presentation" aria-hidden="true"></span>
				</a>
			</div>
			<div class="collapse" id="learning-attach-{$id}">
				<ul class="list-group list-unstyled">
				{foreach from=$learning->files.stage3 item=file}
					<li class="list-group-item">
					{if $file->icon}
						<img class="file-icon" src="{$file->icon}" alt="">
					{else}
						<span class="icon icon-{$file->artefacttype} icon-lg text-default left" role="presentation" aria-hidden="true"></span>
					{/if}
					{if !$options.editing}
					<span class="title">
						<a class="modal_link" data-toggle="modal-docked" data-target="#configureblock" href="#" data-blockid="{$options.blockid}" data-artefactid="{$file->id}">
							<span class="text-small">{$file->title}</span>
						</a>
					</span>
					{else}
						<span class="title">
							<span class="text-small">{$file->title}</span>
						</span>
					{/if}
						<a href="{$WWWROOT}artefact/file/download.php?file={$file->id}&amp;view={$options.viewid}">
							<span class="sr-only">{str tag=downloadfilesize section=artefact.file arg1=$file->title arg2=$file->size|display_size}</span>
							<span class="icon icon-download icon-lg float-right text-watermark icon-action" role="presentation" aria-hidden="true" data-toggle="tooltip" title="{str tag=downloadfilesize section=artefact.file arg1=$file->title arg2=$file->size|display_size}"></span>
						</a>
						{if $file->description}
						<div class="file-description text-small">
							{$file->description|clean_html|safe}
						</div>
						{/if}
					</li>
				{/foreach}
				</ul>
			</div>
		</div>
		{/if}
	</div>
	{/if}

	{if $learning->evidence}
	<div class="list-group-item">
		<h4>{str tag=stage4title section=artefact.learning}</h4>
		<p>{$learning->evidence|clean_html|safe}</p>
		{if $learning->files.stage4}
		<div class="has-attachment card collapsible" id="learningfiles_stage4_{$id}">
			<div class="card-header">
				<a class="text-left collapsed" data-toggle="collapse" href="#learning-attach-{$id}" aria-expanded="false">
					<span class="icon icon-paperclip left icon-sm" role="presentation" aria-hidden="true"></span>
					<span class="text-small"> {str tag=attachedfiles section=artefact.blog} </span>
					 <span class="metadata">
						({$learning->files.stage4|count})
					</span>
					<span class="icon icon-chevron-down collapse-indicator float-right" role="presentation" aria-hidden="true"></span>
				</a>
			</div>
			<div class="collapse" id="learning-attach-{$id}">
				<ul class="list-group list-unstyled">
				{foreach from=$learning->files.stage4 item=file}
					<li class="list-group-item">
					{if $file->icon}
						<img class="file-icon" src="{$file->icon}" alt="">
					{else}
						<span class="icon icon-{$file->artefacttype} icon-lg text-default left" role="presentation" aria-hidden="true"></span>
					{/if}
					{if !$options.editing}
					<span class="title">
						<a class="modal_link" data-toggle="modal-docked" data-target="#configureblock" href="#" data-blockid="{$options.blockid}" data-artefactid="{$file->id}">
							<span class="text-small">{$file->title}</span>
						</a>
					</span>
					{else}
						<span class="title">
							<span class="text-small">{$file->title}</span>
						</span>
					{/if}
						<a href="{$WWWROOT}artefact/file/download.php?file={$file->id}&amp;view={$options.viewid}">
							<span class="sr-only">{str tag=downloadfilesize section=artefact.file arg1=$file->title arg2=$file->size|display_size}</span>
							<span class="icon icon-download icon-lg float-right text-watermark icon-action" role="presentation" aria-hidden="true" data-toggle="tooltip" title="{str tag=downloadfilesize section=artefact.file arg1=$file->title arg2=$file->size|display_size}"></span>
						</a>
						{if $file->description}
						<div class="file-description text-small">
							{$file->description|clean_html|safe}
						</div>
						{/if}
					</li>
				{/foreach}
				</ul>
			</div>
		</div>
		{/if}
	</div>
	{/if}

	{if $learning->evaluation}
	<div class="list-group-item">
		<h4>{str tag=stage5title section=artefact.learning}</h4>
		<p>{$learning->evaluation|clean_html|safe}</p>
		{if $learning->files.stage5}
		<div class="has-attachment card collapsible" id="learningfiles_stage5_{$id}">
			<div class="card-header">
				<a class="text-left collapsed" data-toggle="collapse" href="#learning-attach-{$id}" aria-expanded="false">
					<span class="icon icon-paperclip left icon-sm" role="presentation" aria-hidden="true"></span>
					<span class="text-small"> {str tag=attachedfiles section=artefact.blog} </span>
					 <span class="metadata">
						({$learning->files.stage5|count})
					</span>
					<span class="icon icon-chevron-down collapse-indicator float-right" role="presentation" aria-hidden="true"></span>
				</a>
			</div>
			<div class="collapse" id="learning-attach-{$id}">
				<ul class="list-group list-unstyled">
				{foreach from=$learning->files.stage5 item=file}
					<li class="list-group-item">
					{if $file->icon}
						<img class="file-icon" src="{$file->icon}" alt="">
					{else}
						<span class="icon icon-{$file->artefacttype} icon-lg text-default left" role="presentation" aria-hidden="true"></span>
					{/if}
					{if !$options.editing}
					<span class="title">
						<a class="modal_link" data-toggle="modal-docked" data-target="#configureblock" href="#" data-blockid="{$options.blockid}" data-artefactid="{$file->id}">
							<span class="text-small">{$file->title}</span>
						</a>
					</span>
					{else}
						<span class="title">
							<span class="text-small">{$file->title}</span>
						</span>
					{/if}
						<a href="{$WWWROOT}artefact/file/download.php?file={$file->id}&amp;view={$options.viewid}">
							<span class="sr-only">{str tag=downloadfilesize section=artefact.file arg1=$file->title arg2=$file->size|display_size}</span>
							<span class="icon icon-download icon-lg float-right text-watermark icon-action" role="presentation" aria-hidden="true" data-toggle="tooltip" title="{str tag=downloadfilesize section=artefact.file arg1=$file->title arg2=$file->size|display_size}"></span>
						</a>
						{if $file->description}
						<div class="file-description text-small">
							{$file->description|clean_html|safe}
						</div>
						{/if}
					</li>
				{/foreach}
				</ul>
			</div>
		</div>
		{/if}
	</div>
	{/if}
</div>