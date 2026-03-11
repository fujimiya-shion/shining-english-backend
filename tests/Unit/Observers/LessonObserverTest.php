<?php

use App\Models\Lesson;
use App\Observers\LessonObserver;
use App\Util\Video\VideoMetadataReader;

test('lesson observer skips duration update when video is not dirty', function (): void {
    $lesson = new Lesson(['video_url' => 'lessons/video.mp4']);
    $lesson->syncOriginal();

    $reader = Mockery::mock(VideoMetadataReader::class);
    $reader->shouldNotReceive('detectDurationMinutes');

    $observer = new LessonObserver($reader);
    $observer->saving($lesson);

    expect($lesson->duration_minutes)->toBeNull();
});

test('lesson observer sets duration when reader returns value', function (): void {
    $lesson = new Lesson(['video_url' => 'lessons/old.mp4']);
    $lesson->syncOriginal();
    $lesson->video_url = 'lessons/new.mp4';

    $reader = Mockery::mock(VideoMetadataReader::class);
    $reader->shouldReceive('detectDurationMinutes')
        ->once()
        ->with('lessons/new.mp4', 'local')
        ->andReturn(12);

    $observer = new LessonObserver($reader);
    $observer->saving($lesson);

    expect($lesson->duration_minutes)->toBe(12);
});

test('lesson observer keeps duration unchanged when reader returns null', function (): void {
    $lesson = new Lesson(['video_url' => 'lessons/old.mp4', 'duration_minutes' => 9]);
    $lesson->syncOriginal();
    $lesson->video_url = 'lessons/new.mp4';

    $reader = Mockery::mock(VideoMetadataReader::class);
    $reader->shouldReceive('detectDurationMinutes')
        ->once()
        ->with('lessons/new.mp4', 'local')
        ->andReturn(null);

    $observer = new LessonObserver($reader);
    $observer->saving($lesson);

    expect($lesson->duration_minutes)->toBe(9);
});
