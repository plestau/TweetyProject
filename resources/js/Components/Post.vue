<script setup>
import { Inertia } from '@inertiajs/inertia';
import { onMounted, ref } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';
import HeartOutline from 'vue-material-design-icons/HeartOutline.vue';
import ChartBart from 'vue-material-design-icons/ChartBar.vue';
import MessageOutline from 'vue-material-design-icons/MessageOutline.vue';
import ThumbDown from 'vue-material-design-icons/ThumbDown.vue';
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue';
import TrashCanOutline from 'vue-material-design-icons/TrashCanOutline.vue';
import moment from 'moment';
import axios from 'axios';

const timeSince = (datetime) => {
  return moment(datetime).fromNow();
}

const props = defineProps({
  post: Object
});

let userHasDisliked = ref(false);
let userHasLiked = ref(false);

onMounted(() => {
  userHasLiked.value = props.post.hasLiked;
  userHasDisliked.value = props.post.hasDisliked;
});

const likeOrUnlike = async () => {
  if (userHasLiked.value) {
    await unlike();
  } else {
    await like();
    if (userHasDisliked.value) {
      await undislike();
    }
  }
};

const disLikeOrUnDislike = async () => {
  if (userHasDisliked.value) {
    await undislike();
  } else {
    await dislike();
    if (userHasLiked.value) {
      await unlike();
    }
  }
};

const like = async () => {
  try {
    const response = await axios.post(`/post/${props.post.id}/like`);
    props.post.likes = response.data.likes;
    userHasLiked.value = true;
    userHasDisliked.value = false;
  } catch (error) {
    console.error(error);
  }
};

const unlike = async () => {
  try {
    const response = await axios.post(`/post/${props.post.id}/unlike`);
    props.post.likes = response.data.likes;
    userHasLiked.value = false;
  } catch (error) {
    console.error(error);
  }
};

const dislike = async () => {
  try {
    const response = await axios.post(`/post/${props.post.id}/dislike`);
    props.post.likes = response.data.likes;
    userHasLiked.value = false;
    userHasDisliked.value = true;
  } catch (error) {
    console.error(error);
  }
};

const undislike = async () => {
  try {
    const response = await axios.post(`/post/${props.post.id}/undislike`);
    props.post.likes = response.data.likes;
    userHasDisliked.value = false;
  } catch (error) {
    console.error(error);
  }
};


let openOptions = ref(false);
</script>

<template>
  <div class="min-w-[60px]">
    <Link :href="`/profile/${post.user_id}`">
      <img class="rounded-full m-2 mt-3" width="50" :src="'/storage/' + post.image">
    </Link>
  </div>
  <div class="p-2 w-full">
    <div class="font-extrabold flex items-center justify-between mt-0.5 mb-1.5">
      <div class="flex items-center">
        <div>{{ post.name }}</div>
        <span class="font-[300] text-[15px] text-gray-500 pl-2">{{ post.handle }}</span>
      </div>
      <div class="text-xs text-gray-500">{{ timeSince(post.created_at) }}</div>
      <div v-if="$page.props.auth.user.id === post.user_id"
        class="hover:bg-gray-800 rounded-full cursor-pointer relative">
        <button type="button" class="bock p-2">
          <DotsHorizontal @click="openOptions = !openOptions" />
        </button>
        <div v-if="openOptions"
          class="absolute mt-1 p-3 right-0 w-[300px] bg-black border border-gray-700 rounded-lg shadow-lg">
          <Link as="button" method="delete" :href="route('post.destroy', { id: post.id })"
            class="flex items-center cursor-pointer">
          <TrashCanOutline class="pr-3" fillColor="#DC2626" :size="18" />
          <span class="text-red-600 font-extrabold">
            Delete
          </span>
          </Link>
        </div>
      </div>
    </div>
    <div class="pb-3">{{ post.post }}</div>
    <div v-if="post.file">
      <div v-if="!post.is_video" class="rounded-xl">
        <img :src="post.file" class="mt-2 object-fill rounded-xl w-full">
      </div>
      <div v-else>
        <video class="rounded-xl" :src="post.file" controls></video>
      </div>
    </div>
    <div class="flex items-center justify-between mt-4 w-4/5">
      <div class="flex">
        <MessageOutline fillcolor="#5e5c5c" :size="18" />
        <span class="text-xs font-extrabold text-[#5e5c5c] ml-3">
          {{ post.comments }}
        </span>
      </div>
      <div class="flex">
        <HeartOutline :class="{ 'text-red-500': userHasLiked }" fillcolor="#5e5c5c" :size="18" @click="likeOrUnlike"
          class="transition-all duration-500 ease-in-out transform hover:scale-110" />
        <span class="text-xs font-extrabold text-[#5e5c5c] ml-3">
          {{ post.likes }}
        </span>
      </div>

      <div class="flex">
        <ThumbDown :class="{ 'text-blue-500': userHasDisliked }" fillcolor="#5e5c5c" :size="18" @click="disLikeOrUnDislike"
          class="transition-all duration-500 ease-in-out transform hover:scale-110" />
      </div>


      <div class="flex">
        <ChartBart fillcolor="#5e5c5c" :size="18" />
        <span class="text-xs font-extrabold text-[#5e5c5c] ml-3">
          {{ post.analytics }}
        </span>
      </div>
    </div>
  </div>
</template>
