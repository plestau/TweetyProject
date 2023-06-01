<script setup>
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import axios from 'axios'
import TweetyPieHomeLayout from '@/Layouts/TweetyPieHomeLayout.vue'
import Post from '@/Components/Post.vue'
import { defineProps } from '@vue/runtime-core'

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
    observeLoadMoreTrigger()
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
        nextTick(() => {
            observeLoadMoreTrigger()
        })
    }
}

function observeLoadMoreTrigger() {
    unobserveLoadMoreTrigger()
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

watch(() => page.value?.props?.url, (newUrl, oldUrl) => {
    if (oldUrl && newUrl !== oldUrl) {
        unobserveLoadMoreTrigger()
    }
    observeLoadMoreTrigger()
})

onUnmounted(() => {
    unobserveLoadMoreTrigger()
})

function deletePost(postId) {
  // Eliminar el post de la lista de posts
  posts.value.data = posts.value.data.filter((post) => post.id !== postId);

  // Verificar si es necesario actualizar el estado de isLastPage y page
  if (posts.value.data.length === 0 && page.value > 1) {
    page.value--;
    isLastPage.value = false;
  }
}

</script>

<template>
    <TweetyPieHomeLayout>
        <div class="text-white" ref="postsContainer">
            <div class="flex" v-for="post in posts.data" :key="post.id">
                <Post :post="post" />
                <!--Muestra los comentarios si los hubiera-->
            </div>
            <div ref="loadMoreTrigger" v-if="!isLastPage && !isLoading">
                Cargando más posts...
            </div>
        </div>
    </TweetyPieHomeLayout>
</template>
