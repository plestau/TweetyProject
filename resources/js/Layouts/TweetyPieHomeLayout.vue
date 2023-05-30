<script setup>
import { ref, defineProps } from 'vue';
import { router } from '@inertiajs/vue3';
import { Link, Head, usePage } from '@inertiajs/inertia-vue3';
import Layout from '@/Layouts/TweetyPieHomeLayout.vue';
import { defineAsyncComponent } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import TweetyPielogo from 'vue-material-design-icons/Twitter.vue';
import Magnify from 'vue-material-design-icons/Magnify.vue';
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue';
import Feather from 'vue-material-design-icons/Feather.vue';
import Close from 'vue-material-design-icons/Close.vue';
import ChevronDown from 'vue-material-design-icons/ChevronDown.vue';
import Earth from 'vue-material-design-icons/Earth.vue';
import ImageOutline from 'vue-material-design-icons/ImageOutline.vue';
import FileGifBox from 'vue-material-design-icons/FileGifBox.vue';
import Emoticon from 'vue-material-design-icons/Emoticon.vue';
import ArrowLeft from 'vue-material-design-icons/ArrowLeft.vue';
import MenuItem from '@/Components/MenuItem.vue';

const pageProps = usePage().props;
const recentUsers = ref(pageProps.value.recentUsers);

let createPost = ref(false);
let post = ref('');
let file = ref('');
let showUpload = ref('');
let uploadType = ref('');
let textarea = ref(null);
let randImg1 = ref(`https://picsum.photos/id/${(Math.random() * 200).toFixed(0)}/100`);
let randImg2 = ref(`https://picsum.photos/id/${(Math.random() * 200).toFixed(0)}/100`);

const logout = () => {
    Inertia.post(route('logout'));
}

const login = () => {
    // manda al usuario al componente vue login
    Inertia.visit('/login');
}

const register = () => {
    // manda al usuario al componente vue register
    Inertia.visit('/register');
}

