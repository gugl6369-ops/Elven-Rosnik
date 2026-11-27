<script setup lang="ts">
import type { CommentPreview } from '@/entities/comment';
import { EntityCommentsSection } from '@/features/comment';

withDefaults(
    defineProps<{
        plantId: number;
        comments?: CommentPreview[];
        commentsCount?: number;
        isAuthenticated?: boolean;
        currentUserId?: number | null;
        isAdmin?: boolean;
    }>(),
    {
        comments: () => [],
        commentsCount: 0,
        isAuthenticated: false,
        currentUserId: null,
        isAdmin: false,
    },
);
</script>

<template>
    <EntityCommentsSection
        entity-kind="plant"
        :entity-id="plantId"
        reload-prop="plant"
        :comments="comments"
        :comments-count="commentsCount"
        :is-authenticated="isAuthenticated"
        :current-user-id="currentUserId"
        :is-admin="isAdmin"
    />
</template>