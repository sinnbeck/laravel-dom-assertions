<div>
    <form id="form1" x-data="foo" action="store-comment" enctype="multipart/form-data">
        <label for="comment">Comment</label>
        <textarea name="comment" id="comment">
            foo
        </textarea>
    </form>

    <form action="/form" method="post" id="form2">
        @csrf
        @method('PUT')
        <input type="text" name="first_name" value="Foo" />
        @foreach (['Happy', 'Buys cheese'] as $tag)
            <input type="text" name="tags[]" value="{{$tag}}" />
        @endforeach

        <select name="language">
            <option>None</option>
            @foreach(['en' => 'English', 'da' => 'Danish'] as $value => $label)
                <option value="{{$value}}">{{$label}}</option>
            @endforeach
        </select>

        <select name="country" >
            <option x-data="none" value="none" selected>None</option>
            @foreach(['us' => 'USA', 'ua' => 'Ukraine', 'dk' => 'Denmark'] as $value => $label)
                <option value="{{$value}}">{{$label}}</option>
            @endforeach
        </select>

        <button type="submit"></button>
    </form>
    <input type="text" name="outside">
</div>