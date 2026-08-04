/**
 * CKEditor 5 custom build — integrated with Vite + Livewire 3 + Alpine.js.
 *
 * Field: "Tentang Kami" (Company Profile - About)
 * Toolbar: Heading, Bold, Italic, Underline, Bulleted List, Numbered List,
 *          Block Quote, Link, Undo, Redo
 */
import {
    ClassicEditor,
    Essentials,
    Paragraph,
    Heading,
    Bold,
    Italic,
    Underline,
    Link,
    List,
    BlockQuote,
    Undo,
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';

/**
 * Create a CKEditor Classic instance on the given element.
 *
 * @param {HTMLElement} element        The container element (CKEditor replaces it).
 * @param {string} initialData         The initial HTML content.
 * @param {Function} onDataChange      Callback invoked whenever the editor data changes.
 * @returns {Promise<ClassicEditor>}
 */
export function createAboutEditor(element, initialData, onDataChange) {
    return ClassicEditor.create(element, {
        plugins: [
            Essentials,
            Paragraph,
            Heading,
            Bold,
            Italic,
            Underline,
            Link,
            List,
            BlockQuote,
            Undo,
        ],
        toolbar: [
            'heading',
            '|',
            'bold',
            'italic',
            'underline',
            '|',
            'bulletedList',
            'numberedList',
            '|',
            'blockQuote',
            'link',
            '|',
            'undo',
            'redo',
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraf', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h2', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h3', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h4', title: 'Heading 3', class: 'ck-heading_heading3' },
            ],
        },
        link: {
            addTargetToExternalLinks: true,
            defaultProtocol: 'https://',
        },
        initialData: initialData,
        licenseKey: 'GPL',
    }).then((editor) => {
        // Sync editor data back to the Livewire property whenever it changes.
        editor.model.document.on('change:data', () => {
            onDataChange(editor.getData());
        });

        return editor;
    });
}

/**
 * Register the Alpine component for the "Tentang Kami" editor.
 *
 * Usage in blade:
 *   <div wire:ignore x-data="aboutEditor(@js($about))" x-init="initEditor"></div>
 */
document.addEventListener('alpine:init', () => {
    window.Alpine.data('aboutEditor', (initialData = '') => ({
        initEditor() {
            const element = this.$el;
            const wire = this.$wire;

            createAboutEditor(element, initialData, (data) => {
                wire.set('about', data);
            });
        },
    }));
});
