<?php
use App\Models\Topic;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Component;

new class extends Component {
    public $topic = "";

    public function mount()
    {
        $this->topic = Topic::first()?->topic;
    }

    public function clear() {
        if (Auth::user()->isAdminUser()) {
            DB::table("questions")->delete();
            DB::table("question_votes")->delete();
            DB::table("topics")->delete();

            $this->dispatch("topic-changed");
        }
    }
    public function save() {
        if (Auth::user()->isAdminUser()) {
            DB::table("topics")->delete();
            DB::table("topics")->insert([
                "topic" => $this->topic,
            ]);

            $this->dispatch("topic-changed");
        }
    }
}

?>

<div>
@if (Auth::user()->isAdminUser())
    <form>
        <div class="flex gap-4 max-w-xl">
            <flux:input wire:model="topic" class="hover:scale-105 duration-200 hover:shadow-lg shadow-zinc-800/50 dark:shadow-zinc-900/50" />
            <flux:button wire:click="save"> Save </flux:button>
            <flux:button wire:click="clear"> Clear Questions </flux:button>
        </div>
    </form>
@else
    @if (Topic::first())
        <div class="bg-yellow-100 dark:bg-yellow-800 font-bold p-4 rounded-md text-zinc-900 dark:text-white">
            {{ Topic::first()?->topic }}
        </div>
    @endif
@endif
</div>

