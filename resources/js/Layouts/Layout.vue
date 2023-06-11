<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';
import MenuItem from '@/Components/MenuItem.vue';
import { Inertia } from '@inertiajs/inertia';

let isDarkMode = ref(JSON.parse(localStorage.getItem('dark_mode')) || false);
let menuOpen = ref(false);
let showDeleteConfirm = ref(false);
let settingsMenuOpen = ref(false);
let oldPassword = ref('');
let newPassword = ref('');

const changePassword = () => {
    // Enviar una solicitud al backend para cambiar la contraseña
    Inertia.post(route('profile.changePassword'), {
        oldPassword: oldPassword.value,
        newPassword: newPassword.value,
    });
};

let changePasswordVisible = ref(false);

const updateLocalStorage = () => {
    localStorage.setItem('dark_mode', JSON.stringify(isDarkMode.value));
};

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    updateLocalStorage();
};

const deleteAccount = () => {
    showDeleteConfirm.value = true;
};

const deleteConfirmed = async () => {
    const password = prompt('Escribe tu contraseña para confirmar');
    try {
        // Realizar una solicitud al backend para verificar la contraseña
        const response = await axios.post(route('profile.checkPassword'), { password });

        if (response.data.success) {
            // La contraseña coincide, eliminar la cuenta
            Inertia.post(route('profile.destroy'), { _method: 'delete' });
        } else {
            alert('La contraseña no coincide');
        }
    } catch (error) {
        // Manejar errores de la solicitud
        console.error(error);
        alert('Ocurrió un error al verificar la contraseña');
    }
};

const toggleSettingsMenu = () => {
    settingsMenuOpen.value = !settingsMenuOpen.value;
};

onMounted(() => {
    window.addEventListener('beforeunload', updateLocalStorage);
});

onUnmounted(() => {
    window.removeEventListener('beforeunload', updateLocalStorage);
});

const logout = () => {
    Inertia.post(route('logout'));
};
</script>
<template>
    <div :class="{ 'w-full text-white bg-black': isDarkMode, 'w-full text-black bg-white': !isDarkMode }">
        <div id="nav" class="max-w-[1400px] flex mx-auto">
            <div :class="{ 'lg:w-3/12 w-full h-screen max-w-[350px] lg:px-4 lg:mx-auto': !menuOpen, 'hidden lg:block': !menuOpen, 'block': menuOpen }"
                @click="menuOpen = false">
                <div>
                    <div>
                        <img v-if="isDarkMode" src="../../images/Logo-darkMode.png" alt="Logo dark mode">
                        <img v-else src="../../images/Logo-noDarkMode.png" alt="Logo light mode">
                    </div>
                </div>
                <Link :href="route('post.home')">
                <MenuItem iconString="Home" :isDarkMode="isDarkMode">
                </MenuItem>
                </Link>
                <Link :href="route('pages.profile')">
                <MenuItem iconString="Profile" :isDarkMode="isDarkMode">
                </MenuItem>
                </Link>
                <p class="hidden sm:block text-sm font-medium m-5"
                    :class="{ 'text-white': isDarkMode, 'text-gray-700': !isDarkMode }">Bienvenid@, {{
                        $page.props.auth.user.name }}</p>
                <button @click="logout"
                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md mb-3 sm:px-4 sm:py-2 sm:text-sm sm:mb-0"
                    :class="{ 'text-white': !isDarkMode, 'text-black': isDarkMode, 'bg-red-600': !isDarkMode, 'bg-blue-600': isDarkMode, 'hover:bg-red-700': !isDarkMode, 'hover:bg-blue-700': isDarkMode, 'focus:ring-red-500': !isDarkMode, 'focus:ring-blue-500': isDarkMode }">
                    Cerrar sesión
                </button>
                <!-- Setting Button -->
                <div @click="toggleSettingsMenu" class="settings-icon w-5 mt-10 cursor-pointer">⚙️</div>
                <!-- Delete Account Button and Confirmation-->
                <div v-if="settingsMenuOpen"> <!-- New: Show this div when settings menu is open -->
                    <!-- Dark Mode Toggle Button -->
                    <div class="w-14 h-8 flex items-center bg-gray-300 rounded-full p-1 mt-4 duration-300 cursor-pointer"
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
                    <!-- Delete Account Button and Confirmation -->
                    <div class="flex flex-col">
                        <button @click="deleteAccount" class="delete-account mt-10 mb-10">Eliminar cuenta</button>
                        <div v-if="showDeleteConfirm" class="delete-confirm mb-10 bg-red-700">
                            ¿Estás seguro de que quieres eliminar tu cuenta?
                            <div class="flex justify-between mt-4">
                                <button @click="showDeleteConfirm = false" class="cancel-delete">Cancelar</button>
                                <button @click="deleteConfirmed" class="confirm-delete">Eliminar</button>
                            </div>
                        </div>
                        <!-- Change Password Section -->
                        <div v-if="changePasswordVisible" class="flex flex-col mt-4">
                            <h3 class="mb-2">Cambiar contraseña</h3>
                            <input v-model="oldPassword" type="password" placeholder="Contraseña antigua"
                                class="password-input" />
                            <input v-model="newPassword" type="password" placeholder="Contraseña nueva"
                                class="password-input" />
                            <div class="flex justify-between">
                                <button @click="changePassword" class="change-password btn">Guardar cambios</button>
                                <button @click="changePasswordVisible = false"
                                    class="cancel-change-password btn">Cancelar</button>
                            </div>
                        </div>
                        <button v-else @click="changePasswordVisible = true" class="change-password-button btn">Cambiar
                            contraseña</button>
                    </div>
                </div>
            </div>
            <!-- Contenido principal -->
            <div :class="{ 'lg:w-9/12 w-full': true, 'hidden lg:block': menuOpen, 'block': !menuOpen }">
                <button class="fixed top-0 z-50 lg:hidden" @click="menuOpen = !menuOpen">
                    Menú
                </button>
                <slot />
            </div>
        </div>
    </div>
</template>
