<script setup>
import { ref, onMounted, watch } from 'vue';
import { Editor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import TextAlign from '@tiptap/extension-text-align';
import Color from '@tiptap/extension-color';
import TextStyle from '@tiptap/extension-text-style';

// Extension CustomBackgroundColor to handle background color
const CustomBackgroundColor = TextStyle.extend({
  addAttributes() {
    return {
      backgroundColor: {
        default: null,
        parseHTML: element => element.style.backgroundColor || null,
        renderHTML: attributes => {
          if (!attributes.backgroundColor) {
            return {};
          }
          return { style: `background-color: ${attributes.backgroundColor}` };
        },
      },
    };
  },
});

const editor = ref(null);
const previewContent = ref('');

const props = defineProps({
  defaultContent: {
    type: String,
    default: ""
  }
});

const emit = defineEmits(['updateContent']);

onMounted(() => {
  editor.value = new Editor({
    content: props.defaultContent,
    extensions: [
      StarterKit,
      Underline,
      TextStyle,
      Color.configure({ types: ['textStyle'] }),
      TextAlign.configure({ types: ['heading', 'paragraph'] }),
      CustomBackgroundColor, // Use CustomBackgroundColor
    ],
    editorProps: {
      attributes: {
        class: 'min-h-96 focus:outline-none',
      },
    },
    onUpdate({ editor }) {
      const content = editor.getHTML();
      emit('updateContent', content);
      previewContent.value = content;
    }
  });

  previewContent.value = props.defaultContent;
});

watch(() => props.defaultContent, (newContent) => {
  if (editor.value) {
    editor.value.commands.setContent(newContent);
    previewContent.value = newContent;
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

const setTextColor = (color) => {
  editor.value.chain().focus().setColor(color).run();
};

const setTextBackgroundColor = (color) => {
  editor.value.chain().focus().setMark('textStyle', { backgroundColor: color }).run();
};

const setTextAlign = (alignment) => {
  editor.value.chain().focus().setTextAlign(alignment).run();
};
</script>

<template>
  <div>
    <!-- Toolbar -->
    <div class="flex gap-2 mb-2">
      <!-- Bold -->
      <button type="button" @click="toggleBold" :class="{ active: editor?.isActive('bold') }">
        <!-- SVG for Bold -->
      </button>

      <!-- Italic -->
      <button type="button" @click="toggleItalic" :class="{ active: editor?.isActive('italic') }">
        <!-- SVG for Italic -->
      </button>

      <!-- Underline -->
      <button type="button" @click="toggleUnderline" :class="{ active: editor?.isActive('underline') }">
        <!-- SVG for Underline -->
      </button>

      <!-- Text Color -->
      <input type="color" @input="setTextColor($event.target.value)" title="Text Color" class="border rounded-md w-8 h-8 p-0.5"/>

      <!-- Background Color -->
      <input type="color" @input="setTextBackgroundColor($event.target.value)" title="Background Color" class="border rounded-md w-8 h-8 p-0.5"/>

      <!-- Text Alignment -->
      <select @change="setTextAlign($event.target.value)" class="border rounded-md">
        <option value="left">Left</option>
        <option value="center">Center</option>
        <option value="right">Right</option>
        <option value="justify">Justify</option>
      </select>
    </div>

    <EditorContent :editor="editor" class="min-h-[400px] border border-gray-300 p-2 rounded-md"/>

    <div v-html="previewContent" class="mt-4 p-4 border border-gray-300 rounded-md bg-white"></div>
  </div>
</template>
