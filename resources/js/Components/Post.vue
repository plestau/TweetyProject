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

let showComments = ref(false);

let userHasDisliked = ref(false);
let userHasLiked = ref(false);

let createComment = ref(false);
let newComment = ref('');

async function addComment(postId) {
  const comment = newComment.value;
  try {
    const response = await Inertia.post(`/posts/${postId}/comment`, { comment });
      props.post.comments.push({ comment });
      createComment.value = false;
      newComment.value = '';
  } catch (error) {
    console.error(error);
  }
}



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

const truncate = (text, maxLength) => {
  if (text.length <= maxLength) {
    return text;
  } else {
    return text.substring(0, maxLength) + '...';
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
            <span class="text-red-600 font-extrabold">Delete</span>
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
        <HeartOutline :class="{ 'text-red-500': userHasLiked }" fillcolor="#5e5c5c" :size="18" @click="likeOrUnlike"
          class="transition-all duration-500 ease-in-out transform hover:scale-110" />
        <span class="text-xs font-extrabold text-[#5e5c5c] ml-3">
          {{ post.likes }}
        </span>
      </div>

      <div class="flex">
        <ThumbDown :class="{ 'text-blue-500': userHasDisliked }" fillcolor="#5e5c5c" :size="18"
          @click="disLikeOrUnDislike" class="transition-all duration-500 ease-in-out transform hover:scale-110" />
      </div>


      <div class="flex">
        <span class="text-xs font-extrabold text-[#5e5c5c] ml-3 cursor-pointer mr-4"
          @click="showComments = !showComments">
          {{ post.comments.length }}
        </span>
        <MessageOutline fillcolor="#5e5c5c" class="cursor-pointer ml-2" :size="18"
          @click="createComment = !createComment" />

        <transition name="slide-fade">
          <div v-if="createComment" class="w-full mt-4 bg-black rounded-lg p-2">
            <textarea v-model="newComment" placeholder="Escribe tu comentario..."
              class="w-full bg-black border-0 focus:ring-0 text-white text-[19px] font-extrabold min-h-[120px]"></textarea>
            <button @click="addComment(post.id)"
              class="bg-[#1cef2e] text white font-extrabold text-[16px] p-1.5 px-4 rounded-full cursor-pointer">Comentar</button>
          </div>
        </transition>
      </div>
    </div>
    <transition name="slide-fade">
      <div v-if="showComments" class="w-full mt-4 bg-black rounded-lg">
        <h3 class="text-white font-extrabold text-[20px] p-2">Comentarios</h3>
        <div v-for="comment in post.comments" :key="comment.id" class="flex items-center p-2">
          <div class="flex items-center">
            <Link :href="`/profile/${comment.user_id}`">
              <img class="rounded-full m-2 mt-3" width="50" :src="'/storage/' + comment.user_image">
            </Link>
            <div>
              <p class="text-white">{{ comment.name }}</p>
              <p class="text-white">{{ comment.username }}</p>
              <span class="text-xs text-gray-500">{{ timeSince(comment.created_at) }}</span>
            </div>
          </div>
          <div class="ml-2">
            <p class="text-white">{{ truncate(comment.comment, 50) }}</p>
          </div>
          <Link v-if="$page.props.auth.user.id === comment.user_id" as="button" method="delete"
            :href="route('post.comment.destroy', { post_id: post.id, id: comment.id })" class="flex items-center cursor-pointer">
            <TrashCanOutline class="pr-3" fillColor="#DC2626" :size="18" />
          </Link>
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.slide-fade-enter-active {
  transition: all .3s ease;
}

.slide-fade-leave-active {
  transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}

.slide-fade-enter,
.slide-fade-leave-to

/* .slide-fade-leave-active below version 2.1.8 */
{
  transform: translateY(10px);
  opacity: 0;
}
</style>
