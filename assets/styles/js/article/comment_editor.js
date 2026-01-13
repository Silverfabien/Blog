import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'

document.addEventListener('DOMContentLoaded', () => {
    const editorEl = document.querySelector('#comment-editor')
    const textarea = document.querySelector('[data-comment-textarea]')
    const toolbar = document.querySelector('#comment-toolbar')

    if (!editorEl || !textarea || !toolbar) return

    const editor = new Editor({
        element: editorEl,
        extensions: [
            StarterKit.configure({
                heading: false,
                bulletList: false,
                orderedList: false,
                listItem: false,
                image: false,
                horizontalRule: false,
            }),
            Underline,
        ],
        content: '<p></p>',
        onUpdate({ editor }) {
            textarea.value = editor.getHTML()
        },
    })

    toolbar.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action]')
        if (!btn) return

        editor.chain().focus()

        switch (btn.dataset.action) {
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
            case 'blockquote':
                editor.chain().toggleBlockquote().run()
                break
            case 'codeBlock':
                editor.chain().toggleCodeBlock().run()
                break
            case 'code':
                editor.chain().toggleCode().run()
                break
        }
    })
})
