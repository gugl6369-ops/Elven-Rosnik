<script setup lang="ts">
import type { AuthPageProps } from '@/entities/user';
import { LogoutButton } from '@/features/account/logout';
import { AppLink } from '@/shared/ui';
import { usePage } from '@inertiajs/vue3';

defineProps<{
    appName?: string;
}>();

const page = usePage<AuthPageProps>();
</script>

<template>
    <div class="home">
        <h1>{{ appName ?? 'elfi_ros' }}</h1>
        <p>Clean slate — ready for product code.</p>
        <div v-if="page.props.auth.user" class="home-nav">
            <AppLink
                href="/catalog"
                class="home-link"
            >
                Каталог растений
            </AppLink>
            <AppLink
                href="/account"
                class="home-link"
            >
                Личный кабинет
            </AppLink>
            <div class="home-logout">
                <LogoutButton />
            </div>
        </div>
    </div>
</template>

<style scoped>
.home {
    min-height: 100vh;
    display: grid;
    place-content: center;
    gap: 0.75rem;
    text-align: center;
    font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}

h1 {
    font-size: 2.5rem;
    font-weight: 600;
    letter-spacing: -0.03em;
    margin: 0;
}

p {
    margin: 0;
    opacity: 0.7;
}

.home-nav {
    margin-top: 0.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.home-logout {
    width: min(100%, 16rem);
}

.home-link {
    color: #21483f;
    text-decoration: underline;
    text-underline-offset: 0.25rem;
}
</style>
