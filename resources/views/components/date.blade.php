<x-form-group
    {{ $attributes->only(["x-show", "x-if"]) }}
    :class="$groupClasses"
    :errors="$errors"
    :helpText="$helpText"
>
    @if ($label)
        <x-form-label
            :field="$name"
            :value="$label"
            class="{{ $labelClasses }}"
        />
    @endif

    <input
        {{ $attributes->merge(["class" => "form-input"])->except(["x-show", "x-if"]) }}
        name="{{ $name }}"
        type="date"
    >
</x-form-group>
