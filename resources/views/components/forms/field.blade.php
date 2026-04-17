@props(['element' => null, 'name' => ''])

<input
    type="{{ $element->get_type() }}"
    class="form-control 
    @if($element->type === \Ro749\SharedUtils\Forms\InputType::PERCENTAGE)
    input-percent
    @elseif($element->type === \Ro749\SharedUtils\Forms\InputType::MONEY)
    input-money
    @elseif($element->type === \Ro749\SharedUtils\Forms\InputType::PIN)
    input-pin
    @endif
    @if(isset($classes)) 
    {{ $classes }}
    @endif
    "
    id="{{ $name }}"
    name="{{ $name }}"
    @if($element->type !== \Ro749\SharedUtils\Forms\InputType::PERCENTAGE && $element->type !== \Ro749\SharedUtils\Forms\InputType::MONEY)
    x-model="form.{{ $name }}"
    @endif
    placeholder="{{ $element->placeholder }}"
    @if(
        $element->type === \Ro749\SharedUtils\Forms\InputType::TEXT ||
        $element->type === \Ro749\SharedUtils\Forms\InputType::PASSWORD ||
        $element->type === \Ro749\SharedUtils\Forms\InputType::PIN
        )
        @if(!empty($element->max))
    maxlength="{{ $element->max }}"
        @endif
    @endif

    @if(
        $element->type === \Ro749\SharedUtils\Forms\InputType::NUMBER ||
        $element->type === \Ro749\SharedUtils\Forms\InputType::PERCENTAGE ||
        $element->type === \Ro749\SharedUtils\Forms\InputType::MONEY
    )
        @if($element->max!==null)
    max="{{ $element->max }}"
        @endif
        @if($element->min!==null)
    min="{{ $element->min }}"
        @endif
    @endif


    @if(
        $element->type === \Ro749\SharedUtils\Forms\InputType::ID_NUMBER ||
        $element->type === \Ro749\SharedUtils\Forms\InputType::PHONE
        )
    oninput="this.value = this.value.replace(/\D/g, '')"
    @endif
    @if($element->type === \Ro749\SharedUtils\Forms\InputType::EMAIL)
    oninput="this.value = this.value.toLowerCase()"
    @endif
    @if($element->autosave)
        @if($element->type === \Ro749\SharedUtils\Forms\InputType::CHECKBOX)
        @change="submit()"
        @else
        @input.debounce.500ms="submit()"
        @endif
    @endif
>

