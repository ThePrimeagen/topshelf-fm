<?php

use App\Models\Question;
use App\Models\QuestionVote;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;
use App\Enums\TwitchSubscription;

new class extends Component {
    public Question $question;
    public TwitchSubscription $subscription;
    public int $voteCount;
    public array $userVotes;
    public bool $canEdit;

    public function mount(Question $question, int $voteCount, array $userVotes)
    {
        $this->question = $question;
        $this->voteCount = $voteCount;
        $this->userVotes = $userVotes;

        if (Auth::user() == null) {
            $this->canEdit = false;
        } else {
            $this->canEdit = Auth::user()->isAdminUser() || Auth::user()->id === $this->question->user_id;
            $this->subscription = Auth::user()->getHighestSubscription();
        }
    }

    public function upvote()
    {
        QuestionVote::upvote($this->question->id, Auth::user()->id, $this->subscription);

        $this->voteCount = $this->question->voteCount();
        $this->userVotes[$this->question->id] = 1;
    }

    public function downvote()
    {
        QuestionVote::downvote($this->question->id, Auth::user()->id, $this->subscription);

        $this->voteCount = $this->question->voteCount();
        $this->userVotes[$this->question->id] = -1;
    }

    public function deleteQuestion()
    {
        if (!Auth::user()->isAdminUser()) {
            return;
        }

        $this->question->delete();
        $this->dispatch('question-deleted');
    }
}; ?>

<div>
    <flux:card class="m-2 rounded-lg max-w-120 bg-zinc-400/5 dark:bg-zinc-900">
        <div class="pl-2">
            <flux:text variant="strong">{{ $question->question }}</flux:text>
            <div class="min-h-2"></div>

            <div class="flex justify-between items-center">
                <div class="flex items-center mr-auto">
                    <flux:text class="w-4 max-w-4 min-w-4 text-sm mr-2 text-zinc-500 dark:text-zinc-400 tabular-nums">
                        <span wire:text="voteCount"></span>
                    </flux:text>

                    <div class="flex items-center gap-2">
                        <div>
                            <flux:button wire:click="upvote"
                                variant="{{ ($userVotes[$this->question->id] ?? 0) > 0 ? 'primary' : 'ghost' }}"
                                size="sm" class="flex items-center">
                                <flux:icon.hand-thumb-up name="hand-thumb-up" variant="outline"
                                    class="size-4 text-zinc-400 [&_path]:stroke-[2.25]" />
                            </flux:button>
                        </div>

                        <div>
                            <flux:button wire:click="downvote"
                                variant="{{ ($userVotes[$this->question->id] ?? 0) < 0 ? 'primary' : 'ghost' }}"
                                size="sm" class="flex items-center">
                                <flux:icon.hand-thumb-down name="hand-thumb-down" variant="outline"
                                    class="size-4 text-zinc-400 [&_path]:stroke-[2.25]" />

                            </flux:button>
                        </div>

                        @if ($canEdit)
                            <flux:button wire:click="deleteQuestion()" variant="danger" size="sm" inset="left"
                                class="ml-1 flex items-center gap-2 cursor-pointer" :loading="false">
                                <flux:icon.x-mark name="xmark" variant="outline"
                                    class="size-4 text-white [&_path]:stroke-[2.25]" />
                            </flux:button>
                        @endif
                    </div>
                </div>

                <div class="flex items-center pt-2 gap-2">
                    <flux:avatar src="{{ $question->user->twitch_avatar_url }}" size="xs" class="shrink-0" />
                    <flux:subheading variant="strong">
                        {{ $question->user->name }}
                    </flux:subheading>
                </div>

            </div>
        </div>
    </flux:card>
</div>
