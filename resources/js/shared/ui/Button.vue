<script setup lang="ts">
interface IProps {
    type?: 'button' | 'submit';
    variant?: string;
    disabled?: boolean;
}

withDefaults(defineProps<IProps>(), {
    type: 'button',
    variant: 'base',
    disabled: false,
});
</script>

<template>
    <button
        class="bg-[#21483f] rounded-[18px] p-3.75 w-full text-amber-50 hover:bg-[#2A6A61] transition duration-300 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
        :type="type"
        :disabled="disabled"
    >
        <slot />
    </button>
</template>