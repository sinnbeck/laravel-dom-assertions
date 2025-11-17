<div>
    <form id="form1" x-data="foo" action="store-comment" enctype="multipart/form-data">
        <label for="comment">Comment</label>
        <textarea name="comment" id="comment" required>
            foo
        </textarea>
        <input type="checkbox" />
    </form>

    <form action="/form" method="post" id="form2">
        @csrf
        @method('PUT')
        <input type="text" name="first_name" value="Foo" />
        @foreach (['Happy', 'Buys cheese'] as $tag)
            <input type="text" name="tags[]" value="{{$tag}}" />
        @endforeach

        @php
            $selected = ['da', 'en'];
        @endphp
        <select name="languages multiple" x-data="bar">
            <option>None</option>
            @foreach(['en' => 'English', 'da' => 'Danish', 'fi' => 'Finland'] as $value => $label)
                <option {{in_array($value, $selected) ? 'selected' : ''}} value="{{$value}}">{{$label}}</option>
            @endforeach
        </select>

        <select name="country" >
            <option x-data="none" value="none" selected>None</option>
            @foreach(['us' => 'USA', 'ua' => 'Ukraine', 'dk' => 'Denmark'] as $value => $label)
                <option value="{{$value}}">{{$label}}</option>
            @endforeach
        </select>

        <select name="things">
            <optgroup label="Animals">
                <option value="dog">Dog</option>
                <option value="cat">Cat</option>
            </optgroup>
            <optgroup label="Vegetables" x-data="none">
                <option value="carrot">Carrot</option>
                <option value="onion">Onion</option>
            </optgroup>
            <optgroup label="Minerals">
                <option value="calcium">Calcium</option>
                <option value="zinc">Zinc</option>
            </optgroup>
        </select>

        <input list="skills" name="skill" value="PHP">
        <datalist id="skills">
            <option value="PHP"></option>
            <option value="JavaScript"></option>
            <option value="C++"></option>
        </datalist>

        <button type="submit"></button>
    </form>
    <input type="text" name="outside">
</div>
