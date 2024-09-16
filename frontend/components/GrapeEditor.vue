<script setup>
import {ref, onMounted, onBeforeUnmount, watch} from 'vue';
import grapesjs from 'grapesjs';
import 'grapesjs/dist/css/grapes.min.css';

// Référence à l'éditeur et au conteneur
const editor = ref(null);
const editorContainer = ref(null);
const inlineHtml = ref('');

// Propriétés du composant
const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  defaultContent: {
    type: String
  }
});

// Émetteurs d'événements
const emit = defineEmits(['update:modelValue']);

// Charger la police Inter depuis Google Fonts
const loadGoogleFonts = () => {
  const link = document.createElement('link');
  link.href = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap';
  link.rel = 'stylesheet';
  document.head.appendChild(link);

};

// Initialiser l'éditeur GrapesJS
onMounted(() => {

  loadGoogleFonts();

  editor.value = grapesjs.init({
    container: editorContainer.value, // Référence DOM du conteneur de l'éditeur
    fromElement: true,
    width: 'auto',
    height: '700px',
    storageManager: false, // Désactiver le gestionnaire de stockage
    blockManager: {
      appendTo: '#blocks', // ID de conteneur des blocs
      blocks: [
        {
          id: 'section',
          label: '<b>Section</b>',
          content: `
            <section style="width: 100%; padding: 50px; background-color: #f0f0f0;">
              <h1 style="text-align: center; font-family: 'Inter', sans-serif;">This is a Section</h1>
            </section>
          `,
        },
        {
          id: 'div',
          label: 'Div',
          content: `
            <div style="padding: 20px; background-color: #bada55; font-family: 'Inter', sans-serif;">
              Custom Div Block
            </div>
          `,
        },
        {
          id: 'text',
          label: 'Text',
          content: '<p style="font-family: \'Inter\', sans-serif;">Insert your text here</p>',
        },
        {
          id: 'image',
          label: 'Image',
          content: '<img src="https://via.placeholder.com/250x150" alt="Placeholder Image" />',
        },
      ],
    },
    styleManager: {
      appendTo: '#style-manager',
      sectors: [
        {
          name: 'General',
          open: false,
          buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom'],
        },
        {
          name: 'Dimension',
          open: false,
          buildProps: ['width', 'height', 'max-width', 'min-height', 'margin', 'padding'],
        },
        {
          name: 'Polices',
          open: true,
          buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align'],
          properties: [
            {
              name: 'Typographie',
              property: 'font-family',
              type: 'select',
              default: 'Inter, sans-serif',
              options: [
                { name: 'Inter', value: 'Inter, sans-serif' },
                { name: 'Arial', value: 'Arial, Helvetica, sans-serif' },
                { name: 'Georgia', value: 'Georgia, serif' },
                { name: 'Times New Roman', value: 'Times New Roman, Times, serif' },
              ],
            },
          ],
        },
        {
          name: 'Decorations',
          open: false,
          buildProps: ['background-color', 'border-radius', 'border', 'box-shadow', 'background'],
        },
        {
          name: 'Extra',
          open: false,
          buildProps: ['opacity', 'transition'],
        },
      ],
    },

  });



  editor.value.on('update', () => {
    getInlineHTML();
  });

  if (props.defaultContent) {
    editor.value.setComponents(props.defaultContent); // Assurez-vous que le contenu par défaut est chargé
  }

  if (props.modelValue) {
    editor.value.setComponents(props.modelValue);
  }

  // Ajouter un bouton pour centrer le texte
  editor.value.Panels.addButton('options', {
    id: 'center-text',
    className: 'fa fa-align-center',
    command: 'center-text',
    attributes: { title: 'Center Text' },
  });

  // Commande pour centrer le texte
  editor.value.Commands.add('center-text', {
    run(editor) {
      const selected = editor.getSelected();
      if (selected) {
        selected.addStyle({ 'text-align': 'center' });
      }
    },
  });
});




// Fonction pour récupérer le HTML avec les styles en ligne
const getInlineHTML = () => {
  if (!editor.value) return;

  // Récupérer le HTML et CSS
  const html = editor.value.getHtml();
  const css = editor.value.getCss();

  // Créer un conteneur DOM temporaire
  const tempDiv = document.createElement('div');
  tempDiv.innerHTML = html;

  // Appliquer les styles CSS en ligne
  const styleSheet = new CSSStyleSheet();
  styleSheet.replaceSync(css);
  for (const rule of styleSheet.cssRules) {
    const elements = tempDiv.querySelectorAll(rule.selectorText);
    elements.forEach((element) => {
      element.style.cssText += rule.style.cssText;
    });
  }

  // Récupérer le contenu avec les styles inline
  inlineHtml.value = tempDiv.innerHTML;
  emit('update:modelValue', inlineHtml.value);
};

// Libérer les ressources quand le composant est démonté
onBeforeUnmount(() => {
  if (editor.value) {
    editor.value.destroy();
  }
});
</script>

<template>
  <div>
    <!-- Toolbar for actions -->
    <div class="">
    </div>

    <!-- Block Manager for drag and drop components -->
    <div id="blocks" class="panel__basic-actions"></div>

    <!-- GrapesJS Editor Container -->
    <div ref="editorContainer" class="editor-container min-h-[700px] border border-gray-300 p-2 rounded-md"></div>

    <!-- Style Manager -->
    <div id="style-manager"></div>

  </div>
</template>

<style scoped>
.editor-container {
  min-height: 700px;
}
</style>
