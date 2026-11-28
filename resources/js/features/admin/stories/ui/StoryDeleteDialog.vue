<script setup lang="ts">
import { onMounted, ref } from 'vue';

const emit = defineEmits<{
    cancel: [];
    confirm: [];
}>();

const cancelButton = ref<HTMLButtonElement | null>(null);

onMounted(() => cancelButton.value?.focus());
</script>

<template>
    <div
        class="dialog-backdrop"
        role="presentation"
        @click.self="emit('cancel')"
        @keydown.esc.prevent="emit('cancel')"
    >
        <section
            class="dialog"
            role="alertdialog"
            aria-modal="true"
            aria-labelledby="story-delete-title"
            aria-describedby="story-delete-description"
        >
            <h2 id="story-delete-title" class="dialog__title">Удалить историю?</h2>
            <p id="story-delete-description" class="dialog__description">
                История будет удалена без возможности восстановления.
            </p>
            <div class="dialog__actions">
                <button ref="cancelButton" type="button" class="dialog__cancel" @click="emit('cancel')">
                    Отмена
                </button>
                <button type="button" class="dialog__delete" @click="emit('confirm')">
                    Удалить
                </button>
            </div>
        </section>
    </div>
</template>

<style scoped>
.dialog-backdrop {
    position: fixed;
    inset: 0;
    z-index: 60;
    display: grid;
    place-items: center;
    padding: 1rem;
    background: rgb(24 39 34 / 48%);
}

.dialog {
    width: min(100%, 26rem);
    padding: 1.5rem;
    border: 1px solid #d7e5e0;
    border-radius: 1rem;
    background: #fff;
    box-shadow: 0 20px 55px rgb(20 40 32 / 20%);
}

.dialog__title {
    margin: 0;
    color: #203f39;
    font-size: 1.2rem;
}

.dialog__description {
    margin: 0.65rem 0 1.5rem;
    color: #5a746c;
    font-size: 0.9rem;
    line-height: 1.5;
}

.dialog__actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.6rem;
}

.dialog__cancel,
.dialog__delete {
    min-height: 2.5rem;
    padding: 0.5rem 0.9rem;
    border: 1px solid #d7e5e0;
    border-radius: 0.6rem;
    background: #fff;
    color: #21483f;
    font: inherit;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
}

.dialog__cancel:focus-visible,
.dialog__delete:focus-visible {
    outline: 2px solid #21483f;
    outline-offset: 2px;
}

.dialog__delete {
    border-color: #a43131;
    background: #a43131;
    color: #fff;
}
</style>
