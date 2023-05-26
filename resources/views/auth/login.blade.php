<template>
    <div class="container mx-auto">
        <div class="max-w-sm mx-auto mt-32">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="mb-4">
                    <h1 class="text-2xl font-bold text-gray-700 text-center">Iniciar sesión</h1>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const Login = require('@/Pages/Auth/Login.vue'); // Importamos el componente Login con CommonJS

module.exports = {
    components: {
        Login, // Registramos el componente Login
    },

    props: {
        errors: Object,
        session: Object,
    },
};

</script>
