<script setup>
import { router } from '@inertiajs/vue3';
import { Link, Head, usePage } from '@inertiajs/inertia-vue3';
import { watch, computed, ref, defineProps, defineAsyncComponent, provide } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import Magnify from 'vue-material-design-icons/Magnify.vue';
import Feather from 'vue-material-design-icons/Feather.vue';
import Close from 'vue-material-design-icons/Close.vue';
import ImageOutline from 'vue-material-design-icons/ImageOutline.vue';
import ArrowLeft from 'vue-material-design-icons/ArrowLeft.vue';
import MenuItem from '@/Components/MenuItem.vue';
import axios from 'axios';

const pageProps = usePage().props;
const recentUsers = ref(pageProps.value.recentUsers);

let createPost = ref(false);
let post = ref('');
let file = ref('');
let showUpload = ref('');
let uploadType = ref('');
let textarea = ref(null);
let searchQuery = ref('');
let searchResults = ref([]);

// Cambiar entre modo claro y oscuro
const isDarkMode = ref(localStorage.getItem('dark_mode') === 'true');

const bgColor = computed(() => isDarkMode.value ? 'bg-black' : 'bg-white');
const textColor = computed(() => isDarkMode.value ? 'text-white' : 'text-black');
const logoColor = computed(() => isDarkMode.value ? 'text-white' : 'text-black');

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
};

// manda el valor de isDarkMode al componente Post para que cambie de color también
provide('isDarkMode', isDarkMode);
provide('toggleDarkMode', toggleDarkMode);

// Guarda el valor en el local storage
watch(isDarkMode, newValue => {
    localStorage.setItem('dark_mode', newValue);
});

// Buscador de usuarios por username
watch(searchQuery, newValue => {
    if (newValue.length >= 3) {
        searchUsers();
    } else {
        searchResults.value = [];
    }
});

const searchUsers = async () => {
    try {
        const response = await axios.get('/api/search', { params: { query: searchQuery.value } });
        searchResults.value = response.data;
    } catch (error) {
        console.log(error);
    }
}

const logout = () => {
    Inertia.post(route('logout'));
}

// Archivos multimedia
const getFile = (e) => {
    file.value = e.target.files[0];
    showUpload.value = URL.createObjectURL(e.target.files[0]);
    uploadType.value = file.value.name.split('.').pop();
}

// Abrir pestaña creación de posts y cerrarla
const closeMessagebox = () => {
    createPost.value = false;
    post.value = '';
    showUpload.value = '';
    uploadType = '';
}

const textAreaInput = (e) => {
    textarea.value.style.height = "auto";
    textarea.value.style.height = `${e.target.scrollHeight}px`;
}

const addPost = () => {
    if (!post.value) return

    let data = new FormData();

    data.append('post', post.value);
    data.append('file', file.value);

    router.post('/posts', data);

    closeMessagebox();
}
</script>

