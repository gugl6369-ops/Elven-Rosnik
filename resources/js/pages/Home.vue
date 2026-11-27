<script setup lang="ts">
import type { HomePageProps } from '@/entities/story';
import { Catalog } from '@/widgets/catalog';
import { AppShell } from '@/widgets/layout';
import { StoriesStrip } from '@/widgets/stories';
import { AppLink } from '@/shared/ui';

defineProps<Pick<HomePageProps, 'appName' | 'stories' | 'plants'>>();
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
                    :plants="plants"
                    title="Каталог растений"
                    subtitle="Первые карточки — полный список в каталоге."
                    :show-pagination="false"
                />
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