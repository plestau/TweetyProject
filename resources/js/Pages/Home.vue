<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/inertia-vue3'
import { defineProps } from '@vue/runtime-core'
import TweetyPieHomeLayout from '@/Layouts/TweetyPieHomeLayout.vue'
import Post from '@/Components/Post.vue'

let { posts: postsProp, recentUsers } = defineProps({
    posts: Object,
    recentUsers: Array
})

let posts = ref(JSON.parse(JSON.stringify(postsProp)))
let page = ref(posts.value.current_page)
let isLastPage = ref(false)
let isLoading = ref(false)
let observer = ref(null)

onMounted(() => {
    if (page.value >= posts.value.last_page) {
        isLastPage.value = true
    }
    if (!posts.value.data) {
        loadMorePosts()
    }
})

async function loadMorePosts() {
    if (!isLastPage.value && !isLoading.value) {
        isLoading.value = true
        page.value++
        const response = await axios.get(`/?page=${page.value}&json=true`, { headers: { 'Accept': 'application/json' }})
        if (response.data && Array.isArray(response.data.data)) {
            posts.value.data = [...posts.value.data, ...response.data.data]
            if (response.data.current_page >= response.data.last_page) {
                isLastPage.value = true
            }
        } else {
            console.log('response.data.data is not an array')
        }
        isLoading.value = false
    }
}

function observeLoadMoreTrigger() {
    observer.value = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            loadMorePosts()
        }
    }, { threshold: 0.5 })
    observer.value.observe(loadMoreTrigger.value)
}

function unobserveLoadMoreTrigger() {
    if (observer.value) {
        observer.value.disconnect()
    }
}

const postsContainer = ref(null)
const loadMoreTrigger = ref(null)

onMounted(() => {
    observeLoadMoreTrigger()
})

watch(() => page.value?.props?.url, (newUrl, oldUrl) => {
    if (oldUrl && newUrl !== oldUrl) {
        unobserveLoadMoreTrigger()
    }
    observeLoadMoreTrigger()
})


onUnmounted(() => {
    unobserveLoadMoreTrigger()
})
</script>

<template>
    <TweetyPieHomeLayout>
        <div class="text-white" ref="postsContainer">
            <div class="flex" v-for="post in posts.data" :key="post.id">
                <Post :post="post" />
            </div>
            <div ref="loadMoreTrigger" v-if="!isLastPage && !isLoading">
                Cargando más posts...
            </div>
        </div>
    </TweetyPieHomeLayout>
</template>
