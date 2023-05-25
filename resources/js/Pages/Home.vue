<script setup>

import { Head, Link } from '@inertiajs/vue3';
import Post from '@/Components/Post.vue';
import TweetyPieHomeLayout from '@/Layouts/TweetyPieHomeLayout.vue';

defineProps({posts: Array});
</script>

<template>
    <Head title="Home" />
    <TweetyPieHomeLayout>
        <div class="text-white">
            <div class="flex" v-for="post in posts" :key="post">
                <Post :post="post" :key="post.id" />
            </div>
            <div class="border-b border-b-gray-800 mt-2"></div>
        </div>
    </TweetyPieHomeLayout>
</template>
