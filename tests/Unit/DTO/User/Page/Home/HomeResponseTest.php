<?php

use App\DTO\User\Page\Home\HomeBannerResponse;
use App\DTO\User\Page\Home\HomeCourseListingResponse;
use App\DTO\User\Page\Home\HomeCTAResponse;
use App\DTO\User\Page\Home\HomeFeatureResponse;
use App\DTO\User\Page\Home\HomeHeroResponse;
use App\DTO\User\Page\Home\HomeProcessResponse;
use App\DTO\User\Page\Home\HomeResponse;
use App\DTO\User\Page\Home\HomeStatisticResponse;
use App\DTO\User\Page\Home\HomeTestimonialResponse;
use Tests\TestCase;

uses(TestCase::class);

it('HomeResponse constructs and serializes', function (): void {
    $payloads = [];
    $dto = new HomeResponse(payloads: $payloads);

    expect($dto->toArray())->toBe(['payloads' => []]);
});

it('HomeBannerResponse constructs and serializes', function (): void {
    $dto = new HomeBannerResponse(
        bannerLogo: 'logo.svg',
        bannerEyebrow: 'Học tiếng Anh',
        bannerTitle: 'Chào mừng',
        bannerDescription: 'Mô tả',
        bannerActionButtons: [
            new \App\DTO\User\Page\Home\HomeBannerActionButton(
                title: 'Bắt đầu',
                action: '/courses',
                type: \App\DTO\User\Page\Home\HomeBannerActionButtonTypes::PRIMARY,
            ),
        ],
        bannerHighlights: [
            new \App\DTO\User\Page\Home\HomeBannerHighlight(
                text: 'Lộ trình rõ ràng',
                iconPath: '/icons/path.svg',
                iconType: 'route',
            ),
        ],
    );

    expect($dto->type())->toBe('banner');
    $data = $dto->data();
    expect($data['banner_title'])->toBe('Chào mừng');
    expect($data['banner_action_buttons'][0])->toMatchArray([
        'title' => 'Bắt đầu',
        'action' => '/courses',
        'type' => 'PRIMARY',
    ]);
    expect($data['banner_highlights'][0])->toMatchArray([
        'text' => 'Lộ trình rõ ràng',
        'icon_path' => '/icons/path.svg',
        'icon_type' => 'route',
    ]);

    $array = $dto->toArray();
    expect($array['type'])->toBe('banner');
    expect($array['data']['banner_logo'])->toBe('logo.svg');
    expect(json_decode($dto->toJson(), true))->toBe($array);
    expect(json_decode($dto->bannerActionButtons[0]->toJson(), true))->toBe($data['banner_action_buttons'][0]);
    expect(json_decode($dto->bannerHighlights[0]->toJson(), true))->toBe($data['banner_highlights'][0]);
});

it('HomeCourseListingResponse constructs and serializes', function (): void {
    $dto = new HomeCourseListingResponse(
        title: 'Khóa học nổi bật',
        description: 'Mô tả khóa học',
        courses: [],
        hexBgColors: ['#ff0'],
    );

    expect($dto->type())->toBe('courses');
    expect($dto->data()['title'])->toBe('Khóa học nổi bật');
    expect($dto->toArray()['data']['hex_bg_colors'])->toBe(['#ff0']);
});

it('HomeCTAResponse constructs and serializes', function (): void {
    $dto = new HomeCTAResponse(
        title: 'Đăng ký ngay',
        description: 'Bắt đầu học',
        actionButtons: [
            new \App\DTO\User\Page\Home\HomeCTAActionButton(
                title: 'Đăng ký',
                action: '/register',
                type: \App\DTO\User\Page\Home\HomeCTAActionButtonType::SECONDARY,
            ),
        ],
    );

    expect($dto->type())->toBe('cta');
    expect($dto->data()['title'])->toBe('Đăng ký ngay');
    expect($dto->toArray()['data']['action_buttons'][0])->toMatchArray([
        'title' => 'Đăng ký',
        'action' => '/register',
        'type' => 'SECONDARY',
    ]);
});

