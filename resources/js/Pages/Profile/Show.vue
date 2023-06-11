<script setup>
import axios from 'axios'
import { defineProps } from '@vue/runtime-core'
import { Head } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import Post from '@/Components/Post.vue';
import { onBeforeMount, ref, watch, onMounted, onUnmounted, nextTick } from 'vue';

const isDarkMode = ref(false);

onBeforeMount(() => {
    const darkModeValue = localStorage.getItem('dark_mode');
    isDarkMode.value = darkModeValue === 'true';
});

const postsContainer = ref(null)
const loadMoreTrigger = ref(null)

function observeLoadMoreTrigger() {
    if (loadMoreTrigger.value) {
        observer.value = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadMorePosts()
                }
            })
        })
        observer.value.observe(loadMoreTrigger.value)
    }
}

function unobserveLoadMoreTrigger() {
    if (observer.value && loadMoreTrigger.value) {
        observer.value.unobserve(loadMoreTrigger.value)
        observer.value = null
    }
}

onMounted(() => {
    postsContainer.value = document.getElementById('postsContainer')
    loadMoreTrigger.value = document.getElementById('loadMoreTrigger')
    if (page.value >= posts.value.last_page) {
        isLastPage.value = true
    }
    if (!posts.value.data) {
        loadMorePosts()
    }
    observeLoadMoreTrigger()
})

onUnmounted(() => {
    unobserveLoadMoreTrigger()
})


async function loadMorePosts() {
    if (!isLastPage.value && !isLoading.value) {
        isLoading.value = true
        page.value++
        const response = await axios.get(`/?page=${page.value}&json=true`, { headers: { 'Accept': 'application/json' } })
        if (response.data && Array.isArray(response.data.data)) {
            posts.value.data = [...posts.value.data, ...response.data.data]
            if (response.data.current_page >= response.data.last_page) {
                isLastPage.value = true
            }
        } else {
            console.log('response.data.data is not an array')
        }
        isLoading.value = false
        nextTick(() => {
            observeLoadMoreTrigger()
        })
    }
}

let { posts: postsProp } = defineProps({
    posts: Object,
    user: Object
})

let posts = ref({ ...postsProp, data: postsProp.data ? [...postsProp.data] : [] });
let page = ref(posts.value.current_page);
let isLastPage = ref(false);
let isLoading = ref(false);
let observer = ref(null);
</script>

<template>
    <Head title="Profile" />
    <Layout>
        <section class="flex flex-col items-center bg-gray-800 text-white p-6">
            <div class="bg-black bg-opacity-50 backdrop-blur-md z-10 w-full">
                <div class="w-full text-[22px] font-extrabold p-4 text-center">
                    Profile
                </div>
                <div v-if="user">
                    <div class="flex flex-col md:flex-row justify-center items-center">
                        <div class="flex flex-col items-center md:items-end mb-8 md:mb-0 md:mr-24 text-justify">
                            <p>{{ user.email }}</p>
                            <p>Joined: {{ new Date(user.created_at).toLocaleDateString() }}</p>
                        </div>
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-600 mb-8 md:mb-4">
                            <img :src="`/storage/${user.profile_photo_path}`" alt="User's profile picture"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="flex flex-col items-center md:items-start md:ml-24">
                            <div class="flex items-center">
                                <p class="mr-2">{{ user.name }}</p>
                                <p class="opacity-80 ml-12">{{ user.username }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center mt-4">
                        <div class="flex flex-col items-center md:items-start">
                            <div class="flex items-center mb-10">
                                <p>{{ user.biography }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else>No hay usuario autenticado.</p>
            </div>
        </section>
        <section class="px-6 py-4 bg-gray-800 text-white">
            <h2 class="font-bold text-lg mb-2">Últimos posts</h2>
            <!-- Aquí muestra la actividad del usuario -->
            <div class="w-full" ref="postsContainer" v-for="post in posts.data" :key="post.id">
                <div v-if="user.username === post.user.username">
                    <Post :post="post" />
                </div>
            </div>
            <div id="loadMoreTrigger" ref="loadMoreTrigger" v-if="!isLastPage && !isLoading">
                Cargando más posts...
            </div>
        </section>
    </Layout>
</template>
