<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <Head title="Log in" />
  <div class="bg-gradient-to-b from-blue-700 to-lime-400 min-h-screen flex justify-center items-center">
    <div class="container mx-auto h-full flex justify-center items-center">
      <div class="flex justify-center items-center">
        <div class="form w-full max-w-lg leading-loose">
          <img src="../../../images/Logo-noDarkMode.png" alt="login" class="mx-auto pr-36" />
          <form class="max-w-sm m-4 p-10 bg-white bg-opacity-25 rounded shadow-xl" @submit.prevent="submit">
            <p class="text-white text-center text-lg font-bold">LOGIN</p>
            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
              {{ status }}
            </div>
            <div>
              <InputLabel for="email" value="Email" />
              <TextInput id="email" type="email" class="mt-1 block w-full px-5 py-1 text-gray-700 bg-gray-200 rounded"
                v-model="form.email" required autofocus autocomplete="username" />
              <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="mt-4">
              <InputLabel for="password" value="Password" />
              <TextInput id="password" type="password"
                class="mt-1 block w-full px-5 py-1 text-gray-700 bg-gray-200 rounded" v-model="form.password" required
                autocomplete="current-password" />
              <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="block mt-4">
              <label class="flex items-center">
                <Checkbox name="remember" v-model:checked="form.remember" />
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
              </label>
            </div>
            <div class="mt-4 items-center justify-between">
              <PrimaryButton class="px-4 py-1 rounded text-black font-light tracking-wider bg-gray-900 hover:bg-gray-800"
                :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Log in
              </PrimaryButton>
            </div>
          </form>
          <div class="text-center">
            Don't have an account?
            <Link href="/register" class="text-blue-500 hover:text-blue-700 text-sm">
              Register
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
