<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="bg-white p-3">
            <h2 class="my-3 text-center">{$info.title|raw}</h2>
            {if $info.description}
            <div class="bg-light my-2 p-3">
                {$info.description|raw}
            </div>
            {/if}
            {$info.contents|raw}
        </div>
    </div>
</div>