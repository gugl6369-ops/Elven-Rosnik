<script setup lang="ts">
import { ContinueRegisterForm } from '@/features/auth/register';
</script>

<template>
    <div class="grid grid-cols-2 w-full">
        <div
            class="w-full h-screen bg-cover bg-center bg-no-repeat"
            :style="{ backgroundImage: `url('/image/auth.png')` }"
        />
        <div
            class="w-full flex flex-col px-[15%] pt-12.5 pb-[calc(150px - 100vh)] h-full justify-center items-center"
        >
            <ContinueRegisterForm />
        </div>
    </div>
</template>