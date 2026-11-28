<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresPublicUploads;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStoryRequest;
use App\Models\Story;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoryController extends Controller
{
    use StoresPublicUploads;

    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $stories = Story::query()
            ->with('author:id,username,name')
            ->when($search !== '', function ($query) use ($search): void {
                $like = '%'.$search.'%';
                $query->where(function ($inner) use ($like): void {
                    $inner->where('caption', 'ilike', $like)
                        ->orWhereHas('author', function ($author) use ($like): void {
                            $author->where('username', 'ilike', $like)
                                ->orWhere('name', 'ilike', $like);
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Story $story): array => $this->formatAdminStory($story));

        return Inertia::render('admin/ui/StoriesPage', [
            'stories' => $stories,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function store(StoreStoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $file = $request->file('media');
        $mediaUrl = $this->storePublicImage($file, 'stories');

        Story::query()->create([
            'author_id' => $request->user()->id,
            'media_url' => $mediaUrl,
            'media_type' => 'image',
            'caption' => $validated['caption'] ?? null,
            'active_from' => now(),
            'active_until' => $validated['active_until'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return redirect()
            ->route('admin.stories.index')
            ->with('success', 'История создана.');
    }

    public function updateStatus(Request $request, Story $story): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $wasExpired = $story->active_until->isPast();
        $story->is_active = (bool) $validated['is_active'];

        if ($story->is_active && $wasExpired) {
            $story->active_from = now();
            $story->active_until = now()->addHours(24);
        }

        $story->save();

        $message = ! $story->is_active
            ? 'История скрыта из ленты.'
            : ($wasExpired ? 'История добавлена в ленту на 24 часа.' : 'История добавлена в ленту.');

        return redirect()
            ->route('admin.stories.index')
            ->with('success', $message);
    }

    public function destroy(Story $story): RedirectResponse
    {
        $this->deletePublicPath($story->media_url);
        $story->delete();

        return redirect()
            ->route('admin.stories.index')
            ->with('success', 'История удалена.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formatAdminStory(Story $story): array
    {
        $author = $story->author;
        $visibleOnHome = $story->is_active && $story->active_until->isFuture();

        return [
            'id' => $story->id,
            'media_url' => $story->media_url,
            'media_type' => $story->media_type,
            'caption' => $story->caption,
            'active_from' => $story->active_from->toIso8601String(),
            'active_until' => $story->active_until->toIso8601String(),
            'is_active' => $story->is_active,
            'visible_on_home' => $visibleOnHome,
            'created_at' => $story->created_at?->toIso8601String(),
            'author' => $author !== null
                ? [
                    'id' => $author->id,
                    'username' => $author->username,
                    'name' => $author->name,
                ]
                : null,
        ];
    }
}
