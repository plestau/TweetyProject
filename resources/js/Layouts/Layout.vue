<template>
    <div class="w-full text-white">
        <div id="nav" class="max-w-[1400px] flex mx-auto">
            <!-- Barra de navegación lateral -->
            <div :class="{'lg:w-3/12 w-[60px] h-[100vh] max-w-[350px] lg:px-4 lg:mx-auto': true, 'hidden lg:block': !menuOpen, 'block': menuOpen}" @click="menuOpen = false">
                <div class="p-2 px-3 mb-4">
                    <h2>Logo</h2>
                </div>
                <Link :href="route('post.home')"><MenuItem iconString="Home" ></MenuItem></Link>
                <!--
                <MenuItem iconString="Explore" />
                -->
                <Link :href="route('pages.notifications')"><MenuItem iconString="Notifications" /></Link>
                <Link :href="route('pages.messages')"><MenuItem iconString="Messages" /></Link>
                <Link :href="route('pages.profile')"><MenuItem iconString="Profile" /></Link>
            </div>
            <!-- Contenido principal -->
            <div :class="{'lg:w-9/12 w-full': true, 'hidden lg:block': menuOpen, 'block': !menuOpen}">
                <button class="lg:hidden" @click="menuOpen = !menuOpen">
                    Menú
                </button>
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/inertia-vue3';
import MenuItem from '@/Components/MenuItem.vue';

let menuOpen = ref(false);
let randImg2 = ref(`https://picsum.photos/id/${(Math.random() * 200).toFixed(0)}/100`);
</script>

<style>
body {
    background-color: lightgreen;
}
</style>
