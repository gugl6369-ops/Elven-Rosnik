<script setup lang="ts">
import type { AccountPageProps } from '@/entities/user';
import { Account as AccountWidget } from '@/widgets/account';

defineProps<AccountPageProps>();
</script>

<template>
    <AccountWidget :user="user" />
</template>