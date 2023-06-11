<script setup>
import { toRefs } from 'vue'
import Home from 'vue-material-design-icons/Home.vue';
import Pound from 'vue-material-design-icons/Pound.vue';
import BellOutline from 'vue-material-design-icons/BellOutline.vue';
import MailOutline from 'vue-material-design-icons/EmailOutline.vue';
import AccountOutline from 'vue-material-design-icons/AccountOutline.vue';

const props = defineProps({
    iconString: String,
    isDarkMode: Boolean
});

const { iconString } = toRefs(props);

let icon = null;

if (iconString.value === "Home") icon = Home;
if (iconString.value === "Explore") icon = Pound;
if (iconString.value === "Notifications") icon = BellOutline;
if (iconString.value === "Messages") icon = MailOutline;
if (iconString.value === "Profile") icon = AccountOutline;
</script>

<template>
    <div class="w-full lg:inline-block p-2 px-4 lg:pr-6 rounded-full cursor-pointer transition duration-200 ease-in-out"
        :class="{ 'hover:bg-gray-900': isDarkMode, 'hover:bg-gray-100': !isDarkMode }">
        <div class="flex items-center">
            <component :is="icon" fillcolor="#00ff00" :size="30" />
            <span class="lg:block hidden font-extrabold text-[23px] pl-4 mt-0.5"
                :class="{ 'text-white': isDarkMode, 'text-black': !isDarkMode }">
                {{ iconString }}
            </span>
        </div>
    </div>
</template>