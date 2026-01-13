import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import Image from '@tiptap/extension-image'
import TextAlign from '@tiptap/extension-text-align'
import Link from '@tiptap/extension-link'
import { TextStyle } from '@tiptap/extension-text-style'
import { Color } from '@tiptap/extension-color'
import { NodeSelection } from '@tiptap/pm/state'

import '../../css/article/form.css'

document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.querySelector('#article_content')
    const editorEl = document.querySelector('#editor')
    const toolbar = document.querySelector('#editor-toolbar')
    const imageToolbar = document.querySelector('#image-toolbar')
    const headingBtn = document.querySelector('#heading-btn')
    const headingMenu = document.querySelector('#heading-menu')
    const listBtn = document.querySelector('#list-btn')
    const listMenu = document.querySelector('#list-menu')
    const linkBtn = toolbar.querySelector('[data-action="link"]')
    const linkPopover = document.querySelector('#link-popover')
    const linkInput = document.querySelector('#link-input')
    const linkApply = document.querySelector('#link-apply')
    const linkRemove = document.querySelector('#link-remove')

    if (!textarea || !editorEl || !toolbar || !imageToolbar) return

    const CustomImage = Image.extend({
        addAttributes() {
            return {
                ...this.parent?.(),

                align: {
                    default: 'center',
                    parseHTML: element =>
                        element.getAttribute('data-align') || 'center',
                    renderHTML: attributes => {
                        return {
                            'data-align': attributes.align,
                            class: `img-align-${attributes.align}`,
                        }
                    },
                },

                width: {
                    default: null,
                    parseHTML: element => element.getAttribute('width'),
                    renderHTML: attributes => {
                        return { width: attributes.width }
                    },
                },
            }
        },

        renderHTML({ HTMLAttributes }) {
            return ['img', HTMLAttributes]
        },
    })

    const editor = new Editor({
        element: editorEl,
        extensions: [
            StarterKit.configure({ link: false }),
            TextStyle,
            Color,
            CustomImage.configure({
                inline: false,
                draggable: true,
                selectable: true
            }),
            TextAlign.configure({
                types: ['heading', 'paragraph', 'blockquote'],
            }),
            Link.configure({
                openOnClick: false,
                autolink: true,
                linkOnPaste: true,
            }),
        ],
        content: textarea.value || '<p></p>',
        onUpdate({ editor }) {
            textarea.value = editor.getHTML()
        },
    })

    toolbar.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action]')
        if (!btn) return

        const action = btn.dataset.action
        editor.chain().focus()

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

            case 'link':
                break

            case 'alignLeft':
                editor.chain().setTextAlign('left').run()
                break
            case 'alignCenter':
                editor.chain().setTextAlign('center').run()
                break
            case 'alignRight':
                editor.chain().setTextAlign('right').run()
                break
            case 'alignJustify':
                editor.chain().setTextAlign('justify').run()
                break

            case 'codeBlock':
                editor.chain().toggleCodeBlock().run()
                break
            case 'blockquote':
                editor.chain().toggleBlockquote().run()
                break
            case 'code':
                editor.chain().toggleCode().run()
                break
            case 'horizontalRule':
                editor.chain().setHorizontalRule().run()
                break

            case 'image': {
                const url = prompt('URL de l’image')
                if (url) {
                    editor.chain().setImage({ src: url }).run()
                }
                break
            }
        }
    })

    document.addEventListener('click', () => {
        headingMenu?.classList.add('hidden')
        listMenu?.classList.add('hidden')
    })

    headingBtn.addEventListener('click', (e) => {
        e.preventDefault()
        e.stopPropagation()
        headingMenu.classList.toggle('hidden')
    })

    headingMenu.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault()
            e.stopPropagation()

            const level = Number(btn.dataset.level)
            editor.chain().focus().toggleHeading({ level }).run()
            headingMenu.classList.add('hidden')
        })
    })

    if (listBtn && listMenu) {
        listBtn.addEventListener('click', (e) => {
            e.preventDefault()
            e.stopPropagation()
            listMenu.classList.toggle('hidden')
        })

        listMenu.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault()
                e.stopPropagation()

                const type = btn.dataset.type

                editor.chain().focus()

                if (type === 'bullet') {
                    editor.chain().toggleBulletList().run()
                }

                if (type === 'ordered') {
                    editor.chain().toggleOrderedList().run()
                }

                listMenu.classList.add('hidden')
            })
        })
    }

    if (linkBtn && linkPopover && linkInput) {
        linkBtn.addEventListener('click', (e) => {
            e.preventDefault()
            e.stopPropagation()

            const href = editor.getAttributes('link').href || ''
            linkInput.value = href
            linkPopover.classList.toggle('hidden')
            linkInput.focus()
        })

        linkApply.addEventListener('click', (e) => {
            e.preventDefault()

            const url = linkInput.value.trim()
            if (!url) return

            editor
                .chain()
                .focus()
                .extendMarkRange('link')
                .setLink({ href: url })
                .run()

            linkPopover.classList.add('hidden')
        })

        linkRemove.addEventListener('click', (e) => {
            e.preventDefault()

            editor.chain().focus().unsetLink().run()
            linkPopover.classList.add('hidden')
        })

        document.addEventListener('click', (e) => {
            if (
                !linkPopover.contains(e.target) &&
                !linkBtn.contains(e.target)
            ) {
                linkPopover.classList.add('hidden')
            }
        })
    }

    let imageResizeHandle = null
    let activeImage = null
    let activeImagePos = null

    editor.on('selectionUpdate', ({ editor }) => {
        const { selection } = editor.state

        if (imageResizeHandle) {
            imageResizeHandle.style.display = 'none'
            activeImage = null
        }

        // 👉 on continue seulement si c’est une NodeSelection
        if (!(selection instanceof NodeSelection)) {
            activeImagePos = null
            imageToolbar.classList.add('hidden')
            return
        }

        const node = selection.node
        if (selection.node.type.name !== 'image') {
            activeImagePos = null
            imageToolbar.classList.add('hidden')
            return
        }

        activeImagePos = selection.from

        const dom = editor.view.nodeDOM(selection.from)
        if (!dom || dom.nodeName !== 'IMG') return

        activeImage = dom

        if (!imageResizeHandle) {
            imageResizeHandle = document.createElement('div')
            imageResizeHandle.className = 'image-resize-handle'
            document.body.appendChild(imageResizeHandle)

            enableImageResize(imageResizeHandle, editor)
        }

        const rect = dom.getBoundingClientRect()

        imageResizeHandle.style.left =
            `${window.scrollX + rect.right - 7}px`

        imageResizeHandle.style.top =
            `${window.scrollY + rect.top + rect.height / 2 - 7}px`

        imageResizeHandle.style.display = 'block'

        imageToolbar.style.top = `${window.scrollY + rect.top - 42}px`
        imageToolbar.style.left = `${window.scrollX + rect.left + rect.width / 2}px`
        imageToolbar.style.transform = 'translateX(-50%)'
        imageToolbar.classList.remove('hidden')
        document.body.appendChild(imageToolbar)
    })

    function enableImageResize(handle, editor) {
        let startX = 0
        let startWidth = 0

        handle.addEventListener('mousedown', (e) => {
            if (!activeImage) return

            e.preventDefault()
            e.stopPropagation()

            startX = e.clientX
            startWidth = activeImage.getBoundingClientRect().width

            document.addEventListener('mousemove', onMove)
            document.addEventListener('mouseup', onUp)
        })

        function onMove(e) {
            const diff = e.clientX - startX
            const newWidth = Math.max(120, startWidth + diff)

            activeImage.style.width = `${newWidth}px`
        }

        function onUp() {
            document.removeEventListener('mousemove', onMove)
            document.removeEventListener('mouseup', onUp)

            if (!activeImage) return

            editor
                .chain()
                .focus()
                .updateAttributes('image', {
                    width: activeImage.style.width,
                })
                .run()
        }
    }

    imageToolbar.addEventListener('click', (e) => {
        e.preventDefault()
        e.stopPropagation()

        const btn = e.target.closest('button')
        if (!btn || activeImagePos === null) return

        editor.commands.setNodeSelection(activeImagePos)

        switch (btn.dataset.action) {
            case 'image-align-left':
                editor.chain().focus().updateAttributes('image', {
                    align: 'left',
                }).run()
                break

            case 'image-align-center':
                editor.chain().focus().updateAttributes('image', {
                    align: 'center',
                }).run()
                break

            case 'image-align-right':
                editor.chain().focus().updateAttributes('image', {
                    align: 'right',
                }).run()
                break

            case 'image-delete':
                editor.chain().focus().deleteSelection().run()
                imageToolbar.classList.add('hidden')
                activeImagePos = null
                break
        }
    })

    document.addEventListener('mousedown', (e) => {
        if (
            imageToolbar.contains(e.target) ||
            editor.view.dom.contains(e.target)
        ) {
            return
        }

        imageToolbar.classList.add('hidden')
    })

    editorEl.addEventListener('dragover', e => e.preventDefault())

    editorEl.addEventListener('drop', async (e) => {
        e.preventDefault()

        const file = e.dataTransfer.files[0]
        if (!file || !file.type.startsWith('image/')) return

        const { url } = await uploadImage(file)

        editor.chain().focus().setImage({ src: url }).run()
    })

    async function uploadImage(file) {
        const formData = new FormData()
        formData.append('file', file)

        const response = await fetch('/article/upload/picture', {
            method: 'POST',
            body: formData,
        })

        if (!response.ok) {
            throw new Error('Upload failed')
        }

        return await response.json()
    }
})