it('HomeFeatureResponse constructs and serializes', function (): void {
    $dto = new HomeFeatureResponse(
        eyebrow: 'Tính năng',
        title: 'Học mọi lúc',
        description: 'Mô tả tính năng',
        items: [
            new \App\DTO\User\Page\Home\HomeFeatureCard(
                title: 'Video ngắn',
                description: 'Dễ học mỗi ngày',
                iconPath: '/icons/video.svg',
                iconType: 'video',
                badgeText: 'Mới',
                tagText: 'Daily',
            ),
        ],
    );

    expect($dto->type())->toBe('feature');
    expect($dto->data()['eyebrow'])->toBe('Tính năng');
    expect($dto->toArray()['data']['items'][0])->toMatchArray([
        'title' => 'Video ngắn',
        'description' => 'Dễ học mỗi ngày',
        'icon_path' => '/icons/video.svg',
        'icon_type' => 'video',
        'badge_text' => 'Mới',
        'tag_text' => 'Daily',
    ]);
});

it('HomeHeroResponse constructs and serializes', function (): void {
    $dto = new HomeHeroResponse(
        title: 'Hero Title',
        htmlTitle: '<strong>Hero</strong>',
        description: 'Hero description',
        actions: [
            new \App\DTO\User\Page\Home\HomeHeroActionButton(
                title: 'Xem khóa học',
                action: '/courses',
                type: 'primary',
            ),
        ],
        ctas: [
            new \App\DTO\User\Page\Home\HomeHeroCTA(
                title: '30 phút',
                description: 'Mỗi ngày',
            ),
        ],
        image: 'hero.jpg',
        imageTags: [
            new \App\DTO\User\Page\Home\HomeHeroImageTag(
                text: 'A1-B2',
                hexBgColor: '#ffffff',
                hexTextColor: '#111111',
            ),
        ],
        imageCTA: new \App\DTO\User\Page\Home\HomeHeroImageCTA(
            icon: 'star',
            title: 'CTA Title',
            description: 'CTA Desc',
        ),
    );

    expect($dto->type())->toBe('hero');
    expect($dto->data()['title'])->toBe('Hero Title');
    expect($dto->toArray()['data']['html_title'])->toBe('<strong>Hero</strong>');
    expect($dto->toArray()['data']['actions'][0])->toMatchArray([
        'title' => 'Xem khóa học',
        'action' => '/courses',
        'type' => 'primary',
    ]);
    expect($dto->toArray()['data']['ctas'][0])->toMatchArray([
        'title' => '30 phút',
        'description' => 'Mỗi ngày',
    ]);
    expect($dto->toArray()['data']['image_tags'][0])->toMatchArray([
        'text' => 'A1-B2',
        'hex_bg_color' => '#ffffff',
        'hex_text_color' => '#111111',
    ]);
    expect($dto->toArray()['data']['image_cta'])->toMatchArray([
        'icon' => 'star',
        'title' => 'CTA Title',
        'description' => 'CTA Desc',
    ]);
});

it('HomeProcessResponse constructs and serializes', function (): void {
    $dto = new HomeProcessResponse(
        title: 'Quy trình',
        description: '3 bước',
        steps: [
            new \App\DTO\User\Page\Home\HomeProcessStep(
                label: 'Bước 1',
                title: 'Chọn khóa',
                description: 'Chọn đúng mục tiêu',
                iconPath: '/icons/book.svg',
                iconType: 'book',
            ),
        ],
        tags: ['tag1'],
    );

    expect($dto->type())->toBe('process');
    expect($dto->data()['title'])->toBe('Quy trình');
    expect($dto->toArray()['data']['tags'])->toBe(['tag1']);
    expect($dto->toArray()['data']['steps'][0])->toMatchArray([
        'label' => 'Bước 1',
        'title' => 'Chọn khóa',
        'description' => 'Chọn đúng mục tiêu',
        'icon_path' => '/icons/book.svg',
        'icon_type' => 'book',
    ]);
});

it('HomeStatisticResponse constructs and serializes', function (): void {
    $dto = new HomeStatisticResponse(items: [
        new \App\DTO\User\Page\Home\HomeStatisticItem(
            value: '10K+',
            label: 'Người học',
        ),
    ]);

    expect($dto->type())->toBe('statistics');
    expect($dto->data()['items'][0])->toMatchArray([
        'value' => '10K+',
        'label' => 'Người học',
    ]);
    expect($dto->toArray()['data']['items'][0])->toMatchArray([
        'value' => '10K+',
        'label' => 'Người học',
    ]);
});

it('HomeTestimonialResponse constructs and serializes', function (): void {
    $dto = new HomeTestimonialResponse(
        title: 'Cảm nhận',
        description: 'Học viên nói gì',
        reviews: [],
    );

    expect($dto->type())->toBe('testimonials');
    expect($dto->data()['title'])->toBe('Cảm nhận');
    expect($dto->toArray()['data']['items'])->toBe([]);
});
