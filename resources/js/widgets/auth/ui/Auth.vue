<script setup lang="ts">
import { LoginForm } from '@/features/auth/login';
import { RegisterForm } from '@/features/auth/register';
import logoUrl from '@/shared/assets/img/icons/logo.svg';

defineProps<{
    mode: 'login' | 'register';
}>();
</script>

<template>
    <div class="grid grid-cols-2 w-full">
        <div
            class="flex flex-col px-[15%] pt-12.5 pb-[calc(150px - 100vh)] w-full h-full justify-center items-center"
        >
            <img :src="logoUrl" alt="logo" />
            <div
                class="flex flex-col gap-6 w-full items-center justify-between"
            >
                <h2 class="text-3xl text-center max-w-[90%] text-[#203f39]">
                    {{ mode === 'login'? 'С возвращением в эльфийский росник!' :  'Добро пожаловать в эльфийский росник!'}}
                </h2>
                <LoginForm v-if="mode === 'login'" />
                <RegisterForm v-else />
            </div>
        </div>
        <div
            class="w-full h-screen bg-cover bg-center bg-no-repeat"
            :style="{ backgroundImage: `url('/image/auth.png')` }"
        />
    </div>
</template>
