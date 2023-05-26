<template>
    <div class="container mx-auto">
        <div class="max-w-sm mx-auto mt-32">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="mb-4">
                    <h1 class="text-2xl font-bold text-gray-700 text-center">Iniciar sesión</h1>
                </div>
                <Login :errors="errors" :session="session" />
            </div>
        </div>
    </div>
</template>

<script>
import Login from '@/Pages/Auth/Login.vue'; // Importamos el componente Login

export default {
    components: {
        Login, // Registramos el componente Login
    },

    props: {
        errors: Object,
        session: Object,
    },
};
</script>
