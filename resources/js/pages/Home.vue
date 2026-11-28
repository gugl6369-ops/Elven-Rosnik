<script setup lang="ts">
import type { HomePageProps } from '@/entities/story';
import { Catalog } from '@/widgets/catalog';
import { AppShell } from '@/widgets/layout';
import { StoriesStrip } from '@/widgets/stories';
import { AppLink } from '@/shared/ui';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps<Pick<HomePageProps, 'appName' | 'stories' | 'plants' | 'recipeSections'>>();

const feedPlants = ref([...props.plants.data]);
const recipeSections = ref([...props.recipeSections]);
const nextPage = ref(props.plants.current_page + 1);
const hasMore = ref(props.plants.current_page < props.plants.last_page);
const loading = ref(false);
const loadError = ref(false);
const autoLoadAvailable = ref(true);
const sentinel = ref<HTMLElement | null>(null);
let observer: IntersectionObserver | null = null;

const feed = computed(() => ({ ...props.plants, data: feedPlants.value }));

async function loadNextPage(): Promise<void> {
    if (loading.value || !hasMore.value) return;

    loading.value = true;
    loadError.value = false;

    try {
        const response = await fetch(`/home/feed?page=${nextPage.value}`, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });
        if (!response.ok) throw new Error('Не удалось загрузить растения');

        const data = await response.json() as {
            plants: HomePageProps['plants']['data'];
            recipeSection: HomePageProps['recipeSections'][number] | null;
            hasMore: boolean;
            nextPage: number | null;
        };

        feedPlants.value.push(...data.plants);
        if (data.recipeSection) recipeSections.value.push(data.recipeSection);
        hasMore.value = data.hasMore;
        if (data.nextPage !== null) nextPage.value = data.nextPage;
    } catch {
        loadError.value = true;
    } finally {
        loading.value = false;
        requestAnimationFrame(() => {
            if (!loadError.value && hasMore.value && sentinel.value && sentinel.value.getBoundingClientRect().top < window.innerHeight + 500) {
                void loadNextPage();
            }
        });
    }
}

onMounted(() => {
    if (!sentinel.value || !('IntersectionObserver' in window)) {
        autoLoadAvailable.value = false;
        return;
    }
    observer = new IntersectionObserver((entries) => {
        if (entries.some((entry) => entry.isIntersecting)) void loadNextPage();
    }, { rootMargin: '500px 0px' });
    observer.observe(sentinel.value);
});

onUnmounted(() => observer?.disconnect());
</script>

<template>
    <AppShell>
        <div class="home">
            <header class="home__hero">
                <h1 class="home__title">
                    {{ appName ?? 'elfi_ros' }}
                </h1>
                <p class="home__subtitle">
                    Истории сообщества и свежие карточки из каталога.
                </p>
            </header>

            <StoriesStrip :stories="stories" />

            <section class="home__catalog">
                <Catalog
                    :plants="feed"
                    :recipe-sections="recipeSections"
                    title="Каталог растений"
                    subtitle="Листайте ленту: новые растения и рецепты подгружаются автоматически."
                    :show-pagination="false"
                />
                <div v-if="hasMore || loading || loadError" ref="sentinel" class="home__feed-status" aria-live="polite">
                    <span v-if="loading">Загружаем растения…</span>
                    <button v-else-if="loadError" type="button" class="home__retry" @click="loadNextPage">
                        Не удалось загрузить. Повторить
                    </button>
                    <button v-else-if="!autoLoadAvailable" type="button" class="home__retry" @click="loadNextPage">
                        Загрузить ещё растения
                    </button>
                    <span v-else>Прокрутите ниже, чтобы загрузить ещё</span>
                </div>
                <p v-else class="home__feed-end">Вы посмотрели все растения.</p>
                <div class="home__catalog-footer">
                    <AppLink
                        href="/catalog"
                        class="home__catalog-link"
                    >
                        Смотреть весь каталог
                    </AppLink>
                </div>
            </section>
        </div>
    </AppShell>
</template>

<style scoped>
.home {
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
}

.home__hero {
    text-align: center;
}

.home__title {
    margin: 0;
    font-size: 2rem;
    font-weight: 600;
    letter-spacing: -0.02em;
    color: #203f39;
}

.home__subtitle {
    margin: 0.5rem 0 0;
    font-size: 0.9375rem;
    color: #78928a;
}

.home__catalog {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.home__catalog-footer {
    display: flex;
    justify-content: center;
}

.home__feed-status,
.home__feed-end {
    display: flex;
    justify-content: center;
    margin: 0;
    padding: 0.5rem;
    color: #648078;
    font-size: 0.875rem;
}

.home__retry {
    border: 0;
    background: none;
    color: #2f6d61;
    font: inherit;
    text-decoration: underline;
    cursor: pointer;
}

.home__catalog-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    border: 1px solid #21483f;
    background: #21483f;
    padding: 0.65rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #fff;
    text-decoration: none;
    transition: background-color 0.15s ease;
}

.home__catalog-link:hover {
    background: #1a3a32;
}
</style>
