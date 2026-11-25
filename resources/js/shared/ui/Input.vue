<script setup lang="ts">
import { computed, ref } from 'vue';

interface IProps {
    error?: string | boolean;
    disabled?: boolean;
    label: string;
    placeholder: string;
    type?: string;
    required?: boolean;
}

const props = withDefaults(defineProps<IProps>(), {
    type: 'text',
    disabled: false,
    error: undefined,
    placeholder: '',
});

const emit = defineEmits<{
    blur: [];
    input: [];
}>();

const model = defineModel<string>();
const check = ref<boolean>(false);

const checkType = computed<string>(() => {
    if (check.value && props.type === 'password') {
        return 'text';
    }
    if (!check.value && props.type === 'password') {
        return 'password';
    }

    return props.type;
});
</script>

<template>
    <label class="flex flex-col gap-3 w-full">
        <span class="text-sm text-[#444444]">
            {{ label }}
        </span>
        <div
            class="relative w-full border-2 rounded-[18px]"
            :class="error ? 'border-red-600' : 'border-[#21483f]'"
        >
            <input
                v-model="model"
                :type="checkType"
                required
                :placeholder="placeholder"
                :disabled="disabled"
                class="h-[44px] w-full bg-white px-4 rounded-[18px] text-sm text-[#203f39] outline-none transition"
                @blur="emit('blur')"
                @input="emit('input')"
            />
            <button
                v-if="type === 'password'"
                type="button"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-[#78928a]"
                aria-label="Показать пароль"
                @click="check = !check"
            >
                <svg
                    v-if="!check"
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                >
                    <path
                        d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"
                    />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <svg
                    v-else
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                >
                    <path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8" />
                    <path
                        d="M9.9 5.2A10 10 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3.1 3.8M6.2 6.2C3.5 8.1 2 12 2 12s3.6 7 10 7c1.2 0 2.3-.3 3.3-.7"
                    />
                </svg>
            </button>
        </div>
        <span
            v-if="typeof error === 'string' && error"
            class="text-sm text-red-600"
        >
            {{ error }}
        </span>
    </label>
</template>