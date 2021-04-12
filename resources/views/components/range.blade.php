<x-form-group
    {{ $attributes->only(["x-show", "x-if"]) }}
    :class="$groupClasses"
    :errors="$errors"
    :helpText="$helpText"
>
    <input
        {{ $attributes->except(["x-show", "x-if"]) }}
        type="range"
        :name="$name"
        :value="$value"
    >
</x-form-group>
