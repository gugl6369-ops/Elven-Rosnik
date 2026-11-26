<script setup lang="ts">
import { Auth as AuthWidget } from '@/widgets/auth';

defineProps<{
    mode: 'login' | 'register';
}>();
</script>

<template>
    <AuthWidget :mode="mode" />
</template>
