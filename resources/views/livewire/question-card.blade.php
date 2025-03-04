<?php

use App\Models\Question;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public Question $question;
    public int $voteCount;
    public array $userVotes;

    public function upvote(Question $question) {
        DB::table('question_votes')->updateOrInsert([
            'question_id' => $question->id,
            'user_id' => Auth::user()->id,
        ], [
            'count' => 1,
        ]);

        $this->voteCount = $this->question->voteCount();
        $this->userVotes[$question->id] = 1;
    }

    public function downvote(Question $question) {
        DB::table('question_votes')->updateOrInsert([
            'question_id' => $question->id,
            'user_id' => Auth::user()->id,
        ], [
            'count' => -1,
        ]);

        $this->voteCount = $this->question->voteCount();
        $this->userVotes[$question->id] = -1;
    }

    public function deleteQuestion()
    {
        if (! Auth::user()->isAdminUser()) {
            return;
        }

        $this->question->delete();

        $this->dispatch('question-deleted');
    }
} ?>

<div>
    <flux:card class="m-2 rounded-lg max-w-120 bg-zinc-400/5 dark:bg-zinc-900">
        <div class="pl-2">
            <flux:text variant="strong">{{ $question->question }}</flux:text>
            <div class="min-h-2"></div>

            <div class="flex jusify-between items-center">
                <div class="flex items-center mr-auto">
                    <flux:text class="w-4 max-w-4 min-w-4 text-sm mr-2 text-zinc-500 dark:text-zinc-400 tabular-nums">{{ $voteCount }}</flux:text>

                    <div class="flex items-center gap-2">
                        <div>
                        <flux:button
                                wire:click="upvote({{ $question->id }})"
                                variant="{{ ($userVotes[$question->id] ?? 0) > 0 ? 'primary' : 'ghost' }}"
                                size="sm"
                                class="flex items-center" >
                            <flux:icon.hand-thumb-up name="hand-thumb-up" variant="outline" class="size-4 text-zinc-400 [&_path]:stroke-[2.25]" />
                        </flux:button>
                        </div>

                        <div>
                        <flux:button
                                wire:click="downvote({{ $question->id }})"
                                variant="{{ ($userVotes[$question->id] ?? 0) < 0 ? 'primary' : 'ghost' }}"
                                size="sm"
                                class="flex items-center" >
                            <flux:icon.hand-thumb-down name="hand-thumb-down" variant="outline" class="size-4 text-zinc-400 [&_path]:stroke-[2.25]" />

                        </flux:button>
                        </div>

                        @if (Auth::user()->isAdminUser() || Auth::user()->id === $question->user_id)
                            <flux:button wire:click="deleteQuestion()" variant="danger" size="sm" inset="left" class="ml-1 flex items-center gap-2 cursor-pointer" :loading="false">
                                <flux:icon.x-mark name="xmark" variant="outline" class="size-4 text-white [&_path]:stroke-[2.25]" />
                            </flux:button>
                        @endif
                    </div>
                </div>

                <div class="flex items-center pt-2 gap-2">
                    <flux:avatar src="{{ $question->user->twitch_avatar_url }}" size="xs" class="shrink-0" />
                    <flux:subheading variant="strong">
                        {{ $question->user->name }}
                    </flux:subheading>
                    @if ($question->user->poki_sub->isSubscribed())
                        <flux:avatar src="https://static-cdn.jtvnw.net/emoticons/v2/306981855/default/dark/3.0" size="xs" class="shrink-0" />
                    @endif
                </div>

            </div>
        </div>
    </flux:card>
</div>
