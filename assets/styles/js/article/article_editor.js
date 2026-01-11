import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import { TextStyle } from '@tiptap/extension-text-style';
import { Color } from '@tiptap/extension-color';
import TextAlign from '@tiptap/extension-text-align';
import Link from '@tiptap/extension-link';

import '../../css/article/form.css';

document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.querySelector('#article_content');
    const editorEl = document.querySelector('#editor');
    const toolbar = document.querySelector('#editor-toolbar');
    const colorPicker = document.querySelector('#color-picker');

    const headingBtn = document.querySelector('#heading-btn')
    const headingMenu = document.querySelector('#heading-menu')
    const listBtn = document.querySelector('#list-btn')
    const listMenu = document.querySelector('#list-menu')

    if (!textarea || !editorEl || !toolbar) return;

    const editor = new Editor({
        element: editorEl,
        extensions: [
            StarterKit.configure({
                link: false
            }),
            TextStyle,
            Color,
            Image.configure({
                inline: false,
                allowBase64: true,
            }),
            TextAlign.configure({
                types: ['heading', 'paragraph', 'blockquote', 'listItem']
            }),
            Link.configure({
                openOnClick: false,
                autolink: true,
                linkOnPaste: true,
                HTMLAttributes: {
                    class: 'text-primary underline',
                    rel: 'noopener noreferrer',
                    target: '_blank'
                },
                validate: href => /^https?:\/\//.test(href)
            })
        ],
        content: textarea.value || '<p></p>',
        onUpdate({ editor }) {
            textarea.value = editor.getHTML()
        },
    });

    // 🧠 Toolbar actions
    toolbar.addEventListener('click', (e) => {
        const action = e.target.dataset.action;
        if (!action) return;

        editor.chain().focus();

        switch (action) {
            case 'h1':
                editor.chain().toggleHeading({ level: 1 }).run()
                break
            case 'h2':
                editor.chain().toggleHeading({ level: 2 }).run()
                break
            case 'h3':
                editor.chain().toggleHeading({ level: 3 }).run()
                break
            case 'h4':
                editor.chain().toggleHeading({ level: 4 }).run()
                break
            case 'bulletList':
                editor.chain().toggleBulletList().run()
                break
            case 'orderedList':
                editor.chain().toggleOrderedList().run()
                break
            case 'bold':
                editor.chain().toggleBold().run()
                break
            case 'italic':
                editor.chain().toggleItalic().run()
                break
            case 'underline':
                editor.chain().toggleUnderline().run()
                break
            case 'strike':
                editor.chain().toggleStrike().run()
                break
            case 'link': {
                const previousUrl = editor.getAttributes('link').href
                const url = window.prompt('URL du lien', previousUrl)

                if (url === null) {
                    return
                }

                if (url === '') {
                    editor.chain().focus().unsetLink().run()
                    return
                }

                editor.chain()
                    .focus()
                    .extendMarkRange('link')
                    .setLink({ href: url })
                    .run()
                break
            }
            case 'alignLeft':
                editor.chain().focus().setTextAlign('left').run()
                break
            case 'alignCenter':
                editor.chain().focus().setTextAlign('center').run()
                break
            case 'alignRight':
                editor.chain().focus().setTextAlign('right').run()
                break
            case 'alignJustify':
                editor.chain().focus().setTextAlign('justify').run()
                break
            case 'codeBlock':
                editor.chain().toggleCodeBlock().run()
                break
            case 'blockquote':
                editor.chain().toggleBlockquote().run()
                break
            case 'horizontalRule':
                editor.chain().focus().setHorizontalRule().run()
                break
            case 'image': {
                const url = prompt('URL de l’image')
                if (url) {
                    editor.chain().setImage({ src: url }).run()
                }
                break
            }
        }
    });

    // 🎨 Color picker
    colorPicker.addEventListener('input', (e) => {
        editor.chain().focus().setColor(e.target.value).run()
    });

    const updateToolbar = () => {
        toolbar.querySelector('[data-action="h1"]')
            ?.classList.toggle('btn-primary', editor.isActive('heading', { level: 1 }));
        toolbar.querySelector('[data-action="h2"]')
            ?.classList.toggle('btn-primary', editor.isActive('heading', { level: 2 }));
        toolbar.querySelector('[data-action="h3"]')
            ?.classList.toggle('btn-primary', editor.isActive('heading', { level: 3 }));
        toolbar.querySelector('[data-action="h4"]')
            ?.classList.toggle('btn-primary', editor.isActive('heading', { level: 4 }));

        toolbar.querySelector('[data-action="bulletList"]')
            ?.classList.toggle('btn-primary', editor.isActive('bulletList'));
        toolbar.querySelector('[data-action="orderedList"]')
            ?.classList.toggle('btn-primary', editor.isActive('orderedList'));

        toolbar.querySelector('[data-action="bold"]')
            ?.classList.toggle('btn-primary', editor.isActive('bold'));
        toolbar.querySelector('[data-action="italic"]')
            ?.classList.toggle('btn-primary', editor.isActive('italic'));
        toolbar.querySelector('[data-action="underline"]')
            ?.classList.toggle('btn-primary', editor.isActive('underline'));
        toolbar.querySelector('[data-action="strike"]')
            ?.classList.toggle('btn-primary', editor.isActive('strike'));
        toolbar.querySelector('[data-action="link"]')
            ?.classList.toggle('btn-primary', editor.isActive('link'));


        toolbar.querySelector('[data-action="alignLeft"]')
            ?.classList.toggle('btn-primary', editor.isActive({ textAlign: 'left' }));
        toolbar.querySelector('[data-action="alignCenter"]')
            ?.classList.toggle('btn-primary', editor.isActive({ textAlign: 'center' }));
        toolbar.querySelector('[data-action="alignRight"]')
            ?.classList.toggle('btn-primary', editor.isActive({ textAlign: 'right' }));
        toolbar.querySelector('[data-action="alignJustify"]')
            ?.classList.toggle('btn-primary', editor.isActive({ textAlign: 'justify' }));
    }

    editor.on('selectionUpdate', updateToolbar);
    editor.on('transaction', updateToolbar);

    headingBtn.addEventListener('click', () => {
        headingMenu.classList.toggle('hidden')
    })

    listBtn.addEventListener('click', () => {
        listMenu.classList.toggle('hidden')
    })

    headingMenu.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', () => {
            const level = parseInt(btn.dataset.level)
            editor.chain().focus().toggleHeading({ level }).run()
            headingMenu.classList.add('hidden')
        })
    })

    listMenu.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', () => {
            const type = btn.dataset.type
            editor.chain().focus()
            if (type === 'bullet') editor.chain().toggleBulletList().run()
            if (type === 'ordered') editor.chain().toggleOrderedList().run()
            listMenu.classList.add('hidden') // ferme le menu après sélection
        })
    })

    console.log('✅ Tiptap ready with toolbar');
})
