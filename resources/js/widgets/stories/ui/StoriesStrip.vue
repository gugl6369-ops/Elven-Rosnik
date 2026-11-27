<script setup lang="ts">
import type { StoryListItem } from '@/entities/story';
import { AppLink } from '@/shared/ui';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        stories: StoryListItem[];
        title?: string;
    }>(),
    {
        title: 'Истории',
    },
);

const hasStories = computed(() => props.stories.length > 0);

function storyImage(story: StoryListItem): string | null {
    if (story.media_type === 'image' && story.media_url) {
        return story.media_url;
    }

    return story.author?.avatar ?? null;
}
</script>

<template>
    <section v-if="hasStories" class="stories" aria-label="Лента историй">
        <h2 class="stories__title">{{ title }}</h2>
        <ul class="stories__row">
            <li
                v-for="story in stories"
                :key="story.id"
                class="stories__item"
            >
                <component
                    :is="story.link_url ? AppLink : 'div'"
                    :href="story.link_url ?? undefined"
                    class="stories__bubble"
                >
                    <span class="stories__ring">
                        <img
                            v-if="storyImage(story)"
                            :src="storyImage(story)!"
                            :alt="story.label"
                            class="stories__media"
                            loading="lazy"
                            width="72"
                            height="72"
                        />
                        <span v-else class="stories__placeholder" aria-hidden="true">
                            {{ story.label.charAt(0).toUpperCase() }}
                        </span>
                    </span>
                    <span class="stories__label">{{ story.label }}</span>
                </component>
            </li>
        </ul>
    </section>
</template>

<style scoped>
.stories {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.stories__title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #203f39;
}

.stories__row {
    display: flex;
    gap: 1rem;
    margin: 0;
    padding: 0.25rem 0.125rem 0.5rem;
    list-style: none;
    overflow-x: auto;
    scrollbar-width: thin;
    scrollbar-color: #b8cdc6 transparent;
}

.stories__item {
    flex: 0 0 auto;
    width: 5.5rem;
}

.stories__bubble {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    color: inherit;
    cursor: default;
}

a.stories__bubble {
    cursor: pointer;
}

.stories__ring {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 4.75rem;
    height: 4.75rem;
    padding: 3px;
    border-radius: 9999px;
    background: linear-gradient(145deg, #3d8b7a, #21483f);
    box-shadow: 0 2px 10px rgb(33 72 63 / 18%);
}

.stories__media {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 9999px;
    border: 2px solid #f5f8f7;
}

.stories__placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    border-radius: 9999px;
    border: 2px solid #f5f8f7;
    background: #e8f2ef;
    font-size: 1.25rem;
    font-weight: 600;
    color: #21483f;
}

.stories__label {
    max-width: 100%;
    font-size: 0.75rem;
    line-height: 1.25;
    text-align: center;
    color: #5a746c;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

a.stories__bubble:hover .stories__label {
    color: #21483f;
}
</style>