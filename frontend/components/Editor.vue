<script setup>
import { ref, onMounted, watch } from 'vue';
import { Editor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';

const editor = ref(null);

const props = defineProps({
  defaultContent: {
    type: String,
    default: ""
  }
})

const emit = defineEmits(['updateContent']);

onMounted(() => {
  editor.value = new Editor({
    content: props.defaultContent,
    extensions: [StarterKit, Underline],
    editorProps: {
      attributes: {
        class: 'min-h-96 focus:outline-none',
      },
    },
    onUpdate({editor}) {
      emit('updateContent', editor.getHTML());
    }
  });
});

watch(() => props.defaultContent, (newContent) => {
  if (editor.value && newContent !== editor.value.getHTML()) {
    editor.value.commands.setContent(newContent);
  }
});

const toggleBold = () => {
  editor.value.chain().focus().toggleBold().run();
};

const toggleUnderline = () => {
  editor.value.chain().focus().toggleUnderline().run();
};

const toggleItalic = () => {
  editor.value.chain().focus().toggleItalic().run();
};
</script>

<template>
  <div>
    <div class="flex gap-2 mb-2">
      <button type="button" @click="toggleBold" :class="{ active: editor?.isActive('bold') }">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="size-6">
          <path stroke-linejoin="round"
                d="M6.75 3.744h-.753v8.25h7.125a4.125 4.125 0 0 0 0-8.25H6.75Zm0 0v.38m0 16.122h6.747a4.5 4.5 0 0 0 0-9.001h-7.5v9h.753Zm0 0v-.37m0-15.751h6a3.75 3.75 0 1 1 0 7.5h-6m0-7.5v7.5m0 0v8.25m0-8.25h6.375a4.125 4.125 0 0 1 0 8.25H6.75m.747-15.38h4.875a3.375 3.375 0 0 1 0 6.75H7.497v-6.75Zm0 7.5h5.25a3.75 3.75 0 0 1 0 7.5h-5.25v-7.5Z"/>
        </svg>
      </button>
      <button type="button" @click="toggleItalic" :class="{ active: editor?.isActive('italic') }">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M5.248 20.246H9.05m0 0h3.696m-3.696 0 5.893-16.502m0 0h-3.697m3.697 0h3.803"/>
        </svg>
      </button>
      <button type="button" @click="toggleUnderline" :class="{ active: editor?.isActive('underline') }">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M17.995 3.744v7.5a6 6 0 1 1-12 0v-7.5m-2.25 16.502h16.5"/>
        </svg>
      </button>
    </div>

    <EditorContent :editor="editor" class="min-h-[400px] border border-gray-300 p-2 rounded-md"/>
  </div>
</template>