<template>
    <div class="fixed w-full" :class="{ 'bg-black': isDarkMode, 'bg-white': !isDarkMode }">
        <div id="nav" class="max-w-[1400px] flex mx-auto">
            <div class="lg:w-3/12 w-[60px] h-[100vh] max-w-[350px] lg:px-4 lg:mx-auto"
                :class="{ 'text-white': isDarkMode, 'text-black': !isDarkMode }">
                <div>
                    <img v-if="isDarkMode" src="../../images/Logo-darkMode.png" alt="Logo dark mode">
                    <img v-else src="../../images/Logo-noDarkMode.png" alt="Logo light mode">
                </div>
                <Link :href="route('post.home')">
                <MenuItem iconString="Home" :isDarkMode="isDarkMode">
                </MenuItem>
                </Link>
                <!-- no me da tiempo :(
                <Link :href="route('pages.notifications')">
                <MenuItem iconString="Notifications" :isDarkMode="isDarkMode">
                </MenuItem>
                </Link>
                <Link :href="route('pages.messages')">
                <MenuItem iconString="Messages" :isDarkMode="isDarkMode">
                </MenuItem>
                </Link>
                -->
                <Link :href="route('pages.profile')">
                <MenuItem iconString="Profile" :isDarkMode="isDarkMode">
                </MenuItem>
                </Link>
                <button @click="createPost = true"
                    class="lg:w-full mt-8 ml-2 font-extrabold text-[22px] bg-[#1cef2e] p-3 px-3 rounded-full cursor-pointer">
                    <span class="lg:block hidden">Post</span>
                    <span class="block lg:hidden">
                        <Feather />
                    </span>
                </button>
                <div class="mt-10">
                    <div v-if="$page.props.auth.user" :class="{ 'text-white': isDarkMode, 'text-black': !isDarkMode }">
                        <p class="hidden sm:block text-sm font-medium m-5"
                            :class="{ 'text-white': isDarkMode, 'text-gray-700': !isDarkMode }">Bienvenid@, {{
                                $page.props.auth.user.name }}</p>
                        <button @click="logout"
                            class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md mb-3 sm:px-4 sm:py-2 sm:text-sm sm:mb-0"
                            :class="{ 'text-white': !isDarkMode, 'text-black': isDarkMode, 'bg-red-600': !isDarkMode, 'bg-blue-600': isDarkMode, 'hover:bg-red-700': !isDarkMode, 'hover:bg-blue-700': isDarkMode, 'focus:ring-red-500': !isDarkMode, 'focus:ring-blue-500': isDarkMode }">
                            Cerrar sesión
                        </button>
                        <!-- Botón para cambiar el tema oscuro/claro -->
                        <div class="w-14 h-8 flex items-center bg-gray-300 rounded-full mt-5 p-1 duration-300 cursor-pointer"
                            :class="{ 'bg-green-500': isDarkMode }" :aria-checked="isDarkMode.toString()"
                            @click="toggleDarkMode">
                            <div class="bg-white w-8 h-8 md:w-10 md:h-10 rounded-full shadow-md transform duration-300"
                                :class="{ 'translate-x-6': isDarkMode }">
                                <div v-if="isDarkMode">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" class="text-black border-2 border-black">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                </div>
                                <div v-else>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:w-7/12 w-11/12 border-x border-gray-800 relative">
                <div class="bg-black bg-opacity-50 backdrop-blur-md z-10 absolute w-full">
                    <div class="border-gray-800 border-b w-full">
                        <div class="w-full text-white text-[22px] font-extrabold p-4">
                            Home
                        </div>
                        <div class="flex">
                            <div
                                class="flex items-center justify-center w-full h-[60px] text-white text-[17px] font-extrabold p-4 hover:bg-gray-500 hover:bg-opacity-30 cursor-pointer transition duration-200 ease-in-out">
                                <div class="inline-block text-center border-b-4 border-b-[#1cef2e] h-[60px]">
                                    <div class="my-auto mt-4">General</div>
                                </div>
                            </div>
                            <div
                                class="w-full h-[60px] text-gray-500 text-[17px] font-extrabold p-4 hover:bg-gray-500 hover:bg-opacity-30 cursor-pointer transition duration-200 ease-in-out">
                                <div class="text-center">*Próximamente</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="absolute top-0 z-0 w-full h-full overflow-auto scrollbar-hide">
                    <div class="mt-[126px]"></div>
                    <slot />
                    <div class="pb-4"></div>
                </div>
            </div>
            <div class="lg:block hidden lg:w-4/12 h-screen border-l border-gray-800 pl-4">
                <div class="w-full p-1 mt-2 px-4 lg:flex items-center rounded-full hidden bg-[#212327]">
                    <Magnify fillColor="#5e5c5c" :size="25" />
                    <div class="w-full p-1 mt-2 px-4 lg:flex items-center rounded-full hidden bg-[#212327] relative">
                        <input type="text" v-model="searchQuery" @input="searchUsers" placeholder="Search"
                            class="appearance-none w-full border-0 py-2 bg-[#212327] text-gray-100 placeholder-gray-500 leading-tight focus:ring-0">
                        <div v-if="searchResults.length > 0 && searchQuery.length >= 3"
                            class="absolute top-full mt-2 w-full bg-white rounded-md shadow-lg max-h-[400px] overflow-auto z-10">
                            <div v-for="user in searchResults" :key="user.id">
                                <Link class="p-4 border-b border-gray-300 flex justify-between items-center"
                                    :href="`/profile/${user.id}`">
                                <div>
                                    <h3>{{ user.name }}</h3>
                                    <p>{{ user.username }}</p>
                                </div>
                                <img :src="'/storage/' + user.profile_photo_path" alt="Foto de perfil"
                                    class="h-14 w-14 rounded-full object-cover">
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full mt-4 rounded-lg lg:block hidden bg-[#212327]">
                    <div class="w-full p-4 text-white font-extrabold text-[20px]">
                        Cuentas creadas recientemente
                    </div>
                    <div v-for="user in recentUsers" :key="user.id"
                        class="h-[80px] hover:bg-gray-800 cursor-pointer transition duration-200 ease-in-out flex items-center space-x-4 p-3">
                        <div
                            class="h-[80px] hover:bg-gray-800 cursor-pointer transition duration-200 ease-in-out flex items-center space-x-4 p-3">
                            <div class="flex items-center space-x-4">
                                <img :src="'/storage/' + user.profile_photo_path" alt="Foto de perfil"
                                    class="h-10 w-10 rounded-full object-cover">
                                <div>
                                    <div class="font-bold text-white">{{ user.name }}</div>
                                    <div class="text-sm text-gray-400">{{ user.username }}</div>
                                </div>
                            </div>
                            <Link :href="`/profile/${user.id}`">
                            <button class="bg-lime-900 hover:bg-lime-700 text-white font-bold py-2 px-4 rounded mt-2">
                                Ver perfil
                            </button>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="seccionOverlay" v-if="createPost"
        class="fixed top-0 left-0 w-full h-screen bg-black bg-opacity-50 md:bg-gray-400 md:bg-opacity-30 md:p-3">
        <div class="md:max-w-2xl md:mx-auto md:mt-10 md:rounded-xl bg-black bg-opacity-100">
            <div class="flex flex-col w-full items-start justify-between p-2 m-2 rounded-full cursor-pointer opacity-100">
                <div class="w-full flex justify-between mb-3">
                    <div @click="closeMessagebox()"
                        class="hover:bg-gray-800 inline-block p-2 rounded-full cursor-pointer text-white">
                        <Close fill="#FFFFFF" :size="28" class="hidden md:block" />
                        <ArrowLeft fillColor="#FFFFFF" :size="28" class="block md:hidden" />
                    </div>
                    <button @click="addPost()" :disabled="!post"
                        :class="post ? 'bg-[#1cef2e] text white' : 'bg-[#12D477] text-gray-400'"
                        class="block font-extrabold text-base mr-4 md:text-[16px] p-1.5 px-4 rounded-full cursor-pointer">
                        Post
                    </button>
                </div>
                <div class="w-full flex">
                    <div class="ml-3.5 mr-2">
                        <img class="rounded-full" width="55"
                            :src="'/storage/' + $page.props.auth.user.profile_photo_path" />
                    </div>
                    <div class="w-full">
                        <div>
                            <textarea :oninput="textAreaInput" cols="30" rows="4"
                                placeholder="¿Qué se te pasa por la cabeza?" v-model="post" ref="textarea"
                                class="w-full bg-black border-0 mt-2 focus:ring-0 text-white text-[19px] font-extrabold min-h-[120px]"></textarea>
                        </div>
                        <div class="w-full flex justify-center">
                            <video controls v-if="uploadType === 'mp4'" :src="showUpload"
                                class="rounded-xl overflow-auto max-w-full h-auto" />
                            <img v-else :src="showUpload" class="rounded-xl max-w-full h-auto">
                        </div>
                        <div class="flex py-2 items-center text-[#1C9CEF] font-extrabold">
                            <div class="border-b border-b-gray-700"></div>
                            <div class="flex items-center justify-between py-2">
                                <div class="flex items-center">
                                    <div class="hover:bg-gray-800 inline-block p-2 rounded-full cursor-pointer">
                                        <label for="fileUpload" class="cursor-pointer">
                                            <ImageOutline fillColor="#1C9CEF" :size="25" />
                                        </label>
                                        <input type="file" id="fileUpload" class="hidden" @change="getFile" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
