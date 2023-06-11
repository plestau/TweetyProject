<script setup>
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// detecta cambios de archivos multimedia
const handleFileChange = (e) => {
    form.profile_photo_path = e.target.files[0];
};

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
    biography: '',
    profile_photo_path: null,
});

const usernameTaken = ref('');

// Crear un getter para form.username
const username = computed(() => form.username);

watch(username, async (newUsername) => {
    if (newUsername) {
        try {
            const response = await axios.post('/api/check-username', { username: newUsername });
            usernameTaken.value = response.data.usernameError;
        } catch (error) {
            console.error(error);
        }
    } else {
        usernameTaken.value = '';
    }
});

const submit = () => {
    let data = new FormData();

    Object.keys(form).forEach((key) => {
        data.append(key, form[key]);
    });

    form.post(route('register'), data, {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="flex w-full h-full min-h-screen justify-center items-center bg-gradient-to-b from-amber-200 to-lime-500">
        <div class="flex flex-col lg:block" id="img">
            <h1 class="text-4xl font-extrabold mt-10 ml-40">¿Aún no estás registrado?</h1>
            <img src="../../../images/Login.jpg" alt="TweetyPie" class="m-3 scale-75 rounded-full">
        </div>
        <div class="w-full max-w-md mr-32 lg:mr-0">
            <Head title="Register" />
            <div class="bg-white shadow-md rounded-lg p-8">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                            autocomplete="name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="username" value="Username" />
                        <TextInput id="username" type="text" class="mt-1 block w-full" v-model="form.username" required
                            autofocus autocomplete="username" />
                        <InputError class="mt-2" :message="form.errors.username" />
                        <p v-if="usernameTaken" class="text-red-500">{{ usernameTaken }}</p>
                    </div>
                    <div class="mt-4">
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                            autocomplete="email" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="password" value="Password" />
                        <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                            autocomplete="new-password" />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                            v-model="form.password_confirmation" required autocomplete="new-password" />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="biography" value="biography" />
                        <TextInput id="biography" type="text" class="mt-1 block w-full" v-model="form.biography" required
                            autofocus autocomplete="biography" />
                        <InputError class="mt-2" :message="form.errors.biography" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="profile_photo_path" value="Foto de perfil" />
                        <TextInput id="profile_photo_path" type="file" class="mt-1 block w-full" @change="handleFileChange"
                            autofocus required autocomplete="profile_photo_path" accept="image/*" />
                        <InputError class="mt-2" :message="form.errors.profile_photo_path" />
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <Link :href="route('login')"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Already registered?
                        </Link>
                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Register
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
