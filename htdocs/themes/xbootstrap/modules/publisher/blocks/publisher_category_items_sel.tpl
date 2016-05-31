<{foreach item=category from=$block.categories}>
    <!--<strong><{$category.name}></strong> <br>-->
    <select class="form-control" style="margin-bottom: 5px;" name="publisher_category_item_link"
            onchange="location=this.options[this.selectedIndex].value">
        <{foreach item=item from=$category.items}>
            <option title="<{$item.title}>" value="<{$item.itemurl}>"><{$item.title}></option>
        <{/foreach}>
    </select>
<{/foreach}>