const getFile = (e) => {
    file.value = e.target.files[0];
    showUpload.value = URL.createObjectURL(e.target.files[0]);
    uploadType.value = file.value.name.split('.').pop();
}

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
    <div class="fixed w-full">
        <div id="nav" class="max-w-[1400px] flex mx-auto">
            <div class="lg:w-3/12 w-[60px] h-[100vh] max-w-[350px] lg:px-4 lg:mx-auto text-white">
                <div class="p-2 px-3 mb-4">
                    <h2>Logo</h2>
                </div>
                <Link :href="route('post.home')">
                <MenuItem iconString="Home">
                </MenuItem>
                </Link>
                <!--
                <MenuItem iconString="Explore" />
                -->
                <Link :href="route('pages.notifications')">
                <MenuItem iconString="Notifications">
                </MenuItem>
                </Link>
                <Link :href="route('pages.messages')">
                <MenuItem iconString="Messages">
                </MenuItem>
                </Link>
                <Link :href="route('pages.profile')">
                <MenuItem iconString="Profile">
                </MenuItem>
                </Link>
                <button @click="createPost = true"
                    class="lg:w-full mt-8 ml-2 text-white font-extrabold text-[22px] bg-[#1cef2e] p-3 px-3 rounded-full cursor-pointer">
                    <span class="lg:block hidden">Post</span>
                    <span class="block lg:hidden">
                        <Feather />
                    </span>
                </button>
                <div class="p-5 mt-10 text-black">
                    <div v-if="$page.props.auth.user">
                        <!-- indica el nombre del usuario y un botón para cerrar sesión-->
                        <p>Bienvenido, {{ $page.props.auth.user.name }}</p>
                        <button @click="logout">Cerrar sesión</button>
                    </div>
                    <div v-else>
                        <!-- El usuario no está autenticado. ESTO NO DEBERÍA DE VERSE, YA QUE HOME ESTÁ PROTEGIDO-->
                        <div class="flex p-7 justify-evenly">
                            <!-- botones de inicio de sesion y registro-->
                            <button @click="login"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Iniciar sesión
                            </button>
                            <button @click="register"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Registrarse
                            </button>
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
                                <div class="inline-block text-center border-b-4 border-b-[#1C9CEF] h-[60px]">
                                    <div class="my-auto mt-4">General</div>
                                </div>
                            </div>
                            <div
                                class="w-full h-[60px] text-gray-500 text-[17px] font-extrabold p-4 hover:bg-gray-500 hover:bg-opacity-30 cursor-pointer transition duration-200 ease-in-out">
                                <div class="text-center">Following</div>
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
                    <input type="text" placeholder="Search"
                        class="appeareance-none w-full border-0 py-2 bg-[#212327] text-gray-100 palceholder-gray-500 leading-tight focus:ring-0">
                </div>
                <div class="w-full mt-4 rounded-lg lg:block hidden bg-[#212327]">
                    <div class="w-full p-4 text-white font-extrabold text-[20px]">
                        Cuentas creadas recientemente
                    </div>
                    <div v-for="user in recentUsers" :key="user.id"
                        class="h-[80px] hover:bg-gray-800 cursor-pointer transition duration-200 ease-in-out flex items-center space-x-4 p-3">
                        <div
                            class="h-[80px] hover:bg-gray-800 cursor-pointer transition duration-200 ease-in-out flex items-center space-x-4 p-3">
                            <Link :href="`/profile/${user.id}`">
                            <div class="flex items-center space-x-4">
                                <img :src="'/storage/' + user.profile_photo_path" alt="Foto de perfil"
                                    class="h-10 w-10 rounded-full object-cover">
                                <div>
                                    <div class="font-bold text-white">{{ user.name }}</div>
                                    <div class="text-sm text-gray-400">{{ user.username }}</div>
                                </div>
                            </div>
                            </Link>
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2">
                                Seguir
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="seccionOverlay" v-if="createPost"
        class="fixed top-0 left-0 w-full h-screen bg-black bg-opacity-50 md:bg-gray-400 md:bg-opacity-30 md:p-3">
        <div class="md:max-w-2xl md:mx-auto md:mt-10 md:rounded-xl bg-black bg-opacity-100">
            <div
                class="flex w-full items-center justify-between md:inline-block p-2 m-2 rounded-full cursor-pointer opacity-100">
                <div @click="closeMessagebox()"
                    class="hover:bg-gray-800 inline-block p-2 rounded-full cursor-pointer text-white">
                    <Close fill="#FFFFFF" :size="28" class="md:block hidden" />
                    <ArrowLeft fillColor="#FFFFFF" :size="28" class="md:hidden block" />
                </div>
                <button @click="addPost()" :disabled="!post"
                    :class="post ? 'bg-[#1cef2e] text white' : 'bg-[#12D477] text-gray-400'"
                    class="md:block hidden font-extrabold text-[16px] p-1.5 px-4 rounded-full cursor-pointer mb-5 w-[98%]">
                    Post
                </button>
                <button @click="addPost()" :disabled="!post"
                    :class="post ? 'bg-[#1cef2e] text white' : 'bg-[#12D477] text-gray-400'"
                    class="md:hidden font-extrabold text-[16px] p-1.5 px-4 rounded-full cursor-pointer">
                    Post
                </button>
                <div class="w-full flex">
                    <div class="ml-3.5 mr-2">
                        <img class="rounded-full" width="55"
                            :src="'/storage/' + $page.props.auth.user.profile_photo_path" />
                    </div>
                    <div class="w-[calc(100%-100px)]">
                        <div>
                            <textarea :oninput="textAreaInput" cols="30" rows="4" placeholder="What's happening now?"
                                v-model="post" ref="textarea"
                                class="w-full bg-black border-0 mt-2 focus:ring-0 text-white text-[19px] font-extrabold min-h-[120px]"></textarea>
                        </div>
                        <div class="w-full">
                            <video controls v-if="uploadType === 'mp4'" :src="showUpload"
                                class="rounded-xl overflow-auto" />
                            <img v-else :src="showUpload" class="rounded-xl min-w-full">
                        </div>
                        <div class="flex py-2 items-center text-[#1C9CEF] font-extrabold">
                            <Earth class="pr-2 text-white" :size="20" />Everyone can reply
                            <div class="border-b border-b-gray-700"></div>
                            <div class="flex items-center justify-between py-2">
                                <div class="flex items-center">
                                    <div class="hover:bg-gray-800 inline-block p-2 rounded-full cursor-pointer">
                                        <label for="fileUpload" class="cursor-pointer">
                                            <ImageOutline fillColor="#1C9CEF" :size="25" />
                                        </label>
                                        <input type="file" id="fileUpload" class="hidden" @change="getFile" />
                                    </div>
                                    <div class="hover:bg-gray-800 inline-block p-2 rounded-full cursor-pointer">
                                        <FileGifBox fillcolor="#1C9CEF" :size="25" />
                                    </div>
                                    <div class="hover:bg-gray-800 inline-block p-2 rounded-full cursor-pointer">
                                        <Emoticon fillcolor="#1C9CEF" :size="25" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full">
                    <video controls v-if="uploadType === 'mp4'" :src="showUpload" class="rounded-xl overflow-auto" />
                    <img v-else :src="showUpload" class="rounded-xl min-w-full">
                </div>
            </div>
        </div>
    </div>
</template>

<style>
body {
    background-color: lightgreen;
}
</style>
