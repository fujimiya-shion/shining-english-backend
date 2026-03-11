<?php

namespace App\Observers;

use App\Models\Lesson;
use App\Util\Video\VideoMetadataReader;

class LessonObserver
{
    public function __construct(
        protected VideoMetadataReader $videoMetadataReader
    ) {}

    public function saving(Lesson $lesson): void
    {
        if (! $lesson->isDirty('video_url')) {
            return;
        }

        $durationMinutes = $this->videoMetadataReader->detectDurationMinutes($lesson->video_url, 'local');
        if ($durationMinutes !== null) {
            $lesson->duration_minutes = $durationMinutes;
        }
    }
}
