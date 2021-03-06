<?php

namespace GeneaLabs\LaravelCasts\Http\Livewire;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Livewire\Component;

class Combobox extends Component
{
    public $callback;
    public $createFormIsVisible = false;
    public $createFormUrl;
    public $createFormView;
    public $errors = [];
    public $fieldName;
    public $key;
    public $label;
    public $labelField;
    public $model;
    public $optionField;
    public $placeholder = "Search ...";
    public $query;
    public $search;
    public $searchField;
    public $selectedValue;
    public $valueField;

    protected function getListeners()
    {
        return [
            "setErrors{$this->key}" => "setErrors",
            "updateSelectedItem{$this->key}" => "updateSelectedItem",
        ];
    }

    public function mount(
        string $createFormView = "",
        string $createFormUrl = "",
        string $fieldName = "",
        string $label = "",
        string $labelField = "",
        string $model = "",
        string $optionField = "",
        string $placeholder = "",
        string $query = "",
        string $searchField = "",
        string $valueField = "id",
        string $callback = "",
        $value = null
    ) : void {
        $this->key = Str::random();
        $this->createFormView = $createFormView ?: "";
        $this->createFormUrl = $createFormUrl ?: "";
        $this->fieldName = $fieldName ?: "";
        $this->label = $label ?: "";
        $this->labelField = ($labelField ?: ($searchField ?: ""));
        $this->model = $model ?: "";
        $this->optionField = $optionField;
        $this->query = $query ?: "";
        $this->searchField = $searchField ?: "";
        $this->valueField = $valueField ?: "id";
        $this->callback = $callback;
        $value = json_decode($value, false);

        if ($value && ! is_object($value)) {
            $value = (new $this->model)->find($value);
        }

        $this->callback = $callback;

        if ($value) {
            $this->search = $value->{$this->labelField};
            $this->selectedValue = $value->{$this->valueField};
        }

        if ($placeholder) {
            $this->placeholder = $placeholder;
        }
    }

    public function render()
    {
        $results = collect();

        if ($this->search && ! $this->selectedValue) {
            $query = "";

            if ($this->model) {
                $query = (new $this->model);
            }

            if ($this->query) {
                eval("\$query = {$this->query};");
            }

            if ($query) {
                if ($this->searchField) {
                    if (Str::contains($this->searchField, ".")) {
                        $query = $query->whereJoin($this->searchField, "ILIKE", "%{$this->search}%")
                            ->orderByJoin($this->searchField);
                    } else {
                        // TODO: refactor out from if-else.
                        $query = $query->where($this->searchField, "ILIKE", "%{$this->search}%")
                            ->orderBy($this->searchField);
                    }
                }

                $results = $query
                    ->limit(100)
                    ->get();
            }
        }

        if ($this->errors["errors"] ?? false) {
            $messageBag = new MessageBag();

            foreach ($this->errors["errors"] ?? [] as $key => $message) {
                $messageBag->add($key, $message[0]);
            }

            session(["customErrors" => $messageBag]);
        }

        return view('genealabs-laravel-casts::livewire.combobox')
            ->with([
                "results" => $results,
                "id" => $this->id,
            ]);
    }

    public function resetSearch(string $id) : void
    {
        if ($id !== $this->key) {
            return;
        }

        $this->selectedValue = null;
    }

    public function select(string $value, string $search, string $id) : void
    {
        if ($id !== $this->key) {
            return;
        }

        $this->search = $search;
        $this->selectedValue = $value;
    }

    public function showCreateForm(string $id) : void
    {
        if ($id !== $this->key) {
            return;
        }

        $this->createFormIsVisible = true;
    }

    public function cancelForm(string $id) : void
    {
        if ($id !== $this->key) {
            return;
        }

        $this->createFormIsVisible = false;
        $this->search = "";
    }

    public function setErrors(array $errors = [], string $id) : void
    {
        if ($id !== $this->key) {
            return;
        }

        $this->errors = $errors;
    }

    public function updateSelectedItem(array $data = [], string $id) : void
    {
        if ($id !== $this->key) {
            return;
        }

        $this->selectedValue = $data[$this->valueField];
        $this->search = $data[$this->labelField];
        $this->createFormIsVisible = false;
    }
}
