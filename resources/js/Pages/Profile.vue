<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import Post from '@/Components/Post.vue';
defineProps({ posts: Array });

</script>

<template>
    <Head title="Profile" />
    <Layout>
        <section class="flex flex-col items-center bg-gray-800 text-white p-6">
            <div class="bg-black bg-opacity-50 backdrop-blur-md z-10 w-full">
                <div class="w-full text-[22px] font-extrabold p-4">
                    Profile
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-600 mb-4">
                        <img :src="`/storage/${$page.props.auth.user.profile_photo_path}`" alt="User's profile picture"
                            class="w-full h-full object-cover">
                    </div>
                    <p v-if="$page.props.auth.user">
                    <p>Name: {{ $page.props.auth.user.name }}</p>
                    <p>Username: {{ $page.props.auth.user.username }}</p>
                    <p>Email: {{ $page.props.auth.user.email }}</p>
                    <p>Joined: {{ new Date($page.props.auth.user.created_at).toLocaleDateString() }}</p>
                    <p>Bio: {{ $page.props.auth.user.biography }}</p>
                    </p>
                    <p v-else>No hay usuario autenticado.</p>
                </div>
            </div>
        </section>
        <section class="px-6 py-4 bg-gray-800 text-white">
            <h2 class="font-bold text-lg mb-2">Activity</h2>
            <!-- Aquí muestra la actividad del usuario, como posts, fotos, etc. -->
            <div class="text-white">
            <div class="flex" v-for="post in $page.props.posts" :key="post">
                <Post :post="post" :key="post.id" />
            </div>
            <div class="border-b border-b-gray-800 mt-2"></div>
        </div>
        </section>
    </Layout>
</template>
