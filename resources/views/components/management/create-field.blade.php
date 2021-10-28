@if ($type == "textarea")
    <div class="grid grid-cols-3 h-1/3 px-3">
        <label for="{{$name}}" class="text-lg font-bold">
            {{ $label }}
        </label>
        <textarea name="{{$name}}" value="{{old($name, $value)}}" class="text-lg col-span-2" placeholder="{{$label}}"></textarea>
@else
    <div class="grid grid-cols-3 h-6">
        <label for="{{$name}}" class="text-lg font-bold">
            {{ $label }}
        </label>
        <input name="{{$name}}" value="{{old($name, $value)}}" type="{{$type}}" class="text-lg"/>
@endif
@if($errors->has($name))
    <div class="font-bold text-warning-high p-1">{{$errors->first($name)}}</div>
@endif
</div>
