<script setup lang="ts">
defineProps<{
    appName?: string;
}>();
</script>

<template>
    <div class="home">
        <h1>{{ appName ?? 'elfi_ros' }}</h1>
        <p>Clean slate — ready for product code.</p>
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
</style>
