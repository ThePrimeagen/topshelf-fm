<?php

use Livewire\Volt\Component;
use App\Models\Question;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $question = "";
    public $userVotes = [];

    public function mount() {
        $this->userVotes = auth()->user()->votes()->get()
            ->mapWithKeys(fn($vote) => [$vote->question_id => $vote->count])
            ->toArray();
    }

    public function saveQuestion()
    {
        $this->validate([
            'question' => 'required|min:10|max:420',
        ]);

        // TODO: Make this prettier
        if (!auth()->user()->canSubmitQuestion()) {
            throw ValidationException::withMessages([
                'question' => 'You have reached the question limit',
            ]);
        }

        auth()->user()->questions()->create([
            'question' => $this->question,
        ]);

        $this->question = "";
    }

    public function clearUserQuestion() {
        auth()->user()->questions()->delete();
    }

} ?>

<x-layouts.app>
    @volt
    <div>
        <livewire:topic @topic-changed="$refresh" />

        <div class="mt-4" wire:poll.10s>
            @if (Auth::user()->canSubmitQuestion())
            <form wire:submit="saveQuestion">
                <flux:input.group>
                    <flux:input wire:model="question" placeholder="Your Greatest Query" />
                    <flux:button type="submit" class="cursor-pointer">Submit</flux:button>
                </flux:input.group>
            </form>
            @else
            @if (DB::table("topics")->count() > 0)
                <div class="items-center flex gap-2">
                    <flux:heading>Question Limit Reached</flux:heading>
                    <flux:button wire:click="clearUserQuestion" variant="danger" size="xs" inset="left" class="ml-1 flex items-center gap-2 cursor-pointer" :loading="false">
                        <flux:icon.x-mark name="xmark" variant="outline" class="size-4 text-white [&_path]:stroke-[2.25]" />
                    </flux:button>
                </div>
            @else
                <flux:heading>There is no topic</flux:heading>
            @endif
        @endif
        </div>

        <div class="mt-6 grid sm:grid-cols-2 gap-2">
            <div wire:poll.30s>
                <h2>Hot Questions</h2>
                <ul>
                    @foreach (Question::getSortedQuestions() as $question)
                        <li wire:key="hot-li-{{ $question->id }}">
                            <livewire:question-card @question-deleted="$refresh" :user-votes="$userVotes" :question="$question" :vote-count="$question->votes" :key="'hot-'.$question->id" />
                        </li>
                    @endforeach
                </ul>
            </div>

            <div wire:poll.30s>
                <h2>Latest Questions</h2>
                <ul>
                    @foreach (Question::getRecentQuestions() as $question)
                        <li wire:key="recent-li-{{ $question->id }}">
                            <livewire:question-card @question-deleted="$refresh" :question="$question" :vote-count="$question->votes" :key="'recent-'.$question->id" />
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endvolt
</x-layouts.app>
