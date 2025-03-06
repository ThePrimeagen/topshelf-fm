<?php

namespace App\Enums;

// GOLANG IN ABSOLUTE SHAMBLES!!!!
enum TwitchSubscription: string
{
    case None = "0000";
    case Tier1 = "1000";
    case Tier2 = "2000";
    case Tier3 = "3000";

    public function isSubscribed(): bool
    {
        return $this->value !== "0000";
    }

    public function maxActiveQuestions(): int
    {
        return match ($this) {
            self::Tier1 => 1,
            self::Tier2 => 2,
            self::Tier3 => 3,
            default => 0,
        };
    }

    public function getVoteValue(): int 
    {
        return match ($this) {
            self::None => 1,
            self::Tier1 => 2,
            self::Tier2 => 4,
            self::Tier3 => 8,
        };
    }
} 